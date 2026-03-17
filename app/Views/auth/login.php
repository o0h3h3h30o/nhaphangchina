<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h4 class="card-title text-center mb-4">Dang nhap</h4>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <form action="<?= url_to('auth/login') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mat khau</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">Dang nhap</button>
            </div>

            <div class="text-center">
                <a href="<?= url_to('auth/register') ?>">Dang ky tai khoan moi</a>
                <span class="mx-2">|</span>
                <a href="<?= url_to('auth/forgot-password') ?>">Quen mat khau?</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
