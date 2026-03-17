<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="mb-4">Tao yeu cau rut tien</h4>

    <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Thong tin rut tien</h5></div>
                <div class="card-body">
                    <div class="alert alert-info">
                        So du kha dung: <strong><?= number_format($availableBalance ?? 0, 0, ',', '.') ?> VND</strong>
                    </div>

                    <form action="<?= site_url('withdrawal/store') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="amount" class="form-label">So tien rut (VND) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" value="<?= old('amount') ?>" min="10000" max="<?= $availableBalance ?? 0 ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="bank_account_id" class="form-label">Tai khoan ngan hang <span class="text-danger">*</span></label>
                            <select class="form-select" id="bank_account_id" name="bank_account_id" required>
                                <option value="">-- Chon tai khoan --</option>
                                <?php if (!empty($bankAccounts)): ?>
                                    <?php foreach ($bankAccounts as $ba): ?>
                                        <option value="<?= esc($ba['id']) ?>" <?= old('bank_account_id') == $ba['id'] ? 'selected' : '' ?>>
                                            <?= esc($ba['bank_name']) ?> - <?= esc($ba['account_number']) ?> (<?= esc($ba['account_holder']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small><a href="<?= site_url('bank-accounts/create') ?>">Them tai khoan ngan hang moi</a></small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Tao yeu cau rut tien</button>
                            <a href="<?= site_url('withdrawal') ?>" class="btn btn-outline-secondary">Huy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
