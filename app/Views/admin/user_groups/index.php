<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-layer-group me-2"></i>Nhom nguoi dung</h4>
    <a href="/admin/user-groups/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Them nhom moi</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Ten nhom</th>
                        <th>Ma</th>
                        <th>Mo ta</th>
                        <th>So user</th>
                        <th>Mac dinh</th>
                        <th class="text-center">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($groups)): ?>
                        <?php foreach ($groups as $g): ?>
                            <tr>
                                <td><?= esc($g['id']) ?></td>
                                <td class="fw-semibold"><?= esc($g['name']) ?></td>
                                <td><code><?= esc($g['code']) ?></code></td>
                                <td><?= esc($g['description'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= number_format($g['user_count'] ?? 0) ?></span>
                                </td>
                                <td>
                                    <?php if ($g['is_default']): ?>
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Mac dinh</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="/admin/user-groups/<?= esc($g['id']) ?>/edit" class="btn btn-sm btn-outline-primary" title="Sua">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (!$g['is_default']): ?>
                                        <form method="post" action="/admin/user-groups/<?= esc($g['id']) ?>/delete" class="d-inline" onsubmit="return confirm('Xoa nhom nay? User se duoc chuyen ve nhom mac dinh.')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Chua co nhom nao</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
