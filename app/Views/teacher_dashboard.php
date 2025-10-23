<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <h2 class="card-title text-dark">Welcome Teacher!</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Your Courses</h5>
                </div>
                <div class="card-body">
                    <?php
                    $db = \Config\Database::connect();
                    $courses = $db->table('courses')
                                ->where('teacher_id', session()->get('userId'))
                                ->get()
                                ->getResultArray();
                    ?>

                    <?php if (empty($courses)): ?>
                        <p class="text-muted">You haven't created any courses yet.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($courses as $course): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= esc($course['title']) ?></h6>
                                            <p class="card-text text-truncate"><?= esc($course['description']) ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="<?= site_url('materials/upload/' . $course['id']) ?>" class="btn btn-primary btn-sm">Upload Material</a>
                                                <small class="text-muted">Created: <?= date('M d, Y', strtotime($course['created_at'])) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
