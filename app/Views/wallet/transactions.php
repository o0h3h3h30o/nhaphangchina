<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="mb-4">Lich su giao dich</h4>

    <!-- Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= site_url('wallet/transactions') ?>" method="get" class="row g-3">
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">-- Loai giao dich --</option>
                        <?php
                        $types = ['topup' => 'Nap tien', 'withdrawal' => 'Rut tien', 'payment' => 'Thanh toan', 'refund' => 'Hoan tien', 'fee' => 'Phi'];
                        foreach ($types as $key => $label):
                        ?>
                            <option value="<?= $key ?>" <?= ($type ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="date_from" value="<?= esc($date_from ?? '') ?>" placeholder="Tu ngay">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="date_to" value="<?= esc($date_to ?? '') ?>" placeholder="Den ngay">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2"><i class="fas fa-search"></i> Loc</button>
                    <a href="<?= site_url('wallet/transactions') ?>" class="btn btn-outline-secondary">Dat lai</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ngay</th>
                            <th>Loai</th>
                            <th>So tien</th>
                            <th>So du sau GD</th>
                            <th>Mo ta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $tx): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($tx['created_at'])) ?></td>
                                    <td>
                                        <?php
                                        $typeColors = [
                                            'topup' => 'success', 'withdrawal' => 'danger', 'payment' => 'warning',
                                            'refund' => 'info', 'fee' => 'secondary',
                                        ];
                                        $tColor = $typeColors[$tx['type']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $tColor ?>"><?= esc($tx['type']) ?></span>
                                    </td>
                                    <td class="<?= ($tx['amount'] >= 0) ? 'text-success' : 'text-danger' ?>">
                                        <?= ($tx['amount'] >= 0 ? '+' : '') . number_format($tx['amount'], 0, ',', '.') ?> VND
                                    </td>
                                    <td><?= number_format($tx['balance_after'] ?? 0, 0, ',', '.') ?> VND</td>
                                    <td><?= esc($tx['description'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Khong co giao dich nao.</td>
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
