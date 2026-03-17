<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-minus-circle me-2"></i>Quan ly rut tien</h4>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="/admin/withdrawals" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Trang thai</label>
                <select name="status" class="form-select">
                    <option value="">-- Tat ca --</option>
                    <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Cho duyet</option>
                    <option value="approved" <?= ($statusFilter ?? '') === 'approved' ? 'selected' : '' ?>>Da duyet</option>
                    <option value="completed" <?= ($statusFilter ?? '') === 'completed' ? 'selected' : '' ?>>Hoan thanh</option>
                    <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>>Tu choi</option>
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
                        <th>Ma rut</th>
                        <th>User</th>
                        <th>So tien</th>
                        <th>Ngan hang</th>
                        <th>Trang thai</th>
                        <th>Ngay tao</th>
                        <th class="text-center">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($withdrawals)): ?>
                        <?php foreach ($withdrawals as $w): ?>
                            <tr>
                                <td><strong><?= esc($w['code']) ?></strong></td>
                                <td><?= esc($w['username'] ?? '-') ?></td>
                                <td class="fw-bold text-danger"><?= number_format($w['amount'] ?? 0, 0, ',', '.') ?> VND</td>
                                <td><?= esc($w['bank_name'] ?? '-') ?></td>
                                <td>
                                    <?php
                                        $wBadge = match($w['status'] ?? '') {
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                        $wLabel = match($w['status'] ?? '') {
                                            'pending' => 'Cho duyet',
                                            'approved' => 'Da duyet',
                                            'completed' => 'Hoan thanh',
                                            'rejected' => 'Tu choi',
                                            default => esc($w['status']),
                                        };
                                    ?>
                                    <span class="badge <?= $wBadge ?>"><?= $wLabel ?></span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($w['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="/admin/withdrawals/<?= esc($w['id']) ?>" class="btn btn-sm btn-outline-primary" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
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
