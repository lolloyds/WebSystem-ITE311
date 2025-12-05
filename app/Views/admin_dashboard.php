<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <h2 class="card-title text-dark">Welcome admin!</h2>
                    <a href="<?= base_url('manage-users') ?>" class="btn btn-primary mt-3">
                        <i class="bi bi-people-fill me-2"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
