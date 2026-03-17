<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Don ky gui</h4>
        <a href="<?= site_url('consignments/create') ?>" class="btn btn-primary"><i class="fas fa-plus-lg"></i> Tao don moi</a>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= site_url('consignments') ?>" method="get" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Tim kiem ma don, ten san pham..." value="<?= esc($search ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Trang thai --</option>
                        <?php
                        $statuses = [
                            'draft'       => 'Nhap',
                            'submitted'   => 'Da gui',
                            'received_cn' => 'Kho TQ nhan',
                            'in_transit'  => 'Dang van chuyen',
                            'received_vn' => 'Kho VN nhan',
                            'completed'   => 'Hoan thanh',
                            'cancelled'   => 'Da huy',
                        ];
                        foreach ($statuses as $key => $label):
                        ?>
                            <option value="<?= $key ?>" <?= ($status ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" placeholder="Tu ngay" value="<?= esc($date_from ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" placeholder="Den ngay" value="<?= esc($date_to ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2"><i class="fas fa-search"></i> Tim kiem</button>
                    <a href="<?= site_url('consignments') ?>" class="btn btn-outline-secondary">Dat lai</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ma don</th>
                            <th>Ma van don TQ</th>
                            <th>Ten san pham</th>
                            <th>Trang thai</th>
                            <th>Can nang (kg)</th>
                            <th>Tong phi</th>
                            <th>Ngay tao</th>
                            <th>Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><a href="<?= site_url('consignments/' . esc($order['id'])) ?>"><?= esc($order['order_code']) ?></a></td>
                                    <td><?= esc($order['cn_tracking_code'] ?? '-') ?></td>
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
                                    <td><?= esc($order['actual_weight'] ?? '-') ?></td>
                                    <td><?= number_format($order['total_fee'] ?? 0, 0, ',', '.') ?> VND</td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= site_url('consignments/' . esc($order['id'])) ?>" class="btn btn-sm btn-outline-primary" title="Xem"><i class="fas fa-eye"></i></a>
                                        <?php if ($order['status'] === 'draft'): ?>
                                            <a href="<?= site_url('consignments/' . esc($order['id']) . '/edit') ?>" class="btn btn-sm btn-outline-warning" title="Sua"><i class="fas fa-pencil-alt"></i></a>
                                        <?php endif; ?>
                                        <?php if (in_array($order['status'], ['draft', 'submitted'])): ?>
                                            <a href="<?= site_url('consignments/' . esc($order['id']) . '/cancel') ?>" class="btn btn-sm btn-outline-danger" title="Huy" onclick="return confirm('Ban co chac chan muon huy don nay?')"><i class="fas fa-times-circle"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Khong co don hang nao.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (isset($pager)): ?>
            <div class="card-footer bg-white">
                <?= $pager ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
