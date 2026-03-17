<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-box-open me-2"></i>Quan ly bao hang</h4>
    <a href="<?= site_url('admin/bags/create') ?>" class="btn btn-success"><i class="fas fa-plus me-1"></i> Tao bao moi</a>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="d-flex gap-2 align-items-center" method="get">
            <label class="fw-bold small text-muted">Trang thai:</label>
            <select name="status" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                <option value="">Tat ca</option>
                <option value="packing" <?= $status === 'packing' ? 'selected' : '' ?>>Dang dong</option>
                <option value="sealed" <?= $status === 'sealed' ? 'selected' : '' ?>>Da niem phong</option>
                <option value="in_transit" <?= $status === 'in_transit' ? 'selected' : '' ?>>Dang chuyen</option>
                <option value="arrived_vn" <?= $status === 'arrived_vn' ? 'selected' : '' ?>>Da den VN</option>
                <option value="unpacked" <?= $status === 'unpacked' ? 'selected' : '' ?>>Da do bao</option>
            </select>
        </form>
    </div>
</div>

<!-- Bags list -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ma bao</th>
                        <th>So kien</th>
                        <th>Tong can (kg)</th>
                        <th>Trang thai</th>
                        <th>Nguoi dong</th>
                        <th>Niem phong</th>
                        <th>Xuat kho</th>
                        <th>Den VN</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $statusLabels = [
                        'packing'    => ['Dang dong', 'bg-warning text-dark'],
                        'sealed'     => ['Da niem phong', 'bg-info'],
                        'in_transit' => ['Dang chuyen', 'bg-primary'],
                        'arrived_vn' => ['Da den VN', 'bg-success'],
                        'unpacked'   => ['Da do bao', 'bg-dark'],
                    ];
                    ?>
                    <?php if (!empty($bags)): ?>
                        <?php foreach ($bags as $b): ?>
                            <?php $sl = $statusLabels[$b['status']] ?? ['?', 'bg-secondary']; ?>
                            <tr>
                                <td><a href="<?= site_url('admin/bags/' . $b['id']) ?>" class="fw-bold text-decoration-none"><?= esc($b['bag_code']) ?></a></td>
                                <td class="fw-bold"><?= $b['total_parcels'] ?></td>
                                <td><?= number_format($b['total_weight'], 2) ?></td>
                                <td><span class="badge <?= $sl[1] ?>"><?= $sl[0] ?></span></td>
                                <td><?= esc($b['packed_by_name'] ?? '-') ?></td>
                                <td class="small"><?= $b['sealed_at'] ? date('H:i d/m', strtotime($b['sealed_at'])) : '-' ?></td>
                                <td class="small"><?= $b['departed_at'] ? date('H:i d/m', strtotime($b['departed_at'])) : '-' ?></td>
                                <td class="small"><?= $b['arrived_at'] ? date('H:i d/m', strtotime($b['arrived_at'])) : '-' ?></td>
                                <td>
                                    <a href="<?= site_url('admin/bags/' . $b['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Chua co bao hang nao</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
