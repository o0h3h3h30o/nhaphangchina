<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Chi tiet giao hang #<?= esc($delivery['delivery_code']) ?></h4>
        <a href="<?= site_url('deliveries') ?>" class="btn btn-outline-secondary">Quay lai</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Thong tin giao hang</h5></div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th class="text-muted" width="40%">Ma giao hang:</th><td><?= esc($delivery['delivery_code']) ?></td></tr>
                        <tr><th class="text-muted">Ma don hang:</th><td><a href="<?= site_url('consignments/' . esc($delivery['order_id'] ?? '')) ?>"><?= esc($delivery['order_code'] ?? '-') ?></a></td></tr>
                        <tr><th class="text-muted">Nguoi nhan:</th><td><?= esc($delivery['receiver_name'] ?? '-') ?></td></tr>
                        <tr><th class="text-muted">SDT:</th><td><?= esc($delivery['receiver_phone'] ?? '-') ?></td></tr>
                        <tr><th class="text-muted">Dia chi:</th><td><?= esc($delivery['receiver_address'] ?? '-') ?></td></tr>
                        <tr>
                            <th class="text-muted">Trang thai:</th>
                            <td>
                                <?php
                                $statusColors = [
                                    'pending' => 'warning', 'assigned' => 'info', 'in_transit' => 'primary',
                                    'delivered' => 'success', 'failed' => 'danger',
                                ];
                                $color = $statusColors[$delivery['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= esc($delivery['status']) ?></span>
                            </td>
                        </tr>
                        <tr><th class="text-muted">Ngay hen giao:</th><td><?= !empty($delivery['scheduled_date']) ? date('d/m/Y', strtotime($delivery['scheduled_date'])) : '-' ?></td></tr>
                        <tr><th class="text-muted">Ngay giao:</th><td><?= !empty($delivery['delivered_at']) ? date('d/m/Y H:i', strtotime($delivery['delivered_at'])) : '-' ?></td></tr>
                        <tr><th class="text-muted">Ghi chu:</th><td><?= esc($delivery['note'] ?? '-') ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Status History -->
            <?php if (!empty($statusHistory)): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Lich su trang thai</h5></div>
                <div class="card-body">
                    <?php foreach ($statusHistory as $history): ?>
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                                <?php if ($history !== end($statusHistory)): ?>
                                    <div class="border-start border-2 ms-3" style="height:20px;"></div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php $hColor = $statusColors[$history['status']] ?? 'secondary'; ?>
                                <span class="badge bg-<?= $hColor ?>"><?= esc($history['status']) ?></span>
                                <br>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($history['created_at'])) ?></small>
                                <?php if (!empty($history['note'])): ?>
                                    <p class="mb-0 small"><?= esc($history['note']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <!-- Delivery Proofs -->
            <?php if (!empty($proofs)): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Hinh anh giao hang</h5></div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php foreach ($proofs as $proof): ?>
                            <div class="col-6">
                                <a href="<?= base_url($proof['image_path']) ?>" target="_blank">
                                    <img src="<?= base_url($proof['image_path']) ?>" alt="Proof" class="img-fluid rounded">
                                </a>
                                <?php if (!empty($proof['note'])): ?>
                                    <small class="text-muted"><?= esc($proof['note']) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
