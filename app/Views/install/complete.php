<?= $this->extend('install/layout') ?>
<?= $this->section('content') ?>

<div class="install-card card">
    <div class="install-header" style="background: linear-gradient(135deg, #4caf50, #2e7d32);">
        <h2><i class="fas fa-check-circle me-2"></i>Cai Dat Thanh Cong!</h2>
        <p>He thong da san sang su dung</p>
        <div class="step-indicator">
            <div class="step-dot done"></div>
            <div class="step-dot done"></div>
            <div class="step-dot done"></div>
            <div class="step-dot active"></div>
        </div>
    </div>
    <div class="install-body text-center">
        <div class="mb-4">
            <i class="fas fa-shipping-fast text-success" style="font-size: 4rem;"></i>
        </div>

        <h4 class="mb-3">Van Chuyen Hong Phat</h4>
        <p class="text-muted mb-4">
            He thong da duoc cai dat thanh cong. Ban co the bat dau su dung ngay bay gio.
        </p>

        <div class="alert alert-warning text-start">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Luu y bao mat:</h6>
            <ul class="mb-0 small">
                <li>Xoa thu muc <code>install/</code> sau khi cai dat xong</li>
                <li>Doi mat khau admin mac dinh ngay lap tuc</li>
                <li>Cau hinh <code>.env</code> cho moi truong production</li>
            </ul>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="/" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-home me-2"></i>Trang chu
            </a>
            <a href="/login" class="btn btn-install btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i>Dang nhap
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
