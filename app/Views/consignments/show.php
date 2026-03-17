<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Chi tiet don #<?= esc($order['order_code']) ?></h4>
        <div>
            <?php if ($order['status'] === 'draft'): ?>
                <a href="<?= site_url('consignments/' . esc($order['id']) . '/edit') ?>" class="btn btn-warning"><i class="fas fa-pencil-alt"></i> Chinh sua</a>
            <?php endif; ?>
            <?php if (in_array($order['status'], ['draft', 'submitted'])): ?>
                <a href="<?= site_url('consignments/' . esc($order['id']) . '/cancel') ?>" class="btn btn-danger" onclick="return confirm('Ban co chac chan muon huy don nay?')"><i class="fas fa-times-circle"></i> Huy don</a>
            <?php endif; ?>
            <a href="<?= site_url('consignments') ?>" class="btn btn-outline-secondary">Quay lai</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Order Info -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Thong tin don hang</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><th class="text-muted" width="40%">Ma don:</th><td><?= esc($order['order_code']) ?></td></tr>
                                <tr><th class="text-muted">Ma van don TQ:</th><td><?= esc($order['cn_tracking_code'] ?? '-') ?></td></tr>
                                <tr><th class="text-muted">Ten san pham:</th><td><?= esc($order['product_name']) ?></td></tr>
                                <tr><th class="text-muted">Mo ta:</th><td><?= esc($order['product_description'] ?? '-') ?></td></tr>
                                <?php
                                    $cargoLabels = ['general' => 'Hang thuong', 'hang_lo' => 'Hang lo', 'hang_tmdt' => 'Hang TMDT', 'fragile' => 'Hang de vo', 'bulky' => 'Hang cong kenh', 'special' => 'Hang dac biet'];
                                ?>
                                <tr><th class="text-muted">Loai hang:</th><td><?= esc($cargoLabels[$order['cargo_type'] ?? ''] ?? $order['cargo_type'] ?? '-') ?></td></tr>
                                <tr><th class="text-muted">So kien:</th><td><?= esc($order['package_count'] ?? '-') ?></td></tr>
                                <tr><th class="text-muted">Can nang uoc tinh:</th><td><?= esc($order['estimated_weight'] ?? '-') ?> kg</td></tr>
                                <tr><th class="text-muted">Can nang thuc te:</th><td><?= esc($order['actual_weight'] ?? '-') ?> kg</td></tr>
                                <?php if (!empty($order['package_length'])): ?>
                                <tr><th class="text-muted">Kich thuoc:</th><td><?= $order['package_length'] ?>x<?= $order['package_width'] ?>x<?= $order['package_height'] ?> cm</td></tr>
                                <tr><th class="text-muted">Can quy doi:</th><td><?= number_format($order['volume_weight'] ?? 0, 2) ?> kg <small class="text-muted">(chia <?= number_format($order['volume_divisor'] ?? 6000, 0, ',', '.') ?>)</small></td></tr>
                                <tr><th class="text-muted">Tinh cuoc theo:</th><td class="fw-bold text-primary"><?= number_format($order['chargeable_weight'] ?? $order['actual_weight'] ?? 0, 2) ?> kg</td></tr>
                                <?php elseif (!empty($order['chargeable_weight'])): ?>
                                <tr><th class="text-muted">Tinh cuoc theo:</th><td class="fw-bold"><?= number_format($order['chargeable_weight'], 2) ?> kg</td></tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th class="text-muted" width="40%">Trang thai:</th>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'draft' => 'secondary', 'submitted' => 'info', 'received_cn' => 'primary',
                                            'in_transit' => 'warning', 'received_vn' => 'success', 'completed' => 'success', 'cancelled' => 'danger',
                                        ];
                                        $color = $statusColors[$order['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= esc($order['status']) ?></span>
                                    </td>
                                </tr>
                                <tr><th class="text-muted">Gia tri khai bao:</th><td><?= number_format($order['declared_value'] ?? 0, 0, ',', '.') ?> VND</td></tr>
                                <tr><th class="text-muted">Kho TQ:</th><td><?= esc($order['cn_warehouse'] ?? '-') ?></td></tr>
                                <tr><th class="text-muted">Nguoi nhan:</th><td><?= esc($order['vn_receiver_name'] ?? '-') ?></td></tr>
                                <tr><th class="text-muted">SDT nguoi nhan:</th><td><?= esc($order['vn_receiver_phone'] ?? '-') ?></td></tr>
                                <tr><th class="text-muted">Dia chi:</th><td><?= esc(implode(', ', array_filter([$order['vn_receiver_address'] ?? '', $order['vn_receiver_ward'] ?? '', $order['vn_receiver_district'] ?? '', $order['vn_receiver_city'] ?? '']))) ?></td></tr>
                                <tr><th class="text-muted">Ngay tao:</th><td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td></tr>
                                <tr><th class="text-muted">Ghi chu:</th><td><?= esc($order['note'] ?? '-') ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Packages -->
            <?php if (!empty($packages)): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Danh sach kien hang</h5></div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Ma kien</th>
                                <th>Can nang (kg)</th>
                                <th>Kich thuoc (cm)</th>
                                <th>Ghi chu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($packages as $i => $pkg): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($pkg['package_code'] ?? '-') ?></td>
                                    <td><?= esc($pkg['weight'] ?? '-') ?></td>
                                    <td><?= esc(($pkg['length'] ?? '-') . ' x ' . ($pkg['width'] ?? '-') . ' x ' . ($pkg['height'] ?? '-')) ?></td>
                                    <td><?= esc($pkg['note'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tracking Events -->
            <?php if (!empty($trackingEvents)): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Lich su van chuyen</h5></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($trackingEvents as $event): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong><?= esc($event['location'] ?? '') ?></strong>
                                        <p class="mb-0 text-muted"><?= esc($event['description'] ?? '') ?></p>
                                    </div>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($event['event_time'])) ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Fee Breakdown -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Chi tiet phi</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td>Phi van chuyen:</td><td class="text-end"><?= number_format($order['shipping_fee'] ?? 0, 0, ',', '.') ?> VND</td></tr>
                        <tr><td>Phi dich vu:</td><td class="text-end"><?= number_format($order['service_fee'] ?? 0, 0, ',', '.') ?> VND</td></tr>
                        <tr><td>Phi bao hiem:</td><td class="text-end"><?= number_format($order['insurance_fee'] ?? 0, 0, ',', '.') ?> VND</td></tr>
                        <tr><td>Phi dong go:</td><td class="text-end"><?= number_format($order['packing_fee'] ?? 0, 0, ',', '.') ?> VND</td></tr>
                        <tr><td>Phi khac:</td><td class="text-end"><?= number_format($order['other_fee'] ?? 0, 0, ',', '.') ?> VND</td></tr>
                        <tr class="border-top fw-bold"><td>Tong phi:</td><td class="text-end"><?= number_format($order['total_fee'] ?? 0, 0, ',', '.') ?> VND</td></tr>
                    </table>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Lich su trang thai</h5></div>
                <div class="card-body">
                    <?php if (!empty($statusHistory)): ?>
                        <div class="timeline">
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
                                        <?php
                                        $hColor = $statusColors[$history['to_status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $hColor ?>"><?= esc($history['to_status']) ?></span>
                                        <br>
                                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($history['created_at'])) ?></small>
                                        <?php if (!empty($history['note'])): ?>
                                            <p class="mb-0 small"><?= esc($history['note']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Chua co lich su trang thai.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
