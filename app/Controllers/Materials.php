<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Materials extends BaseController
{
    protected $materialModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    /**
     * Display upload form and handle file upload for a course
     *
     * @param int $course_id Course ID
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function upload($course_id)
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isAuthenticated')) {
            session()->setFlashdata('error', 'You must be logged in to upload materials.');
            return redirect()->to('/login');
        }

        // Check if user is admin or teacher
        $userRole = $session->get('userRole');
        if (!in_array($userRole, ['admin', 'teacher'])) {
            session()->setFlashdata('error', 'Access denied: Insufficient permissions.');
            return redirect()->to('/announcements');
        }

        // Verify the course exists and belongs to the teacher (if teacher)
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $course_id)->get()->getRowArray();
        if (!$course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Course not found');
        }

        if ($userRole === 'teacher' && $course['teacher_id'] != $session->get('userId')) {
            session()->setFlashdata('error', 'Access denied: You can only upload materials to your own courses.');
            return redirect()->to('/teacher/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->handleUpload($course_id);
        }

        $data = [
            'course' => $course,
            'title' => 'Upload Material for ' . $course['title']
        ];

        return view('materials/upload', $data);
    }

    /**
     * Handle the file upload process
     *
     * @param int $course_id Course ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    private function handleUpload($course_id)
    {
        // Load validation library
        $validation = \Config\Services::validation();

        // Set validation rules
        $validation->setRules([
            'material' => [
                'label' => 'File',
                'rules' => 'uploaded[material]|max_size[material,10240]|ext_in[material,pdf,ppt,pptx]',
            ],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            session()->setFlashdata('error', $validation->getError('material'));
            return redirect()->back();
        }

        // Handle file upload
        $file = $this->request->getFile('material');

        if (!$file->isValid()) {
            session()->setFlashdata('error', 'Invalid file upload.');
            return redirect()->back();
        }

        // Generate unique filename
        $newName = $file->getRandomName();

        // Move file to writable uploads directory
        $uploadPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'materials' . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if (!$file->move($uploadPath, $newName)) {
            session()->setFlashdata('error', 'Failed to upload file.');
            return redirect()->back();
        }

        // Save material data to database
        $materialData = [
            'course_id' => $course_id,
            'file_name' => $file->getClientName(),
            'file_path' => 'writable/uploads/materials/' . $newName,
        ];

        if ($this->materialModel->insertMaterial($materialData)) {
            session()->setFlashdata('success', 'Material uploaded successfully.');
        } else {
            // If database save fails, delete the uploaded file to maintain consistency
            $uploadedFilePath = $uploadPath . $newName;
            if (file_exists($uploadedFilePath)) {
                unlink($uploadedFilePath);
            }
            session()->setFlashdata('error', 'Failed to save material information.');
        }

        return redirect()->back();
    }

    /**
     * Delete a material
     *
     * @param int $material_id Material ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($material_id)
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isAuthenticated')) {
            session()->setFlashdata('error', 'You must be logged in to delete materials.');
            return redirect()->to('/login');
        }

        // Check if user is admin or teacher
        $userRole = $session->get('userRole');
        if (!in_array($userRole, ['admin', 'teacher'])) {
            session()->setFlashdata('error', 'Access denied: Insufficient permissions.');
            return redirect()->to('/announcements');
        }

        // Get material details
        $material = $this->materialModel->getMaterialById($material_id);
        if (!$material) {
            session()->setFlashdata('error', 'Material not found.');
            return redirect()->to('/announcements');
        }

        // Get course details
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $material['course_id'])->get()->getRowArray();

        // Check permissions - only teachers need to check ownership
        if ($userRole === 'teacher' && $course['teacher_id'] != $session->get('userId')) {
            session()->setFlashdata('error', 'Access denied: You can only delete materials from your own courses.');
            return redirect()->to('/teacher/dashboard');
        }
        
        // Admins can delete materials from any course

        // Delete file from filesystem
        if (strpos($material['file_path'], 'writable/') === 0) {
            $filePath = WRITEPATH . str_replace('writable/', '', $material['file_path']);
        } else {
            $filePath = FCPATH . $material['file_path'];
        }
        
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        if ($this->materialModel->deleteMaterial($material_id)) {
            session()->setFlashdata('success', 'Material deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to delete material.');
        }

        // Redirect back to appropriate dashboard
        if ($userRole === 'admin') {
            return redirect()->to('/admin/dashboard');
        } else {
            return redirect()->to('/teacher/dashboard');
        }
    }

    /**
     * Download a material file
     *
     * @param int $material_id Material ID
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function download($material_id)
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isAuthenticated')) {
            session()->setFlashdata('error', 'You must be logged in to download materials.');
            return redirect()->to('/login');
        }

        // Get material details
        $material = $this->materialModel->getMaterialById($material_id);
        if (!$material) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material not found');
        }

        // Check if user is enrolled in the course (admins can download without enrollment)
        $user_id = $session->get('userId');
        $userRole = $session->get('userRole');
        
        // Admins can download materials without being enrolled
        if ($userRole !== 'admin') {
            $isEnrolled = $this->enrollmentModel->isAlreadyEnrolled($user_id, $material['course_id']);

            if (!$isEnrolled) {
                session()->setFlashdata('error', 'Access denied: You must be enrolled in the course to download materials.');
                return redirect()->to('/announcements');
            }
        }

        // Get file path - use WRITEPATH for writable directory
        if (strpos($material['file_path'], 'writable/') === 0) {
            $filePath = WRITEPATH . str_replace('writable/', '', $material['file_path']);
        } else {
            $filePath = FCPATH . $material['file_path'];
        }

        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        // Force download
        return $this->response->download($filePath, null, true)->setFileName($material['file_name']);
    }

    /**
     * View a material file in the browser
     *
     * @param int $material_id Material ID
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function viewFile($material_id)
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isAuthenticated')) {
            session()->setFlashdata('error', 'You must be logged in to view materials.');
            return redirect()->to('/login');
        }

        // Get material details
        $material = $this->materialModel->getMaterialById($material_id);
        if (!$material) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material not found');
        }

        // Check if user is enrolled in the course (admins can view without enrollment)
        $user_id = $session->get('userId');
        $userRole = $session->get('userRole');
        
        // Admins can view materials without being enrolled
        if ($userRole !== 'admin') {
            $isEnrolled = $this->enrollmentModel->isAlreadyEnrolled($user_id, $material['course_id']);

            if (!$isEnrolled) {
                session()->setFlashdata('error', 'Access denied: You must be enrolled in the course to view materials.');
                return redirect()->to('/announcements');
            }
        }

        // Get file path - use WRITEPATH for writable directory
        if (strpos($material['file_path'], 'writable/') === 0) {
            $filePath = WRITEPATH . str_replace('writable/', '', $material['file_path']);
        } else {
            $filePath = FCPATH . $material['file_path'];
        }

        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        // Get file extension to determine if it can be viewed in browser
        $ext = strtolower(pathinfo($material['file_name'], PATHINFO_EXTENSION));
        $viewableExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt', 'html', 'htm', 'xml'];

        if (!in_array($ext, $viewableExtensions)) {
            // If file cannot be viewed in browser, redirect to download
            return redirect()->to('materials/download/' . $material_id);
        }

        // Set appropriate content type for viewing
        $contentType = $this->getContentType($ext);

        // Read file contents
        $fileContent = file_get_contents($filePath);

        // Return file for inline viewing - explicitly set Content-Disposition to inline
        return $this->response
            ->setContentType($contentType)
            ->setHeader('Content-Disposition', 'inline')
            ->setHeader('Content-Length', (string) filesize($filePath))
            ->setBody($fileContent);
    }

    /**
     * Get content type based on file extension
     *
     * @param string $ext File extension
     * @return string Content type
     */
    private function getContentType($ext)
    {
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'htm' => 'text/html',
            'xml' => 'application/xml',
        ];

        return $contentTypes[$ext] ?? 'application/octet-stream';
    }
}
