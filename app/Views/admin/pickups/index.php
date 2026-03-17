<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-people-carry me-2"></i>Quan ly lay hang</h4>
    <span class="badge bg-primary fs-6"><?= $total ?? 0 ?> yeu cau</span>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="get" action="/admin/pickups" class="d-flex gap-2 align-items-center">
            <span class="text-muted"><i class="fas fa-filter me-1"></i></span>
            <select name="status" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                <option value="">Tat ca</option>
                <option value="requested" <?= ($status ?? '') === 'requested' ? 'selected' : '' ?>>Yeu cau</option>
                <option value="confirmed" <?= ($status ?? '') === 'confirmed' ? 'selected' : '' ?>>Da xac nhan</option>
                <option value="picked_up" <?= ($status ?? '') === 'picked_up' ? 'selected' : '' ?>>Da lay</option>
                <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>Hoan thanh</option>
                <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>>Da huy</option>
            </select>
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
                        <th>Ma don</th>
                        <th>Khach hang</th>
                        <th>Nguoi nhan</th>
                        <th>SDT</th>
                        <th>Dia chi</th>
                        <th>Phi (VND)</th>
                        <th>Trang thai</th>
                        <th>Ngay tao</th>
                        <th class="text-center">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pickups)): ?>
                        <?php foreach ($pickups as $p): ?>
                            <tr>
                                <td>
                                    <a href="/admin/consignments/<?= esc($p['consignment_order_id']) ?>">
                                        <strong><?= esc($p['order_code'] ?? '-') ?></strong>
                                    </a>
                                </td>
                                <td>
                                    <a href="/admin/users/<?= esc($p['user_id']) ?>"><?= esc($p['username'] ?? '-') ?></a>
                                </td>
                                <td><strong><?= esc($p['receiver_name'] ?? '-') ?></strong></td>
                                <td><?= esc($p['receiver_phone'] ?? '-') ?></td>
                                <td><small><?= esc(mb_substr($p['receiver_address'] ?? '-', 0, 30)) ?><?= mb_strlen($p['receiver_address'] ?? '') > 30 ? '...' : '' ?></small></td>
                                <td class="fw-bold text-success"><?= number_format($p['total_fee'] ?? 0, 0, ',', '.') ?></td>
                                <td>
                                    <?php
                                        $sBadge = match($p['status'] ?? '') {
                                            'requested' => 'bg-warning text-dark',
                                            'confirmed' => 'bg-info',
                                            'scheduled' => 'bg-primary',
                                            'picked_up', 'completed' => 'bg-success',
                                            'missed' => 'bg-danger',
                                            'cancelled' => 'bg-secondary',
                                            default => 'bg-secondary',
                                        };
                                        $sLabel = match($p['status'] ?? '') {
                                            'requested' => 'Yeu cau',
                                            'confirmed' => 'Da xac nhan',
                                            'scheduled' => 'Da hen lich',
                                            'picked_up' => 'Da lay',
                                            'completed' => 'Hoan thanh',
                                            'missed' => 'Lo hen',
                                            'cancelled' => 'Da huy',
                                            default => esc($p['status']),
                                        };
                                    ?>
                                    <span class="badge <?= $sBadge ?>"><?= $sLabel ?></span>
                                </td>
                                <td class="small"><?= date('d/m H:i', strtotime($p['created_at'])) ?></td>
                                <td class="text-center">
                                    <?php if (($p['status'] ?? '') === 'requested'): ?>
                                        <form method="post" action="/admin/pickups/<?= esc($p['id']) ?>/confirm" class="d-inline" onsubmit="return confirm('Xac nhan yeu cau nay?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-info" title="Xac nhan"><i class="fas fa-check"></i></button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (in_array($p['status'] ?? '', ['confirmed'])): ?>
                                        <form method="post" action="/admin/pickups/<?= esc($p['id']) ?>/complete" class="d-inline" onsubmit="return confirm('Xac nhan da giao hang?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Hoan thanh"><i class="fas fa-check-double"></i></button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (!empty($p['note'])): ?>
                                        <span class="btn btn-sm btn-outline-secondary" title="<?= esc($p['note']) ?>"><i class="fas fa-sticky-note"></i></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Khong co du lieu</td>
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
