<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Learning Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <style>
    /* Custom navbar gradient */
    .navbar-custom {
      background: linear-gradient(90deg, #1e3a8a, #1e40af);
    }
    .navbar-brand {
      font-size: 1.5rem;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
    <div class="container-fluid">
      <!-- Brand -->
      <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">
        Learning Management System
      </a>

      <!-- Mobile Toggle -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar Links -->
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('about') ?>">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('contact') ?>">Contact</a>
          </li>

          <?php if (session()->get('isAuthenticated')): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('announcements') ?>">Announcements</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('dashboard') ?>">Dashboard</a>
            </li>
          <?php endif; ?>

          <?php if (session()->get('isAuthenticated')): ?>
            <?php $role = (string) session()->get('userRole'); ?>
            <?php if ($role === 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Admin</a>
              </li>
            <?php elseif ($role === 'teacher'): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= base_url('teacher/dashboard') ?>">Teacher</a>
              </li>
            <?php endif; ?>
          <?php endif; ?>

          <li class="nav-item">
            <?php if (session()->get('isAuthenticated')): ?>
              <a class="nav-link" href="<?= base_url('logout') ?>">Logout</a>
            <?php else: ?>
              <a class="nav-link" href="<?= base_url('login') ?>">Login</a>
            <?php endif; ?>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main content -->
  <?= $this->renderSection('content') ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
