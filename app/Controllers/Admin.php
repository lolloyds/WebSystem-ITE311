<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class Admin extends BaseController
{
    public function dashboard()
    {
        $session = session();
        if (! $session->get('isAuthenticated') || (string) $session->get('userRole') !== 'admin') {
            return redirect()->to(base_url('login'));
        }

        $db = Database::connect();

        $totalUsers = (int) $db->table('users')->countAllResults();
        $totalCourses = (int) $db->table('courses')->countAllResults();

        $recentUsers = $db->table('users')
            ->select('id, name, email, role, created_at')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('admin/dashboard', [
            'totalUsers' => $totalUsers,
            'totalCourses' => $totalCourses,
            'recentUsers' => $recentUsers,
        ]);
    }
}


