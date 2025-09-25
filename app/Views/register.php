<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-lg border-0 rounded-3 bg-light">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 fw-bold text-primary">Create Account</h2>

                <?php if (session()->getFlashdata('register_error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= esc(session()->getFlashdata('register_error')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('register') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-floating mb-3">
                        <input 
                            type="text" 
                            class="form-control border-0 bg-white shadow-sm" 
                            id="name" 
                            name="name" 
                            placeholder="Your Name"
                            required 
                            value="<?= esc(old('name')) ?>">
                        <label for="name">Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input 
                            type="email" 
                            class="form-control border-0 bg-white shadow-sm" 
                            id="email" 
                            name="email" 
                            placeholder="name@example.com"
                            required 
                            value="<?= esc(old('email')) ?>">
                        <label for="email">Email address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input 
                            type="password" 
                            class="form-control border-0 bg-white shadow-sm" 
                            id="password" 
                            name="password" 
                            placeholder="Password"
                            required>
                        <label for="password">Password</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input 
                            type="password" 
                            class="form-control border-0 bg-white shadow-sm" 
                            id="password_confirm" 
                            name="password_confirm" 
                            placeholder="Confirm Password"
                            required>
                        <label for="password_confirm">Confirm Password</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-person-plus-fill me-1"></i> Create Account
                    </button>
                </form>

                <hr class="my-4">

                <p class="text-center small mb-0">
                    Already have an account? 
                    <a href="<?= base_url('login') ?>" class="text-decoration-none fw-semibold text-primary">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
