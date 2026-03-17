<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-truck me-2"></i>Quan ly chuyen xe</h4>
    <a href="<?= site_url('admin/truck-trips/create') ?>" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tao chuyen xe</a>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/truck-trips') ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Trang thai</label>
                <select name="status" class="form-select">
                    <option value="">-- Tat ca --</option>
                    <option value="draft" <?= ($statusFilter ?? '') === 'draft' ? 'selected' : '' ?>>Nhap</option>
                    <option value="loading" <?= ($statusFilter ?? '') === 'loading' ? 'selected' : '' ?>>Dang xep hang</option>
                    <option value="departed" <?= ($statusFilter ?? '') === 'departed' ? 'selected' : '' ?>>Da xuat phat</option>
                    <option value="border_processing" <?= ($statusFilter ?? '') === 'border_processing' ? 'selected' : '' ?>>Dang thong quan</option>
                    <option value="arrived_vn" <?= ($statusFilter ?? '') === 'arrived_vn' ? 'selected' : '' ?>>Da ve VN</option>
                    <option value="completed" <?= ($statusFilter ?? '') === 'completed' ? 'selected' : '' ?>>Hoan thanh</option>
                    <option value="issue" <?= ($statusFilter ?? '') === 'issue' ? 'selected' : '' ?>>Co van de</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Loc</button>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ma chuyen</th>
                        <th>Ten xe</th>
                        <th>Bien so</th>
                        <th>Tuyen duong</th>
                        <th>Trang thai</th>
                        <th>Ngay xuat phat</th>
                        <th>Du kien den</th>
                        <th class="text-center">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($trips)): ?>
                        <?php foreach ($trips as $t): ?>
                            <tr>
                                <td><strong><?= esc($t['trip_code'] ?? '-') ?></strong></td>
                                <td><?= esc($t['truck_name'] ?? '-') ?></td>
                                <td><?= esc($t['plate_number'] ?? '-') ?></td>
                                <td><?= esc($t['route'] ?? '-') ?></td>
                                <td>
                                    <?php
                                        $sBadge = match($t['status'] ?? '') {
                                            'draft' => 'bg-secondary',
                                            'loading' => 'bg-info',
                                            'departed' => 'bg-primary',
                                            'border_processing' => 'bg-warning text-dark',
                                            'arrived_vn' => 'bg-success',
                                            'completed' => 'bg-success',
                                            'issue' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                        $sLabel = match($t['status'] ?? '') {
                                            'draft' => 'Nhap',
                                            'loading' => 'Dang xep hang',
                                            'departed' => 'Da xuat phat',
                                            'border_processing' => 'Thong quan',
                                            'arrived_vn' => 'Da ve VN',
                                            'completed' => 'Hoan thanh',
                                            'issue' => 'Co van de',
                                            default => esc($t['status']),
                                        };
                                    ?>
                                    <span class="badge <?= $sBadge ?>"><?= $sLabel ?></span>
                                </td>
                                <td><?= !empty($t['departure_date']) ? date('d/m/Y', strtotime($t['departure_date'])) : '-' ?></td>
                                <td><?= !empty($t['estimated_arrival']) ? date('d/m/Y', strtotime($t['estimated_arrival'])) : '-' ?></td>
                                <td class="text-center">
                                    <a href="<?= site_url('admin/truck-trips/' . esc($t['id'])) ?>" class="btn btn-sm btn-outline-primary" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= site_url('admin/truck-trips/' . esc($t['id']) . '/edit') ?>" class="btn btn-sm btn-outline-warning" title="Sua">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Khong co du lieu</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($pager)): ?>
        <div class="card-footer bg-white border-top">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
