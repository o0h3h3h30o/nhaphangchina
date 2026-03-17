<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Tai khoan ngan hang</h4>
        <a href="<?= site_url('bank-accounts/create') ?>" class="btn btn-primary"><i class="fas fa-plus-lg"></i> Them tai khoan</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="row g-3">
        <?php if (!empty($bankAccounts)): ?>
            <?php foreach ($bankAccounts as $ba): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0"><?= esc($ba['bank_name']) ?></h5>
                                <?php if (!empty($ba['is_default'])): ?>
                                    <span class="badge bg-primary">Mac dinh</span>
                                <?php endif; ?>
                            </div>
                            <p class="mb-1"><strong>STK:</strong> <?= esc($ba['account_number']) ?></p>
                            <p class="mb-1"><strong>Chu TK:</strong> <?= esc($ba['account_holder']) ?></p>
                            <?php if (!empty($ba['branch'])): ?>
                                <p class="mb-1"><strong>Chi nhanh:</strong> <?= esc($ba['branch']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="<?= site_url('bank-accounts/' . esc($ba['id']) . '/edit') ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-pencil-alt"></i> Sua</a>
                            <?php if (empty($ba['is_default'])): ?>
                                <a href="<?= site_url('bank-accounts/' . esc($ba['id']) . '/set-default') ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-star"></i> Dat mac dinh</a>
                            <?php endif; ?>
                            <a href="<?= site_url('bank-accounts/' . esc($ba['id']) . '/delete') ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Ban co chac chan muon xoa tai khoan nay?')"><i class="fas fa-trash"></i> Xoa</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        <p>Chua co tai khoan ngan hang nao.</p>
                        <a href="<?= site_url('bank-accounts/create') ?>" class="btn btn-primary">Them tai khoan</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
