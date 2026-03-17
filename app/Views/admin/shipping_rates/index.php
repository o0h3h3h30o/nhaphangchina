<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-tags me-2"></i>Cau hinh gia cuoc</h4>
    <a href="/admin/shipping-rates/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Them gia moi</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nhom user</th>
                        <th>Tuyen duong</th>
                        <th>Loai hang</th>
                        <th>Gia/kg</th>
                        <th>KL toi thieu</th>
                        <th>Lam tron</th>
                        <th>Phi de vo</th>
                        <th>Phi cong kenh</th>
                        <th>Phi dac biet</th>
                        <th>He so khoi</th>
                        <th>Ap dung tu</th>
                        <th>Trang thai</th>
                        <th class="text-center">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rates)): ?>
                        <?php foreach ($rates as $r): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($r['group_name'])): ?>
                                        <span class="badge bg-info"><?= esc($r['group_name']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Mac dinh</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($r['route'] ?? '-') ?></td>
                                <td><?= esc($r['cargo_type'] ?? '-') ?></td>
                                <td class="fw-bold"><?= number_format($r['rate_per_kg'] ?? 0, 0, ',', '.') ?> VND</td>
                                <td><?= number_format($r['min_weight'] ?? 0, 2) ?> kg</td>
                                <td><?= esc($r['rounding_method'] ?? '-') ?></td>
                                <td><?= number_format($r['extra_fee_fragile'] ?? 0, 0, ',', '.') ?></td>
                                <td><?= number_format($r['extra_fee_bulky'] ?? 0, 0, ',', '.') ?></td>
                                <td><?= number_format($r['extra_fee_special'] ?? 0, 0, ',', '.') ?></td>
                                <td class="fw-bold text-info"><?= number_format($r['volume_divisor'] ?? 6000, 0, ',', '.') ?></td>
                                <td><?= !empty($r['effective_from']) ? date('d/m/Y', strtotime($r['effective_from'])) : '-' ?></td>
                                <td>
                                    <?php if (!empty($r['is_active'])): ?>
                                        <span class="badge bg-success">Hoat dong</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tat</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="/admin/shipping-rates/<?= esc($r['id']) ?>/edit" class="btn btn-sm btn-outline-primary" title="Sua">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="post" action="/admin/shipping-rates/<?= esc($r['id']) ?>/toggle" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-<?= !empty($r['is_active']) ? 'warning' : 'success' ?>" title="<?= !empty($r['is_active']) ? 'Tat' : 'Bat' ?>">
                                            <i class="fas fa-<?= !empty($r['is_active']) ? 'toggle-on' : 'toggle-off' ?>"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">Chua co cau hinh gia cuoc nao</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
