<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ITE311-DORAIDO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* Custom navbar gradient */
    .navbar-custom {
      background: linear-gradient(90deg, #94acd0ff, #071d3dff);
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
    <div class="container-fluid">
      <!-- Brand -->
      <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= base_url('/') ?>">
        <i class="bi bi-mortarboard-fill me-2 text-warning"></i> ITE311-DORAIDO
      </a>

      <!-- Mobile Toggle -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar Links -->
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/') ?>"><i class="bi bi-house-door-fill me-1"></i> Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('about') ?>"><i class="bi bi-info-circle-fill me-1"></i> About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('contact') ?>"><i class="bi bi-envelope-fill me-1"></i> Contact</a>
          </li>

          <?php if (session()->get('isAuthenticated')): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('dashboard') ?>"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
            </li>
          <?php endif; ?>

          <?php if (session()->get('isAuthenticated')): ?>
            <?php $role = (string) session()->get('userRole'); ?>
            <?php if ($role === 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-shield-lock-fill me-1"></i> Admin</a>
              </li>
            <?php endif; ?>
          <?php endif; ?>

          <li class="nav-item ms-lg-3">
            <?php if (session()->get('isAuthenticated')): ?>
              <a class="btn btn-sm btn-light text-primary fw-semibold" href="<?= base_url('logout') ?>">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
              </a>
            <?php else: ?>
              <a class="btn btn-sm btn-warning fw-semibold" href="<?= base_url('login') ?>">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login
              </a>
            <?php endif; ?>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main content -->
  <div class="container mt-4">
    <?= $this->renderSection('content') ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
