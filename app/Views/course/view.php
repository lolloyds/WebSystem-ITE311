<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Course Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0"><?= esc($course['title']) ?></h3>
                </div>
                <div class="card-body">
                    <p class="card-text"><?= esc($course['description']) ?></p>
                    <small class="text-muted">Created: <?= date('F j, Y', strtotime($course['created_at'])) ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Materials Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Course Materials</h5>
                </div>
                <div class="card-body">
                    <?php
                    $materialModel = new \App\Models\MaterialModel();
                    $materials = $materialModel->getMaterialsByCourse($course['id']);
                    ?>

                    <?php if (empty($materials)): ?>
                        <p class="text-muted">No materials available for this course yet.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($materials as $material): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= esc($material['file_name']) ?></h6>
                                        <small class="text-muted">Uploaded: <?= date('M d, Y H:i', strtotime($material['created_at'])) ?></small>
                                    </div>
                                    <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download"></i> Download
                                    </a>
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
