<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is authenticated
        if (!session()->get('isAuthenticated')) {
            session()->setFlashdata('error', 'Authentication required to access this area.');
            return redirect()->to('/login');
        }

        // Get user role and current URI
        $userRole = session()->get('userRole');
        $currentURI = $request->getUri()->getPath();

        // Define role-based access rules
        if ($userRole === 'admin') {
            // Admin can access any route starting with /admin, plus general routes, and materials routes
            if (str_starts_with($currentURI, '/admin') ||
                str_starts_with($currentURI, '/materials')) {
                // Allow admin routes and materials routes
                return;
            } elseif (in_array($currentURI, ['/', '/about', '/contact', '/announcements', '/dashboard', '/settings', '/login', '/register', '/logout'])) {
                // Allow general routes
                return;
            } else {
                session()->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to('/announcements');
            }
        } elseif ($userRole === 'teacher') {
            // Teacher can access /teacher routes, general routes, and materials routes
            if (str_starts_with($currentURI, '/teacher') ||
                str_starts_with($currentURI, '/materials')) {
                // Allow teacher routes and materials routes
                return;
            } elseif (in_array($currentURI, ['/', '/about', '/contact', '/announcements', '/dashboard', '/settings', '/login', '/register', '/logout'])) {
                // Allow general routes
                return;
            } else {
                session()->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to('/announcements');
            }
        } elseif ($userRole === 'student') {
            // Student can access /student routes, /announcements, general routes, and materials download
            if (str_starts_with($currentURI, '/student') ||
                $currentURI === '/announcements' ||
                str_starts_with($currentURI, '/materials/download')) {
                // Allow student-specific routes, announcements, and materials download
                return;
            } elseif (in_array($currentURI, ['/', '/about', '/contact', '/dashboard', '/settings', '/login', '/register', '/logout'])) {
                // Allow general routes
                return;
            } else {
                session()->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to('/announcements');
            }
        } else {
            // Unknown role - redirect to announcements
            session()->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/announcements');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
