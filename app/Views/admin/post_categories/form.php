<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-<?= $category ? 'edit' : 'plus' ?> me-2"></i><?= $category ? 'Sửa danh mục' : 'Tạo danh mục' ?></h4>
    <a href="/admin/post-categories" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= $category ? '/admin/post-categories/' . $category['id'] . '/edit' : '/admin/post-categories/create' ?>">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= esc($category['name'] ?? '') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Thứ tự</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= esc($category['sort_order'] ?? 0) ?>" min="0">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                            <?= ($category['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isActive">Hoạt động</label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Mô tả</label>
                    <textarea name="description" class="form-control" rows="3"><?= esc($category['description'] ?? '') ?></textarea>
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger"><i class="fas fa-save me-1"></i> <?= $category ? 'Cập nhật' : 'Tạo mới' ?></button>
                <a href="/admin/post-categories" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
