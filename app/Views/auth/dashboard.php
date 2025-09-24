<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<h1 class="h3 mb-4">Dashboard</h1>

<?php $role = $role ?? 'student'; ?>

<?php if ($role === 'admin'): ?>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card text-bg-primary">
      <div class="card-body">
        <div class="h1 mb-0"><?= esc($totalUsers ?? 0) ?></div>
        <div>Total Users</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-bg-success">
      <div class="card-body">
        <div class="h1 mb-0"><?= esc($totalCourses ?? 0) ?></div>
        <div>Total Courses</div>
      </div>
    </div>
  </div>
  <div class="col-md-12">
    <div class="card mt-3">
      <div class="card-header">Recent Users</div>
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
              <?php foreach (($recentUsers ?? []) as $u): ?>
              <tr>
                <td><?= esc($u['id']) ?></td>
                <td><?= esc($u['name']) ?></td>
                <td><?= esc($u['email']) ?></td>
                <td><?= esc($u['role']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php elseif ($role === 'teacher'): ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>My Courses</span>
    <a href="#" class="btn btn-sm btn-primary">Create Course</a>
  </div>
  <ul class="list-group list-group-flush">
    <?php foreach (($myCourses ?? []) as $c): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <span><?= esc($c['title']) ?></span>
      <span class="text-muted small"><?= esc($c['created_at'] ?? '') ?></span>
    </li>
    <?php endforeach; ?>
    <?php if (empty($myCourses)): ?>
    <li class="list-group-item text-muted">No courses yet.</li>
    <?php endif; ?>
  </ul>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header">Available Courses</div>
  <ul class="list-group list-group-flush">
    <?php foreach (($availableCourses ?? []) as $c): ?>
    <li class="list-group-item">
      <?= esc($c['title']) ?>
    </li>
    <?php endforeach; ?>
    <?php if (empty($availableCourses)): ?>
    <li class="list-group-item text-muted">No courses available.</li>
    <?php endif; ?>
  </ul>
</div>
<?php endif; ?>

<?= $this->endSection() ?>


