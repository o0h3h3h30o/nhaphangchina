<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="fas fa-<?= isset($group) ? 'edit' : 'plus' ?> me-2"></i>
        <?= isset($group) ? 'Sua nhom' : 'Them nhom moi' ?>
    </h4>
    <a href="/admin/user-groups" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= isset($group) ? '/admin/user-groups/' . $group['id'] . '/edit' : '/admin/user-groups/create' ?>">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Ten nhom <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= esc(old('name') ?? ($group['name'] ?? '')) ?>" required placeholder="VD: Dai ly, VIP">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ma nhom <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" value="<?= esc(old('code') ?? ($group['code'] ?? '')) ?>" required placeholder="VD: dai-ly, vip">
                    <small class="text-muted">Ma duy nhat, khong dau, viet thuong</small>
                </div>
                <div class="col-12">
                    <label class="form-label">Mo ta</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Mo ta nhom..."><?= esc(old('description') ?? ($group['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault" <?= (old('is_default') ?? ($group['is_default'] ?? 0)) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isDefault">
                            Nhom mac dinh (user moi se tu dong vao nhom nay)
                        </label>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> <?= isset($group) ? 'Cap nhat' : 'Tao nhom' ?></button>
                <a href="/admin/user-groups" class="btn btn-secondary">Huy</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
