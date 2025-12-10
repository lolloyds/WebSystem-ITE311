<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-file-earmark me-2"></i>
                        <?= esc($material['file_name_original']) ?>
                    </h4>
                    <div>
                        <a href="/materials/<?= $material['id'] ?>/download"
                           class="btn btn-primary me-2"
                           target="_blank">
                            <i class="bi bi-download me-1"></i>Download
                        </a>
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <?php
                            $fileExtension = strtolower(pathinfo($material['file_name_original'], PATHINFO_EXTENSION));
                            $canPreview = in_array($fileExtension, ['pdf', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                            ?>

                            <?php if ($canPreview): ?>
                                <?php if ($fileExtension === 'pdf'): ?>
                                    <div class="text-center">
                                        <iframe src="/materials/<?= $material['id'] ?>/view"
                                                style="width: 100%; height: 600px; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                                            <p>Your browser does not support iframes.
                                               <a href="/materials/<?= $material['id'] ?>/download">Download the file</a> instead.</p>
                                        </iframe>
                                    </div>
                                <?php elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
                                    <div class="text-center">
                                        <img src="/materials/<?= $material['id'] ?>/view"
                                             alt="<?= esc($material['file_name_original']) ?>"
                                             class="img-fluid rounded shadow"
                                             style="max-height: 600px;">
                                    </div>
                                <?php elseif ($fileExtension === 'txt'): ?>
                                    <div class="border rounded p-3" style="max-height: 600px; overflow-y: auto; background-color: #f8f9fa;">
                                        <pre class="mb-0" style="white-space: pre-wrap; font-family: 'Courier New', monospace; font-size: 0.9rem;">
                                            <?php
                                            $filePath = WRITEPATH . $material['file_path'];
                                            if (file_exists($filePath)) {
                                                echo htmlspecialchars(file_get_contents($filePath));
                                            } else {
                                                echo "File content not available.";
                                            }
                                            ?>
                                        </pre>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-file-earmark-x text-muted" style="font-size: 4rem;"></i>
                                    <h5 class="mt-3 text-muted">Preview Not Available</h5>
                                    <p class="text-muted mb-4">
                                        This file type cannot be previewed in the browser.<br>
                                        Please download the file to view its contents.
                                    </p>
                                    <a href="/materials/<?= $material['id'] ?>/download"
                                       class="btn btn-primary btn-lg"
                                       target="_blank">
                                        <i class="bi bi-download me-2"></i>Download File
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- File Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">File Information</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Uploaded by:</small><br>
                                            <strong><?= esc($material['uploader_name']) ?></strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Upload date:</small><br>
                                            <strong><?= date('F j, Y \a\t g:i A', strtotime($material['uploaded_at'])) ?></strong>
                                        </div>
                                    </div>
                                    <?php if (!empty($material['description'])): ?>
                                        <div class="mt-3">
                                            <small class="text-muted">Description:</small><br>
                                            <em><?= esc($material['description']) ?></em>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
