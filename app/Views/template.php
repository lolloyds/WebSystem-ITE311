<!doctype html>
<html>
<head>
  <title>ITE311-DORAIDO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?= base_url('/') ?>">ITE311-DORAIDO</a>
      <div class="collapse navbar-collapse">
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
            <a class="nav-link" href="<?= base_url('dashboard') ?>">Dashboard</a>
          </li>
          <?php endif; ?>
          <?php if (session()->get('isAuthenticated')): ?>
          <?php $role = (string) session()->get('userRole'); ?>
          <?php if ($role === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Admin</a>
          </li>
          <?php endif; ?>
          <?php endif; ?>
          <li class="nav-item">
            <?php if (session()->get('isAuthenticated')): ?>
              <a class="btn btn-danger ms-2" href="<?= base_url('logout') ?>">Logout</a>
            <?php else: ?>
              <a class="btn btn-success ms-2" href="<?= base_url('login') ?>">Login</a>
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
