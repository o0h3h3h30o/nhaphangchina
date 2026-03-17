<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="mb-4">Tong quan</h4>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Tong don</h6>
                            <h3 class="mb-0"><?= esc($totalOrders) ?></h3>
                        </div>
                        <div class="text-primary fs-1"><i class="fas fa-box-seam"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Don dang xu ly</h6>
                            <h3 class="mb-0"><?= esc($pendingOrders) ?></h3>
                        </div>
                        <div class="text-warning fs-1"><i class="fas fa-hourglass-half"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">So du vi</h6>
                            <h3 class="mb-0"><?= number_format($walletBalance, 0, ',', '.') ?> VND</h3>
                        </div>
                        <div class="text-success fs-1"><i class="fas fa-wallet2"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Don hoan thanh</h6>
                            <h3 class="mb-0"><?= esc($completedOrders) ?></h3>
                        </div>
                        <div class="text-info fs-1"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Don hang gan day</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ma don</th>
                            <th>Ten san pham</th>
                            <th>Trang thai</th>
                            <th>Tong phi</th>
                            <th>Ngay tao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentOrders)): ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><a href="<?= site_url('consignments/' . esc($order['id'])) ?>"><?= esc($order['order_code']) ?></a></td>
                                    <td><?= esc($order['product_name']) ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'draft'       => 'secondary',
                                            'submitted'   => 'info',
                                            'received_cn' => 'primary',
                                            'in_transit'  => 'warning',
                                            'received_vn' => 'success',
                                            'completed'   => 'success',
                                            'cancelled'   => 'danger',
                                        ];
                                        $color = $statusColors[$order['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= esc($order['status']) ?></span>
                                    </td>
                                    <td><?= number_format($order['total_fee'], 0, ',', '.') ?> VND</td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chua co don hang nao.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
