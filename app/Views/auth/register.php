<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h4 class="card-title text-center mb-4">Dang ky tai khoan</h4>

        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <form action="<?= url_to('auth/register') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="username" class="form-label">Ten dang nhap</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" required>
            </div>

            <div class="mb-3">
                <label for="full_name" class="form-label">Ho va ten</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= old('full_name') ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">So dien thoai</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= old('phone') ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mat khau</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="password_confirm" class="form-label">Xac nhan mat khau</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">Dang ky</button>
            </div>

            <div class="text-center">
                <span>Da co tai khoan?</span>
                <a href="<?= url_to('auth/login') ?>">Dang nhap</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
