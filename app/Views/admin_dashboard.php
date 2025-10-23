<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <h2 class="card-title text-dark">Welcome admin!</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- All Courses Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Courses</h5>
                </div>
                <div class="card-body">
                    <?php
                    $db = \Config\Database::connect();
                    $courses = $db->table('courses')
                                ->select('courses.*, users.name as teacher_name')
                                ->join('users', 'users.id = courses.teacher_id')
                                ->get()
                                ->getResultArray();
                    ?>

                    <?php if (empty($courses)): ?>
                        <p class="text-muted">No courses available.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($courses as $course): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= esc($course['title']) ?></h6>
                                            <p class="card-text text-truncate"><?= esc($course['description']) ?></p>
                                            <p class="card-text"><small class="text-muted">Teacher: <?= esc($course['teacher_name']) ?></small></p>
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
