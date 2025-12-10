<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <h2 class="card-title text-dark">Welcome Teacher!</h2>
                    <p class="text-muted mb-0">Manage your courses and upload materials</p>
                </div>
            </div>
        </div>
    </div>

    <!-- My Courses Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-book me-2"></i>My Courses</h5>
                </div>
                <div class="card-body">
                    <?php
                    $userId = session()->get('userId');
                    $db = \Config\Database::connect();
                    $courses = $db->table('courses')
                                 ->where('teacher_id', $userId)
                                 ->orderBy('created_at', 'DESC')
                                 ->get()
                                 ->getResultArray();
                    ?>

                    <?php if (!empty($courses)): ?>
                        <div class="row">
                            <?php foreach ($courses as $course): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border">
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title">
                                                <i class="bi bi-book-fill text-primary me-2"></i>
                                                <?= esc($course['title']) ?>
                                            </h6>
                                            <p class="card-text text-muted small flex-grow-1">
                                                <?= esc($course['description']) ?>
                                            </p>
                                            <div class="mt-auto">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    Created: <?= date('M d, Y', strtotime($course['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <div class="d-flex gap-2">
                                                <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-primary btn-sm flex-grow-1">
                                                    <i class="bi bi-upload me-1"></i> Upload Material
                                                </a>
                                                <a href="<?= base_url('course/view/' . $course['id']) ?>" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-eye me-1"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted mt-3">No Courses Assigned</h4>
                            <p class="text-muted">You don't have any courses assigned to you yet. Contact an administrator to assign courses.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
