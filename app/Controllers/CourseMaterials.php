<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class CourseMaterials extends BaseController
{
    protected $materialModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    /**
     * Get materials list for a course (JSON for AJAX or HTML for direct access)
     *
     * @param int $courseId Course ID
     * @return ResponseInterface
     */
    public function index($courseId): ResponseInterface
    {
        // Check authentication
        if (!session()->get('isAuthenticated')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Authentication required'
            ])->setStatusCode(401);
        }

        // Check if course exists and user has access
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
        if (!$course) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Course not found'
            ])->setStatusCode(404);
        }

        $userId = session()->get('userId');
        $userRole = session()->get('userRole');

        // Check permissions: teachers can access their courses, students must be enrolled
        if ($userRole === 'teacher' && $course['teacher_id'] != $userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        if ($userRole === 'student' && !$this->enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        // Get materials with uploader info
        $materials = $this->materialModel->getMaterialsByCourseWithUser($courseId);

        // Return JSON response
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $materials
        ]);
    }

    /**
     * Upload material for a course
     *
     * @param int $courseId Course ID
     * @return ResponseInterface
     */
    public function upload($courseId): ResponseInterface
    {
        // Check authentication and role
        if (!session()->get('isAuthenticated') || !in_array(session()->get('userRole'), ['admin', 'teacher'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        // Check if course exists and teacher owns it
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
        if (!$course) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Course not found'
            ])->setStatusCode(404);
        }

        $userId = session()->get('userId');
        $userRole = session()->get('userRole');

        if ($userRole === 'teacher' && $course['teacher_id'] != $userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        // Get uploaded file
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No valid file uploaded'
            ])->setStatusCode(400);
        }

        // Validate file type
        $allowedMimes = [
            'application/pdf',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        $mimeType = $file->getClientMimeType();
        if (!in_array($mimeType, $allowedMimes)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid file type. Only PDF, PPT, PPTX, DOC, DOCX allowed.'
            ])->setStatusCode(400);
        }

        // Validate file size (10MB max)
        if ($file->getSize() > 10 * 1024 * 1024) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File size exceeds 10MB limit'
            ])->setStatusCode(400);
        }

        // Generate unique filename
        $originalName = $file->getClientName();
        $storedName = $file->getRandomName();

        // Create directory if it doesn't exist
        $uploadPath = WRITEPATH . 'uploads/materials/' . $courseId . '/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Move file
        if (!$file->move($uploadPath, $storedName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save file'
            ])->setStatusCode(500);
        }

        // Save to database
        $materialData = [
            'course_id' => $courseId,
            'file_name_original' => $originalName,
            'file_name_stored' => $storedName,
            'file_path' => 'uploads/materials/' . $courseId . '/' . $storedName,
            'description' => $this->request->getPost('description') ?? '',
            'uploaded_by' => $userId,
            'uploaded_at' => date('Y-m-d H:i:s')
        ];

        $materialId = $this->materialModel->insertMaterial($materialData);
        if (!$materialId) {
            // Clean up file if DB insert fails
            unlink($uploadPath . $storedName);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save material information'
            ])->setStatusCode(500);
        }

        // Get material with user info for response
        $material = $this->materialModel->getMaterialsByCourseWithUser($courseId);
        $newMaterial = array_filter($material, function($m) use ($materialId) {
            return $m['id'] == $materialId;
        });
        $newMaterial = reset($newMaterial);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Material uploaded successfully',
            'data' => $newMaterial
        ]);
    }

    /**
     * Delete a material
     *
     * @param int $materialId Material ID
     * @return ResponseInterface
     */
    public function delete($materialId): ResponseInterface
    {
        // Check authentication and role
        if (!session()->get('isAuthenticated') || !in_array(session()->get('userRole'), ['admin', 'teacher'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        // Get material
        $material = $this->materialModel->getMaterialById($materialId);
        if (!$material) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Material not found'
            ])->setStatusCode(404);
        }

        $userId = session()->get('userId');
        $userRole = session()->get('userRole');

        // Check ownership (only uploader or admin can delete)
        if ($userRole !== 'admin' && $material['uploaded_by'] != $userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        // Delete file from filesystem
        $filePath = WRITEPATH . $material['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        if (!$this->materialModel->deleteMaterial($materialId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete material'
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Material deleted successfully'
        ]);
    }

    /**
     * View a material file (inline preview)
     *
     * @param int $materialId Material ID
     * @return ResponseInterface
     */
    public function view($materialId): ResponseInterface
    {
        // Check authentication
        if (!session()->get('isAuthenticated')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Authentication required'
            ])->setStatusCode(401);
        }

        // Get material
        $material = $this->materialModel->getMaterialById($materialId);
        if (!$material) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Material not found'
            ])->setStatusCode(404);
        }

        $userId = session()->get('userId');
        $userRole = session()->get('userRole');

        // Check permissions: enrolled students or teachers/admins
        if ($userRole !== 'admin') {
            $db = \Config\Database::connect();
            $course = $db->table('courses')->where('id', $material['course_id'])->get()->getRowArray();

            if ($userRole === 'teacher' && $course['teacher_id'] != $userId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied'
                ])->setStatusCode(403);
            }

            if ($userRole === 'student' && !$this->enrollmentModel->isAlreadyEnrolled($userId, $material['course_id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied'
                ])->setStatusCode(403);
            }
        }

        // Get file path
        $filePath = WRITEPATH . $material['file_path'];
        if (!file_exists($filePath)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File not found'
            ])->setStatusCode(404);
        }

        // Determine MIME type and file extension
        $fileExtension = strtolower(pathinfo($material['file_name_original'], PATHINFO_EXTENSION));
        $mimeType = mime_content_type($filePath);

        // For supported preview formats, serve inline
        if (in_array($fileExtension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
            $this->response->setHeader('Content-Type', $mimeType);
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . $material['file_name_original'] . '"');
            $this->response->setHeader('Content-Length', filesize($filePath));
            $this->response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
            $this->response->setHeader('Pragma', 'public');

            return $this->response->setBody(file_get_contents($filePath));
        }

        // For text files, serve with text content type
        if ($fileExtension === 'txt') {
            $this->response->setHeader('Content-Type', 'text/plain; charset=UTF-8');
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . $material['file_name_original'] . '"');
            $this->response->setHeader('Content-Length', filesize($filePath));

            return $this->response->setBody(file_get_contents($filePath));
        }

        // For unsupported formats, redirect to preview page
        return redirect()->to("/course/material-preview/{$materialId}");
    }

    /**
     * Download a material file
     *
     * @param int $materialId Material ID
     * @return ResponseInterface
     */
    public function download($materialId): ResponseInterface
    {
        // Check authentication
        if (!session()->get('isAuthenticated')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Authentication required'
            ])->setStatusCode(401);
        }

        // Get material
        $material = $this->materialModel->getMaterialById($materialId);
        if (!$material) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Material not found'
            ])->setStatusCode(404);
        }

        $userId = session()->get('userId');
        $userRole = session()->get('userRole');

        // Check permissions: enrolled students or teachers/admins
        if ($userRole !== 'admin') {
            $db = \Config\Database::connect();
            $course = $db->table('courses')->where('id', $material['course_id'])->get()->getRowArray();

            if ($userRole === 'teacher' && $course['teacher_id'] != $userId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied'
                ])->setStatusCode(403);
            }

            if ($userRole === 'student' && !$this->enrollmentModel->isAlreadyEnrolled($userId, $material['course_id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied'
                ])->setStatusCode(403);
            }
        }

        // Get file path
        $filePath = WRITEPATH . $material['file_path'];
        if (!file_exists($filePath)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File not found'
            ])->setStatusCode(404);
        }

        // Force download with proper headers
        return $this->response->download($filePath, null, true)->setFileName($material['file_name_original']);
    }

    /**
     * Show material preview page
     *
     * @param int $materialId Material ID
     * @return string|ResponseInterface
     */
    public function preview($materialId)
    {
        // Check authentication
        if (!session()->get('isAuthenticated')) {
            return redirect()->to('/login')->with('error', 'Authentication required');
        }

        // Get material with uploader info
        $material = $this->materialModel->getMaterialById($materialId);
        if (!$material) {
            return redirect()->back()->with('error', 'Material not found');
        }

        $userId = session()->get('userId');
        $userRole = session()->get('userRole');

        // Check permissions: enrolled students or teachers/admins
        if ($userRole !== 'admin') {
            $db = \Config\Database::connect();
            $course = $db->table('courses')->where('id', $material['course_id'])->get()->getRowArray();

            if ($userRole === 'teacher' && $course['teacher_id'] != $userId) {
                return redirect()->back()->with('error', 'Access denied');
            }

            if ($userRole === 'student' && !$this->enrollmentModel->isAlreadyEnrolled($userId, $material['course_id'])) {
                return redirect()->back()->with('error', 'Access denied');
            }
        }

        // Get material with uploader name
        $materialWithUser = $this->materialModel->select('materials.*, users.username as uploader_name')
                                               ->join('users', 'users.id = materials.uploaded_by', 'left')
                                               ->where('materials.id', $materialId)
                                               ->get()
                                               ->getRowArray();

        if (!$materialWithUser) {
            return redirect()->back()->with('error', 'Material not found');
        }

        return view('course/material_preview', ['material' => $materialWithUser]);
    }
}
