<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Quan ly giao hang</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDeliveryModal"><i class="fas fa-plus me-1"></i> Tao phieu giao</button>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/deliveries') ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Trang thai</label>
                <select name="status" class="form-select">
                    <option value="">-- Tat ca --</option>
                    <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Cho xu ly</option>
                    <option value="assigned" <?= ($statusFilter ?? '') === 'assigned' ? 'selected' : '' ?>>Da phan cong</option>
                    <option value="picking_up" <?= ($statusFilter ?? '') === 'picking_up' ? 'selected' : '' ?>>Dang lay hang</option>
                    <option value="delivering" <?= ($statusFilter ?? '') === 'delivering' ? 'selected' : '' ?>>Dang giao</option>
                    <option value="delivered" <?= ($statusFilter ?? '') === 'delivered' ? 'selected' : '' ?>>Da giao</option>
                    <option value="failed" <?= ($statusFilter ?? '') === 'failed' ? 'selected' : '' ?>>Giao that bai</option>
                    <option value="returned" <?= ($statusFilter ?? '') === 'returned' ? 'selected' : '' ?>>Hoan tra</option>
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
                        <th>Ma phieu giao</th>
                        <th>Ma don hang</th>
                        <th>Nguoi nhan</th>
                        <th>SDT nguoi nhan</th>
                        <th>Trang thai</th>
                        <th>Shipper</th>
                        <th>Ngay hen giao</th>
                        <th class="text-center">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($deliveries)): ?>
                        <?php foreach ($deliveries as $d): ?>
                            <tr>
                                <td><strong><?= esc($d['delivery_code'] ?? '-') ?></strong></td>
                                <td>
                                    <a href="<?= site_url('admin/consignments/' . esc($d['order_id'] ?? '')) ?>"><?= esc($d['order_code'] ?? '-') ?></a>
                                </td>
                                <td><?= esc($d['receiver_name'] ?? '-') ?></td>
                                <td><?= esc($d['receiver_phone'] ?? '-') ?></td>
                                <td>
                                    <?php
                                        $sBadge = match($d['status'] ?? '') {
                                            'pending' => 'bg-secondary',
                                            'assigned' => 'bg-info',
                                            'picking_up' => 'bg-primary',
                                            'delivering' => 'bg-warning text-dark',
                                            'delivered' => 'bg-success',
                                            'failed' => 'bg-danger',
                                            'returned' => 'bg-dark',
                                            default => 'bg-secondary',
                                        };
                                        $sLabel = match($d['status'] ?? '') {
                                            'pending' => 'Cho xu ly',
                                            'assigned' => 'Da phan cong',
                                            'picking_up' => 'Dang lay hang',
                                            'delivering' => 'Dang giao',
                                            'delivered' => 'Da giao',
                                            'failed' => 'That bai',
                                            'returned' => 'Hoan tra',
                                            default => esc($d['status']),
                                        };
                                    ?>
                                    <span class="badge <?= $sBadge ?>"><?= $sLabel ?></span>
                                </td>
                                <td><?= esc($d['shipper'] ?? '-') ?></td>
                                <td><?= !empty($d['scheduled_date']) ? date('d/m/Y', strtotime($d['scheduled_date'])) : '-' ?></td>
                                <td class="text-center">
                                    <a href="<?= site_url('admin/deliveries/' . esc($d['id'])) ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiet">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Khong co du lieu</td>
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

<!-- Create Delivery Modal -->
<div class="modal fade" id="createDeliveryModal" tabindex="-1" aria-labelledby="createDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= site_url('admin/deliveries/create') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="createDeliveryModalLabel">Tao phieu giao hang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Dong"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ma don hang (ky gui) <span class="text-danger">*</span></label>
                        <input type="text" name="order_code" class="form-control" placeholder="Nhap ma don hang..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Tao phieu</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
