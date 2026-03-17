<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h4 class="card-title text-center mb-4">Dat lai mat khau</h4>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <form action="<?= url_to('auth/reset-password') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token ?? '') ?>">

            <div class="mb-3">
                <label for="password" class="form-label">Mat khau moi</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="password_confirm" class="form-label">Xac nhan mat khau moi</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">Dat lai mat khau</button>
            </div>

            <div class="text-center">
                <a href="<?= url_to('auth/login') ?>">Quay lai dang nhap</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
