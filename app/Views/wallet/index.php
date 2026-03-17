<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="mb-4">Vi cua toi</h4>

    <!-- Balance Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="opacity-75">So du</h6>
                    <h3 class="mb-0"><?= number_format($wallet['balance'] ?? 0, 0, ',', '.') ?> VND</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <h6 class="opacity-75">Tam giu</h6>
                    <h3 class="mb-0"><?= number_format($wallet['locked_balance'] ?? 0, 0, ',', '.') ?> VND</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6 class="opacity-75">Kha dung</h6>
                    <h3 class="mb-0"><?= number_format(($wallet['balance'] ?? 0) - ($wallet['locked_balance'] ?? 0), 0, ',', '.') ?> VND</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="mb-4">
        <a href="<?= site_url('topup/create') ?>" class="btn btn-primary me-2"><i class="fas fa-plus-circle"></i> Nap tien</a>
        <a href="<?= site_url('withdrawal/create') ?>" class="btn btn-outline-primary"><i class="fas fa-arrow-circle-up"></i> Rut tien</a>
    </div>

    <!-- Recent Transactions -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Giao dich gan day</h5>
            <a href="<?= site_url('wallet/transactions') ?>" class="btn btn-sm btn-outline-primary">Xem tat ca</a>
        </div>
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
                                <td colspan="5" class="text-center text-muted py-4">Chua co giao dich nao.</td>
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
