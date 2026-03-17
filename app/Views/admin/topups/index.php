<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Quan ly nap tien</h4>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="/admin/topups" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Trang thai</label>
                <select name="status" class="form-select">
                    <option value="">-- Tat ca --</option>
                    <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Cho duyet</option>
                    <option value="approved" <?= ($status ?? '') === 'approved' ? 'selected' : '' ?>>Da duyet</option>
                    <option value="rejected" <?= ($status ?? '') === 'rejected' ? 'selected' : '' ?>>Tu choi</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Loc</button>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ma nap</th>
                        <th>User</th>
                        <th>So tien</th>
                        <th>Ngan hang</th>
                        <th>Trang thai</th>
                        <th>Ngay tao</th>
                        <th class="text-center">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($topups)): ?>
                        <?php foreach ($topups as $t): ?>
                            <tr>
                                <td><strong><?= esc($t['code']) ?></strong></td>
                                <td><?= esc($t['username'] ?? '-') ?></td>
                                <td class="fw-bold text-success"><?= number_format($t['amount'] ?? 0, 0, ',', '.') ?> VND</td>
                                <td><?= esc($t['bank_name'] ?? '-') ?></td>
                                <td>
                                    <?php
                                        $tBadge = match($t['status'] ?? '') {
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                        $tLabel = match($t['status'] ?? '') {
                                            'pending' => 'Cho duyet',
                                            'approved' => 'Da duyet',
                                            'rejected' => 'Tu choi',
                                            default => esc($t['status']),
                                        };
                                    ?>
                                    <span class="badge <?= $tBadge ?>"><?= $tLabel ?></span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="/admin/topups/<?= esc($t['id']) ?>" class="btn btn-sm btn-outline-primary" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (($t['status'] ?? '') === 'pending'): ?>
                                        <form method="post" action="/admin/topups/<?= esc($t['id']) ?>/approve" class="d-inline" onsubmit="return confirm('Duyet yeu cau nap tien nay?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Duyet"><i class="fas fa-check"></i></button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Tu choi" data-bs-toggle="modal" data-bs-target="#rejectModal<?= esc($t['id']) ?>">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal<?= esc($t['id']) ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="post" action="/admin/topups/<?= esc($t['id']) ?>/reject">
                                                        <?= csrf_field() ?>
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Tu choi nap tien #<?= esc($t['code']) ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Ly do tu choi</label>
                                                                <textarea name="reject_reason" class="form-control" rows="3" required placeholder="Nhap ly do tu choi..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huy</button>
                                                            <button type="submit" class="btn btn-danger">Tu choi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Khong co du lieu</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($pager)): ?>
        <div class="card-footer bg-white border-top">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
