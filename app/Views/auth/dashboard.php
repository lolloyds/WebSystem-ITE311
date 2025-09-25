<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard ⚡
    </h1>
    <div class="text-end">
        <?php 
            $userName = session()->get('userName'); 
            $userRole = ucfirst((string) session()->get('userRole')); 
            $role     = $role ?? strtolower($userRole) ?? 'student';
        ?>
        <div class="fw-semibold">Welcome, <strong><?= esc($userName) ?></strong> 👋</div>
        <div class="text-muted small">
            <i class="bi bi-person-badge me-1"></i> Role: <?= esc($userRole) ?>
        </div>
    </div>
</div>

<?php if ($role === 'admin'): ?>
    <!-- Admin Dashboard -->
    <div class="row g-3">
        <!-- Total Users -->
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px;">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0"><?= esc($totalUsers ?? 0) ?></div>
                        <small class="text-muted">Total Users 👥</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Courses -->
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px;">
                        <i class="bi bi-book-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0"><?= esc($totalCourses ?? 0) ?></div>
                        <small class="text-muted">Total Courses 📚</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users Table -->
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3 mt-3">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-clock-history me-2"></i> Recent Users 🕒
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th><i class="bi bi-person me-1"></i> Name</th>
                                    <th><i class="bi bi-envelope me-1"></i> Email</th>
                                    <th><i class="bi bi-shield-lock me-1"></i> Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentUsers)): ?>
                                    <?php foreach ($recentUsers as $u): ?>
                                        <tr>
                                            <td><?= esc($u['id']) ?></td>
                                            <td><?= esc($u['name']) ?></td>
                                            <td><?= esc($u['email']) ?></td>
                                            <td><?= esc($u['role']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recent users 😶</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($role === 'teacher'): ?>
    <!-- Teacher Dashboard -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-journal-text me-2"></i> My Courses ✏️</span>
            <a href="#" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Create Course ➕
            </a>
        </div>
        <ul class="list-group list-group-flush">
            <?php if (!empty($myCourses)): ?>
                <?php foreach ($myCourses as $c): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-book me-2 text-success"></i> <?= esc($c['title']) ?> 📖</span>
                        <span class="text-muted small"><?= esc($c['created_at'] ?? '') ?></span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-muted">No courses yet 😔</li>
            <?php endif; ?>
        </ul>
    </div>

<?php else: ?>
    <!-- Student Dashboard -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-collection-play me-2"></i> Available Courses 🎓
        </div>
        <ul class="list-group list-group-flush">
            <?php if (!empty($availableCourses)): ?>
                <?php foreach ($availableCourses as $c): ?>
                    <li class="list-group-item">
                        <i class="bi bi-bookmark-star text-warning me-2"></i> <?= esc($c['title']) ?> ⭐
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-muted">No courses available 🚫</li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
