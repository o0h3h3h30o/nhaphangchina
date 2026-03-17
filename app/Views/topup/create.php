<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="mb-4">Tao yeu cau nap tien</h4>

    <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Thong tin nap tien</h5></div>
                <div class="card-body">
                    <form action="<?= site_url('topup/create') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="amount" class="form-label">So tien nap (VND) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" value="<?= old('amount') ?>" min="10000" required>
                        </div>

                        <div class="mb-3">
                            <label for="transfer_content" class="form-label">Noi dung chuyen khoan</label>
                            <input type="text" class="form-control" id="transfer_content" name="transfer_content" value="<?= old('transfer_content', $suggestedContent ?? '') ?>" readonly>
                            <small class="text-muted">Vui long su dung noi dung chuyen khoan nay khi thuc hien giao dich.</small>
                        </div>

                        <div class="mb-3">
                            <label for="receipt_image" class="form-label">Anh hoa don chuyen khoan</label>
                            <input type="file" class="form-control" id="receipt_image" name="receipt_image" accept="image/*">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Tao yeu cau nap tien</button>
                            <a href="<?= site_url('topup') ?>" class="btn btn-outline-secondary">Huy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Tai khoan ngan hang he thong</h5></div>
                <div class="card-body">
                    <p class="text-muted">Vui long chuyen khoan den mot trong cac tai khoan sau:</p>
                    <?php if (!empty($systemBanks)): ?>
                        <?php foreach ($systemBanks as $bank): ?>
                            <div class="border rounded p-3 mb-3">
                                <div class="row">
                                    <div class="col-sm-4 text-muted">Ngan hang:</div>
                                    <div class="col-sm-8 fw-bold"><?= esc($bank['bank_name']) ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 text-muted">So tai khoan:</div>
                                    <div class="col-sm-8 fw-bold"><?= esc($bank['account_number']) ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 text-muted">Chu tai khoan:</div>
                                    <div class="col-sm-8 fw-bold"><?= esc($bank['account_holder']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Chua co thong tin tai khoan ngan hang.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
