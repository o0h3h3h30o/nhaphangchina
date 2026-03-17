<?= $this->extend('install/layout') ?>
<?= $this->section('content') ?>

<div class="install-card card">
    <div class="install-header">
        <h2><i class="fas fa-cogs me-2"></i>Cai Dat He Thong</h2>
        <p>Van Chuyen Hong Phat - Buoc 1: Kiem tra yeu cau</p>
        <div class="step-indicator">
            <div class="step-dot active"></div>
            <div class="step-dot"></div>
            <div class="step-dot"></div>
            <div class="step-dot"></div>
        </div>
    </div>
    <div class="install-body">
        <h5 class="mb-3"><i class="fas fa-clipboard-check me-2 text-primary"></i>Kiem tra he thong</h5>

        <?php
        $allPassed = true;
        foreach ($checks as $label => $passed):
            if (!$passed) $allPassed = false;
        ?>
        <div class="check-item">
            <div class="check-icon">
                <?php if ($passed): ?>
                    <i class="fas fa-check-circle text-success"></i>
                <?php else: ?>
                    <i class="fas fa-times-circle text-danger"></i>
                <?php endif; ?>
            </div>
            <div class="ms-2"><?= esc($label) ?></div>
        </div>
        <?php endforeach; ?>

        <div class="text-center mt-4">
            <?php if ($allPassed): ?>
                <a href="/install/database" class="btn btn-install">
                    <i class="fas fa-arrow-right me-2"></i>Tiep tuc
                </a>
            <?php else: ?>
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Vui long khac phuc cac loi truoc khi tiep tuc.
                </div>
                <a href="/install" class="btn btn-secondary">
                    <i class="fas fa-redo me-2"></i>Kiem tra lai
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
