<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
    $sBadge = match($trip['status'] ?? '') {
        'draft' => 'bg-secondary',
        'loading' => 'bg-info',
        'departed' => 'bg-primary',
        'border_processing' => 'bg-warning text-dark',
        'arrived_vn' => 'bg-success',
        'completed' => 'bg-success',
        'issue' => 'bg-danger',
        default => 'bg-secondary',
    };
    $sLabel = match($trip['status'] ?? '') {
        'draft' => 'Nhap',
        'loading' => 'Dang xep hang',
        'departed' => 'Da xuat phat',
        'border_processing' => 'Thong quan',
        'arrived_vn' => 'Da ve VN',
        'completed' => 'Hoan thanh',
        'issue' => 'Co van de',
        default => esc($trip['status']),
    };
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-truck me-2"></i>Chuyen xe #<?= esc($trip['trip_code'] ?? '') ?></h4>
    <div>
        <a href="<?= site_url('admin/truck-trips/' . esc($trip['id']) . '/edit') ?>" class="btn btn-outline-warning"><i class="fas fa-edit me-1"></i> Sua</a>
        <a href="<?= site_url('admin/truck-trips') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Trip Detail -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thong tin chuyen xe</h6>
                <span class="badge <?= $sBadge ?> fs-6"><?= $sLabel ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ma chuyen</label>
                        <div class="fw-bold"><?= esc($trip['trip_code'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ten xe</label>
                        <div><?= esc($trip['truck_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Bien so xe</label>
                        <div><?= esc($trip['plate_number'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Tuyen duong</label>
                        <div><?= esc($trip['route'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Kho xuat phat</label>
                        <div><?= esc($trip['origin_warehouse'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Kho dich</label>
                        <div><?= esc($trip['destination_warehouse'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Ngay xep hang</label>
                        <div><?= !empty($trip['loading_date']) ? date('d/m/Y', strtotime($trip['loading_date'])) : '-' ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Ngay xuat phat</label>
                        <div><?= !empty($trip['departure_date']) ? date('d/m/Y', strtotime($trip['departure_date'])) : '-' ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Du kien den</label>
                        <div><?= !empty($trip['estimated_arrival']) ? date('d/m/Y', strtotime($trip['estimated_arrival'])) : '-' ?></div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Ghi chu</label>
                        <div><?= esc($trip['note'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Update -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Cap nhat trang thai</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/truck-trips/' . esc($trip['id']) . '/status') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Trang thai moi</label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Chon --</option>
                                <option value="draft">Nhap</option>
                                <option value="loading">Dang xep hang</option>
                                <option value="departed">Da xuat phat</option>
                                <option value="border_processing">Dang thong quan</option>
                                <option value="arrived_vn">Da ve VN</option>
                                <option value="completed">Hoan thanh</option>
                                <option value="issue">Co van de</option>
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

        <!-- Orders in this trip -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-box me-2"></i>Don hang trong chuyen</h6>
            </div>
            <div class="card-body">
                <!-- Add order form -->
                <form method="post" action="<?= site_url('admin/truck-trips/' . esc($trip['id']) . '/add-order') ?>" class="row g-3 mb-4">
                    <?= csrf_field() ?>
                    <div class="col-md-8">
                        <input type="text" name="order_code" class="form-control" placeholder="Nhap ma don hang..." required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-plus me-1"></i> Them don</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ma don</th>
                            <th>San pham</th>
                            <th>Can nang (kg)</th>
                            <th>Trang thai</th>
                            <th class="text-center">Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td><strong><?= esc($o['order_code'] ?? '-') ?></strong></td>
                                    <td><?= esc(mb_strimwidth($o['product_name'] ?? '-', 0, 40, '...')) ?></td>
                                    <td><?= isset($o['weight']) ? number_format($o['weight'], 2) : '-' ?></td>
                                    <td>
                                        <?php
                                            $oBadge = match($o['status'] ?? '') {
                                                'pending' => 'bg-secondary',
                                                'received_cn' => 'bg-info',
                                                'in_transit' => 'bg-primary',
                                                'received_vn' => 'bg-success',
                                                'delivering' => 'bg-warning text-dark',
                                                'completed' => 'bg-dark',
                                                default => 'bg-secondary',
                                            };
                                        ?>
                                        <span class="badge <?= $oBadge ?>"><?= esc($o['status'] ?? '-') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <form method="post" action="<?= site_url('admin/truck-trips/' . esc($trip['id']) . '/remove-order') ?>" class="d-inline" onsubmit="return confirm('Xac nhan xoa don nay khoi chuyen xe?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="order_code" value="<?= esc($o['order_code'] ?? '') ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoa khoi chuyen"><i class="fas fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chua co don hang nao trong chuyen</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
                                    <div class="fw-semibold"><?= esc($h['status'] ?? '') ?></div>
                                    <div class="text-muted small"><?= esc($h['note'] ?? '') ?></div>
                                    <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?> - <?= esc($h['changed_by'] ?? 'System') ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Summary -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Tong quan</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Tong don:</td>
                        <td class="fw-bold"><?= count($orders ?? []) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ngay tao:</td>
                        <td><?= !empty($trip['created_at']) ? date('d/m/Y H:i', strtotime($trip['created_at'])) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Cap nhat:</td>
                        <td><?= !empty($trip['updated_at']) ? date('d/m/Y H:i', strtotime($trip['updated_at'])) : '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
