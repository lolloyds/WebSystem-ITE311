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
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-folder-fill me-2"></i> Course Materials
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    $materialModel = new \App\Models\MaterialModel();
                    $materials = $materialModel->getMaterialsByCourse($course['id']);
                    ?>

                    <?php if (empty($materials)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No materials available for this course yet. Check back later!
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($materials as $material): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="bi bi-file-earmark me-2 text-primary"></i>
                                            <?= esc($material['file_name']) ?>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Uploaded: <?= date('M d, Y H:i', strtotime($material['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="<?= site_url('materials/view/' . $material['id']) ?>" class="btn btn-success btn-sm">
                                            <i class="bi bi-eye me-1"></i> View
                                        </a>
                                        <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
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
