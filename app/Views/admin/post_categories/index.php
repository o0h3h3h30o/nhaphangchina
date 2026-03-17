<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-folder me-2"></i>Danh mục tin tức</h4>
    <a href="/admin/post-categories/create" class="btn btn-danger"><i class="fas fa-plus me-1"></i> Tạo danh mục</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Số bài viết</th>
                    <th style="width:80px;">Thứ tự</th>
                    <th style="width:100px;">Trạng thái</th>
                    <th style="width:140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Chưa có danh mục nào.</td></tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><strong><?= esc($cat['name']) ?></strong></td>
                            <td><code><?= esc($cat['slug']) ?></code></td>
                            <td><span class="badge bg-secondary"><?= $cat['post_count'] ?></span></td>
                            <td><?= $cat['sort_order'] ?></td>
                            <td>
                                <span class="badge bg-<?= $cat['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $cat['is_active'] ? 'Hoạt động' : 'Tắt' ?>
                                </span>
                            </td>
                            <td>
                                <a href="/admin/post-categories/<?= $cat['id'] ?>/edit" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form method="post" action="/admin/post-categories/<?= $cat['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
