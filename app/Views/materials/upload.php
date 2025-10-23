<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Upload Form Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">Upload Material for: <?= esc($course['title']) ?></h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('materials/upload/' . $course['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="material" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="material" name="material" required>
                            <div class="form-text">
                                Allowed file types: PDF, DOC, DOCX, PPT, PPTX, TXT, JPG, JPEG, PNG. Maximum size: 10MB.
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= site_url(session()->get('userRole') === 'admin' ? '/admin/dashboard' : '/teacher/dashboard') ?>" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Upload Material</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Materials Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Uploaded Materials</h5>
                </div>
                <div class="card-body">
                    <?php
                    $materialModel = new \App\Models\MaterialModel();
                    $materials = $materialModel->getMaterialsByCourse($course['id']);
                    ?>

                    <?php if (empty($materials)): ?>
                        <p class="text-muted">No materials uploaded yet for this course.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($materials as $material): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-file me-2"></i>
                                            <?= esc($material['file_name']) ?>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            Uploaded: <?= date('M d, Y H:i', strtotime($material['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-success btn-sm me-2">
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                        <a href="<?= site_url('materials/delete/' . $material['id']) ?>" class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to delete this material?')">
                                            <i class="fas fa-trash me-1"></i> Delete
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
