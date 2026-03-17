<?= $this->extend('install/layout') ?>
<?= $this->section('content') ?>

<div class="install-card card">
    <div class="install-header">
        <h2><i class="fas fa-user-shield me-2"></i>Tao Tai Khoan Admin</h2>
        <p>Buoc 3: Tao tai khoan quan tri vien</p>
        <div class="step-indicator">
            <div class="step-dot done"></div>
            <div class="step-dot done"></div>
            <div class="step-dot active"></div>
            <div class="step-dot"></div>
        </div>
    </div>
    <div class="install-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/install/create-admin">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ten dang nhap <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" placeholder="admin" required minlength="3" maxlength="50">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mat khau <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required minlength="6" placeholder="Toi thieu 6 ky tu">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Xac nhan mat khau <span class="text-danger">*</span></label>
                <input type="password" name="password_confirm" class="form-control" required minlength="6">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="/install/database" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lai
                </a>
                <button type="submit" class="btn btn-install">
                    <i class="fas fa-user-plus me-2"></i>Tao Admin
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
