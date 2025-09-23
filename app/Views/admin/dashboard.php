<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3 mb-0">Admin Dashboard</h1>
  <div>
    <span class="badge bg-primary">Total Users: <?= esc($totalUsers) ?></span>
    <span class="badge bg-success ms-2">Total Courses: <?= esc($totalCourses) ?></span>
  </div>
  </div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">Manage</div>
      <div class="card-body">
        <a href="#" class="btn btn-outline-primary btn-sm">Users</a>
        <a href="#" class="btn btn-outline-secondary btn-sm ms-2">Courses</a>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header">Recent Activity</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentUsers as $u): ?>
              <tr>
                <td><?= esc($u['id']) ?></td>
                <td><?= esc($u['name']) ?></td>
                <td><?= esc($u['email']) ?></td>
                <td><span class="badge bg-dark text-capitalize"><?= esc($u['role']) ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>


