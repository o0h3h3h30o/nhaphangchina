<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
    $sBadge = match($delivery['status'] ?? '') {
        'pending' => 'bg-secondary',
        'assigned' => 'bg-info',
        'picking_up' => 'bg-primary',
        'delivering' => 'bg-warning text-dark',
        'delivered' => 'bg-success',
        'failed' => 'bg-danger',
        'returned' => 'bg-dark',
        default => 'bg-secondary',
    };
    $sLabel = match($delivery['status'] ?? '') {
        'pending' => 'Cho xu ly',
        'assigned' => 'Da phan cong',
        'picking_up' => 'Dang lay hang',
        'delivering' => 'Dang giao',
        'delivered' => 'Da giao',
        'failed' => 'That bai',
        'returned' => 'Hoan tra',
        default => esc($delivery['status']),
    };
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Phieu giao #<?= esc($delivery['delivery_code'] ?? '') ?></h4>
    <a href="<?= site_url('admin/deliveries') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Delivery Detail -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thong tin giao hang</h6>
                <span class="badge <?= $sBadge ?> fs-6"><?= $sLabel ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ma phieu giao</label>
                        <div class="fw-bold"><?= esc($delivery['delivery_code'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ma don hang</label>
                        <div><?= esc($delivery['order_code'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Nguoi nhan</label>
                        <div><?= esc($delivery['receiver_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">SDT nguoi nhan</label>
                        <div><?= esc($delivery['receiver_phone'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small">Dia chi giao</label>
                        <div><?= esc($delivery['receiver_address'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Shipper</label>
                        <div><?= esc($delivery['shipper'] ?? 'Chua phan cong') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ngay hen giao</label>
                        <div><?= !empty($delivery['scheduled_date']) ? date('d/m/Y', strtotime($delivery['scheduled_date'])) : '-' ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ngay tao</label>
                        <div><?= !empty($delivery['created_at']) ? date('d/m/Y H:i', strtotime($delivery['created_at'])) : '-' ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ghi chu</label>
                        <div><?= esc($delivery['note'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Shipper -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-user-tag me-2"></i>Phan cong shipper</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/deliveries/' . esc($delivery['id']) . '/assign') ?>" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-md-8">
                        <label class="form-label">Chon nhan vien giao hang</label>
                        <select name="shipper_id" class="form-select" required>
                            <option value="">-- Chon shipper --</option>
                            <?php if (!empty($staffUsers)): ?>
                                <?php foreach ($staffUsers as $staff): ?>
                                    <option value="<?= esc($staff['id']) ?>" <?= ($delivery['shipper_id'] ?? '') == $staff['id'] ? 'selected' : '' ?>>
                                        <?= esc($staff['username'] ?? $staff['full_name'] ?? '') ?> - <?= esc($staff['phone'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Phan cong</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Status -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Cap nhat trang thai</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/deliveries/' . esc($delivery['id']) . '/status') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Trang thai moi</label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Chon --</option>
                                <option value="pending">Cho xu ly</option>
                                <option value="assigned">Da phan cong</option>
                                <option value="picking_up">Dang lay hang</option>
                                <option value="delivering">Dang giao</option>
                                <option value="delivered">Da giao</option>
                                <option value="failed">Giao that bai</option>
                                <option value="returned">Hoan tra</option>
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

        <!-- Upload Proof -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-camera me-2"></i>Anh xac nhan giao hang</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/deliveries/' . esc($delivery['id']) . '/upload-proof') ?>" enctype="multipart/form-data" class="row g-3 mb-4">
                    <?= csrf_field() ?>
                    <div class="col-md-8">
                        <input type="file" name="proof_image" class="form-control" accept="image/*" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-upload me-1"></i> Tai len</button>
                    </div>
                </form>

                <!-- Proof Images Gallery -->
                <?php if (!empty($proofs)): ?>
                    <div class="row g-3">
                        <?php foreach ($proofs as $proof): ?>
                            <div class="col-md-3">
                                <div class="card">
                                    <img src="<?= esc($proof['image_url'] ?? '') ?>" class="card-img-top" alt="Proof" style="height:150px;object-fit:cover;">
                                    <div class="card-body p-2 text-center">
                                        <small class="text-muted"><?= !empty($proof['created_at']) ? date('d/m/Y H:i', strtotime($proof['created_at'])) : '' ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Chua co anh xac nhan.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status History -->
        <?php if (!empty($statusHistory)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Lich su trang thai</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Thoi gian</th>
                                    <th>Trang thai</th>
                                    <th>Ghi chu</th>
                                    <th>Nguoi cap nhat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($statusHistory as $h): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                                        <td><span class="badge bg-secondary"><?= esc($h['status'] ?? '') ?></span></td>
                                        <td><?= esc($h['note'] ?? '-') ?></td>
                                        <td><?= esc($h['changed_by'] ?? 'System') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-info me-2"></i>Trang thai hien tai</h6>
            </div>
            <div class="card-body text-center">
                <span class="badge <?= $sBadge ?> fs-5 px-4 py-2"><?= $sLabel ?></span>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
