<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-3 bg-dark text-light">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 fw-bold text-success">Sign In</h2>

                <?php if (session()->getFlashdata('register_success')): ?>
                    <div class="alert alert-success" role="alert">
                        <?= esc(session()->getFlashdata('register_success')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('login_error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= esc(session()->getFlashdata('login_error')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('login') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-floating mb-3">
                        <input 
                            type="email" 
                            class="form-control bg-secondary text-light border-0" 
                            id="email" 
                            name="email" 
                            placeholder="name@example.com"
                            required 
                            value="<?= esc(old('email')) ?>">
                        <label for="email">Email address</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input 
                            type="password" 
                            class="form-control bg-secondary text-light border-0" 
                            id="password" 
                            name="password" 
                            placeholder="Password" 
                            required>
                        <label for="password">Password</label>
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">Login</button>
                </form>

                <hr class="my-4 border-light">

                <p class="text-center small mb-0">
                    Don't have an account? 
                    <a href="<?= base_url('register') ?>" class="text-warning fw-semibold">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
