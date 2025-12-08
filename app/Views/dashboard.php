<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0 text-light">Dashboard</h1>
    <a href="<?= base_url('logout') ?>" class="btn btn-outline-primary">Logout</a>
</div>

<div class="alert alert-success" role="alert">
    Welcome, <?= esc(session('userName')) ?>!
</div>

<?php
$userRole = session()->get('userRole');
$userId = session()->get('userId');

if ($userRole === 'student'):
    // Load enrolled courses for students
    $db = \Config\Database::connect();
    $enrollments = $db->table('enrollments')
                     ->select('courses.title, courses.description, enrollments.enrolled_at')
                     ->join('courses', 'courses.id = enrollments.course_id')
                     ->where('enrollments.user_id', $userId)
                     ->orderBy('enrollments.enrolled_at', 'DESC')
                     ->get()
                     ->getResultArray();
?>
    <div class="card shadow-sm border-0 bg-dark text-light mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>My Enrolled Courses</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($enrollments)): ?>
                <div class="row">
                    <?php foreach ($enrollments as $enrollment): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 bg-secondary text-light">
                                <div class="card-body">
                                    <h6 class="card-title"><?= esc($enrollment['title']) ?></h6>
                                    <p class="card-text small"><?= esc($enrollment['description']) ?></p>
                                    <small class="text-muted">Enrolled: <?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">You haven't enrolled in any courses yet. <a href="<?= base_url('courses') ?>" class="text-primary">Browse available courses</a></p>
            <?php endif; ?>
        </div>
    </div>

<?php elseif ($userRole === 'teacher'):
    // Load courses created by teacher
    $db = \Config\Database::connect();
    $courses = $db->table('courses')
                 ->where('teacher_id', $userId)
                 ->orderBy('created_at', 'DESC')
                 ->get()
                 ->getResultArray();
?>
    <div class="card shadow-sm border-0 bg-dark text-light mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-book me-2"></i>My Courses</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($courses)): ?>
                <div class="row">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 bg-secondary text-light">
                                <div class="card-body">
                                    <h6 class="card-title"><?= esc($course['title']) ?></h6>
                                    <p class="card-text small"><?= esc($course['description']) ?></p>
                                    <small class="text-muted">Created: <?= date('M d, Y', strtotime($course['created_at'])) ?></small>
                                </div>
                                <div class="card-footer">
                                    <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-primary btn-sm">Upload Material</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">You haven't created any courses yet.</p>
            <?php endif; ?>
        </div>
    </div>

<?php else: ?>
    <div class="card shadow-sm border-0 bg-dark text-light">
        <div class="card-body">
            <p class="mb-0">This is a protected page only visible after login.</p>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
