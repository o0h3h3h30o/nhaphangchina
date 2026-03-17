<?= $this->extend('install/layout') ?>
<?= $this->section('content') ?>

<div class="install-card card">
    <div class="install-header">
        <h2><i class="fas fa-database me-2"></i>Cau Hinh Database</h2>
        <p>Buoc 2: Ket noi co so du lieu MySQL</p>
        <div class="step-indicator">
            <div class="step-dot done"></div>
            <div class="step-dot active"></div>
            <div class="step-dot"></div>
            <div class="step-dot"></div>
        </div>
    </div>
    <div class="install-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/install/setup-database">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">MySQL Host</label>
                <input type="text" name="hostname" class="form-control" value="localhost" required>
            </div>

            <div class="row">
                <div class="col-8 mb-3">
                    <label class="form-label fw-semibold">Ten database</label>
                    <input type="text" name="database" class="form-control" value="vanchuyenhongphat" required>
                    <div class="form-text">Database se duoc tao tu dong neu chua ton tai.</div>
                </div>
                <div class="col-4 mb-3">
                    <label class="form-label fw-semibold">Port</label>
                    <input type="number" name="port" class="form-control" value="3306">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">MySQL Username</label>
                <input type="text" name="username" class="form-control" value="root" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">MySQL Password</label>
                <input type="password" name="password" class="form-control" placeholder="De trong neu khong co mat khau">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="/install" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lai
                </a>
                <button type="submit" class="btn btn-install">
                    <i class="fas fa-database me-2"></i>Tao Database
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
