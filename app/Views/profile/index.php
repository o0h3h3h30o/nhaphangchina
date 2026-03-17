<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="mb-4"><i class="fas fa-user-circle me-2"></i>Thong tin ca nhan</h4>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Profile Info -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Cap nhat thong tin</h5></div>
                <div class="card-body">
                    <?php if (isset($validation)): ?>
                        <div class="alert alert-danger">
                            <?= $validation->listErrors() ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('profile') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Ho va ten</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= old('full_name', $profile['full_name'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?= esc($user['email'] ?? '') ?>" disabled>
                            <small class="text-muted">Email khong the thay doi.</small>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">So dien thoai</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= old('phone', $user['phone'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Dia chi</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?= old('address', $profile['address'] ?? '') ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">Tinh/Thanh pho</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?= old('city', $profile['city'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="district" class="form-label">Quan/Huyen</label>
                                <input type="text" class="form-control" id="district" name="district" value="<?= old('district', $profile['district'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ward" class="form-label">Phuong/Xa</label>
                                <input type="text" class="form-control" id="ward" name="ward" value="<?= old('ward', $profile['ward'] ?? '') ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Cap nhat thong tin</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Doi mat khau</h5></div>
                <div class="card-body">
                    <form action="<?= site_url('profile/password') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mat khau hien tai</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mat khau moi</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xac nhan mat khau moi</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-warning"><i class="fas fa-key me-1"></i> Doi mat khau</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
