<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-newspaper me-2"></i>Quản lý bài viết</h4>
    <a href="/admin/posts/create" class="btn btn-danger"><i class="fas fa-plus me-1"></i> Tạo bài viết</a>
</div>

<!-- Filter by section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="text-muted me-2"><i class="fas fa-filter me-1"></i>Lọc theo mục:</span>
            <a href="/admin/posts" class="btn btn-sm <?= empty($currentSection) ? 'btn-danger' : 'btn-outline-secondary' ?>">Tất cả</a>
            <?php foreach ($sections as $key => $label): ?>
                <a href="/admin/posts?section=<?= $key ?>" class="btn btn-sm <?= $currentSection === $key ? 'btn-danger' : 'btn-outline-secondary' ?>"><?= esc($label) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Tiêu đề</th>
                    <th>Mục</th>
                    <th>Danh mục</th>
                    <th>Icon</th>
                    <th style="width:80px;">Thứ tự</th>
                    <th style="width:100px;">Trạng thái</th>
                    <th style="width:160px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Chưa có bài viết nào.</td></tr>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= $post['id'] ?></td>
                            <td>
                                <strong><?= esc($post['title']) ?></strong>
                                <?php if (!empty($post['excerpt'])): ?>
                                    <br><small class="text-muted"><?= esc(mb_substr($post['excerpt'], 0, 80)) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $sectionColors = [
                                        'gioi-thieu' => 'primary',
                                        'chinh-sach' => 'success',
                                        'quy-dinh'   => 'warning',
                                        'huong-dan'  => 'info',
                                        'tin-tuc'    => 'secondary',
                                    ];
                                    $color = $sectionColors[$post['section']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= esc($sections[$post['section']] ?? $post['section']) ?></span>
                            </td>
                            <td>
                                <?= !empty($post['category_name']) ? esc($post['category_name']) : '<span class="text-muted">-</span>' ?>
                            </td>
                            <td>
                                <?php if (!empty($post['icon'])): ?>
                                    <i class="<?= esc($post['icon']) ?> fa-lg"></i>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $post['sort_order'] ?></td>
                            <td>
                                <form method="post" action="/admin/posts/<?= $post['id'] ?>/toggle" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm <?= $post['is_published'] ? 'btn-success' : 'btn-outline-secondary' ?>">
                                        <?= $post['is_published'] ? '<i class="fas fa-eye"></i> Hiện' : '<i class="fas fa-eye-slash"></i> Ẩn' ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <a href="/admin/posts/<?= $post['id'] ?>/edit" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form method="post" action="/admin/posts/<?= $post['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Xóa bài viết này?')">
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
