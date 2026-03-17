<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h4 class="card-title text-center mb-4">Quen mat khau</h4>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <p class="text-muted text-center mb-3">Nhap email cua ban de nhan lien ket dat lai mat khau.</p>

        <form action="<?= url_to('auth/forgot-password') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required autofocus>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">Gui yeu cau</button>
            </div>

            <div class="text-center">
                <a href="<?= url_to('auth/login') ?>">Quay lai dang nhap</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
