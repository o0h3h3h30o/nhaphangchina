<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="mb-4">Danh sach giao hang</h4>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ma giao hang</th>
                            <th>Ma don hang</th>
                            <th>Nguoi nhan</th>
                            <th>Trang thai</th>
                            <th>Ngay hen giao</th>
                            <th>Ngay giao</th>
                            <th>Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($deliveries)): ?>
                            <?php foreach ($deliveries as $d): ?>
                                <tr>
                                    <td><?= esc($d['delivery_code']) ?></td>
                                    <td><a href="<?= site_url('consignments/' . esc($d['order_id'] ?? '')) ?>"><?= esc($d['order_code'] ?? '-') ?></a></td>
                                    <td><?= esc($d['receiver_name'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning', 'assigned' => 'info', 'in_transit' => 'primary',
                                            'delivered' => 'success', 'failed' => 'danger',
                                        ];
                                        $color = $statusColors[$d['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= esc($d['status']) ?></span>
                                    </td>
                                    <td><?= !empty($d['scheduled_date']) ? date('d/m/Y', strtotime($d['scheduled_date'])) : '-' ?></td>
                                    <td><?= !empty($d['delivered_at']) ? date('d/m/Y H:i', strtotime($d['delivered_at'])) : '-' ?></td>
                                    <td>
                                        <a href="<?= site_url('deliveries/' . esc($d['id'])) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Chua co don giao hang nao.</td>
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
