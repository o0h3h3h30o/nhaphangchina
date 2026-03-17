<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-box me-2"></i>Chi tiet don #<?= esc($order['order_code']) ?></h4>
    <a href="/admin/consignments" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<?php
    $sBadge = match($order['status'] ?? '') {
        'pending' => 'bg-secondary',
        'received_cn' => 'bg-info',
        'in_transit' => 'bg-primary',
        'received_vn' => 'bg-success',
        'delivering' => 'bg-warning text-dark',
        'completed' => 'bg-dark',
        'cancelled' => 'bg-danger',
        default => 'bg-secondary',
    };
    $sLabel = match($order['status'] ?? '') {
        'pending' => 'Cho xu ly',
        'received_cn' => 'Kho TQ',
        'in_transit' => 'Van chuyen',
        'received_vn' => 'Kho VN',
        'delivering' => 'Dang giao',
        'completed' => 'Hoan thanh',
        'cancelled' => 'Da huy',
        default => esc($order['status']),
    };
?>

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Order Detail -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thong tin don hang</h6>
                <span class="badge <?= $sBadge ?> fs-6"><?= $sLabel ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ma don</label>
                        <div class="fw-bold"><?= esc($order['order_code']) ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Tracking TQ</label>
                        <div><?= esc($order['cn_tracking_code'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">San pham</label>
                        <div><?= esc($order['product_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Loai hang</label>
                        <div><?= esc($order['cargo_type'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Can nang thuc te</label>
                        <div><?= $order['actual_weight'] ? number_format($order['actual_weight'], 2) . ' kg' : 'Chua cap nhat' ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ghi chu</label>
                        <div><?= esc($order['note'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ngay tao</label>
                        <div><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Thanh toan</label>
                        <div>
                            <?php if (!empty($order['is_paid'])): ?>
                                <span class="badge bg-success">Da thanh toan</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Chua thanh toan</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Breakdown -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Chi tiet phi</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($feeBreakdown)): ?>
                    <table class="table table-sm table-borderless mb-0">
                        <?php foreach ($feeBreakdown as $fee): ?>
                            <tr>
                                <td class="text-muted"><?= esc($fee['label'] ?? $fee['type'] ?? '') ?></td>
                                <td class="text-end"><?= number_format($fee['amount'] ?? 0, 0, ',', '.') ?> VND</td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="border-top fw-bold">
                            <td>Tong phi</td>
                            <td class="text-end text-primary"><?= number_format($order['total_fee'] ?? 0, 0, ',', '.') ?> VND</td>
                        </tr>
                    </table>
                <?php else: ?>
                    <p class="text-muted mb-0">Chua co thong tin phi.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status Update Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Cap nhat trang thai</h6>
            </div>
            <div class="card-body">
                <form method="post" action="/admin/consignments/<?= esc($order['id']) ?>/status">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Trang thai moi</label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Chon --</option>
                                <option value="submitted">Da gui</option>
                                <option value="received_cn">Da nhan kho TQ</option>
                                <option value="packed_for_truck">Dong hang len xe</option>
                                <option value="in_transit_cn_vn">Dang van chuyen</option>
                                <option value="received_vn">Da ve kho VN</option>
                                <option value="fee_calculated">Da tinh phi</option>
                                <option value="ready_for_delivery">San sang giao</option>
                                <option value="delivering">Dang giao hang</option>
                                <option value="completed">Hoan thanh</option>
                                <option value="cancelled">Huy don</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Ghi chu</label>
                            <textarea name="note" class="form-control" rows="1" placeholder="Ghi chu cap nhat..."></textarea>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Cap nhat</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Weight Update Form -->
        <?php if (in_array($order['status'] ?? '', ['received_vn', 'received_cn'])): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-weight me-2"></i>Cap nhat can nang</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="/admin/consignments/<?= esc($order['id']) ?>/weight" class="row g-3">
                        <?= csrf_field() ?>
                        <div class="col-md-4">
                            <label class="form-label">Can nang thuc te (kg)</label>
                            <input type="number" name="actual_weight" class="form-control" step="0.01" min="0" value="<?= esc($order['actual_weight'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-info text-white w-100">Luu</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex gap-2 flex-wrap">
                <?php if (!empty($order['actual_weight']) && empty($order['total_fee'])): ?>
                    <form method="post" action="/admin/consignments/<?= esc($order['id']) ?>/calculate-fee">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-calculator me-1"></i> Tinh phi</button>
                    </form>
                <?php endif; ?>

                <?php if (!empty($order['total_fee']) && empty($order['is_paid'])): ?>
                    <form method="post" action="/admin/consignments/<?= esc($order['id']) ?>/charge" onsubmit="return confirm('Xac nhan tru tien vi cua khach hang?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success"><i class="fas fa-credit-card me-1"></i> Thu phi tu vi</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status History -->
        <?php if (!empty($statusHistory)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Lich su trang thai</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($statusHistory as $h): ?>
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                        <i class="fas fa-circle text-white" style="font-size:8px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-semibold"><?= esc($h['to_status'] ?? '') ?></div>
                                    <div class="text-muted small"><?= esc($h['note'] ?? '') ?></div>
                                    <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?> - <?= esc($h['changed_by'] ?? 'System') ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tracking Events -->
        <?php if (!empty($trackingEvents)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Tracking events</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Thoi gian</th>
                                    <th>Vi tri</th>
                                    <th>Mo ta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($trackingEvents as $event): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($event['event_time'] ?? $event['created_at'])) ?></td>
                                        <td><?= esc($event['location'] ?? '-') ?></td>
                                        <td><?= esc($event['description'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Packages -->
        <?php if (!empty($packages)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-boxes me-2"></i>Danh sach kien hang</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Ma kien</th>
                                    <th>Can nang (kg)</th>
                                    <th>Kich thuoc</th>
                                    <th>Ghi chu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($packages as $i => $pkg): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= esc($pkg['package_code'] ?? '-') ?></td>
                                        <td><?= $pkg['weight'] ? number_format($pkg['weight'], 2) : '-' ?></td>
                                        <td><?= esc($pkg['dimensions'] ?? '-') ?></td>
                                        <td><?= esc($pkg['note'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: User Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Thong tin khach hang</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">User:</td>
                        <td>
                            <a href="/admin/users/<?= esc($order['user_id']) ?>"><?= esc($order['username'] ?? '-') ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td><?= esc($order['user_email'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dien thoai:</td>
                        <td><?= esc($order['user_phone'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
