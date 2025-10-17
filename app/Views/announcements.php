<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Announcements Banner -->
    <div class="row">
        <div class="col-12">
            <div class="bg-primary text-white p-4 mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-bullhorn me-3" style="font-size: 2rem;"></i>
                    <div>
                        <h2 class="mb-1">Announcements</h2>
                        <p class="mb-0">Stay updated with the latest news and announcements</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Content -->
    <div class="row">
        <div class="col-12">
            <?php if (empty($announcements)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No announcements available at the moment.
                </div>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold">
                                <?= esc($announcement['title']) ?>
                            </h5>
                            <p class="card-text">
                                <?= nl2br(esc($announcement['content'])) ?>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Posted on: <?= date('F j, Y \a\t g:i A', strtotime($announcement['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
