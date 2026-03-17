<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="mb-4">Them tai khoan ngan hang</h4>

    <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="<?= site_url('bank-accounts/store') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Ten ngan hang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?= old('bank_name') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="account_number" class="form-label">So tai khoan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="account_number" name="account_number" value="<?= old('account_number') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="account_holder" class="form-label">Chu tai khoan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="account_holder" name="account_holder" value="<?= old('account_holder') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="branch" class="form-label">Chi nhanh</label>
                            <input type="text" class="form-control" id="branch" name="branch" value="<?= old('branch') ?>">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1" <?= old('is_default') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_default">Dat lam tai khoan mac dinh</label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Luu</button>
                            <a href="<?= site_url('bank-accounts') ?>" class="btn btn-outline-secondary">Huy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
