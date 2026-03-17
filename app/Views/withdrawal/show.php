<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Chi tiet yeu cau rut tien</h4>
        <a href="<?= site_url('withdrawal') ?>" class="btn btn-outline-secondary">Quay lai</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Thong tin</h5></div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th class="text-muted" width="40%">Ma yeu cau:</th><td><?= esc($withdrawal['code']) ?></td></tr>
                        <tr><th class="text-muted">So tien:</th><td class="fw-bold"><?= number_format($withdrawal['amount'], 0, ',', '.') ?> VND</td></tr>
                        <tr><th class="text-muted">Ngan hang:</th><td><?= esc($withdrawal['bank_name'] ?? '-') ?></td></tr>
                        <tr><th class="text-muted">So tai khoan:</th><td><?= esc($withdrawal['account_number'] ?? '-') ?></td></tr>
                        <tr><th class="text-muted">Chu tai khoan:</th><td><?= esc($withdrawal['account_holder'] ?? '-') ?></td></tr>
                        <tr>
                            <th class="text-muted">Trang thai:</th>
                            <td>
                                <?php
                                $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'completed' => 'success'];
                                $color = $statusColors[$withdrawal['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= esc($withdrawal['status']) ?></span>
                            </td>
                        </tr>
                        <tr><th class="text-muted">Ngay tao:</th><td><?= date('d/m/Y H:i', strtotime($withdrawal['created_at'])) ?></td></tr>
                        <tr><th class="text-muted">Ngay duyet:</th><td><?= !empty($withdrawal['approved_at']) ? date('d/m/Y H:i', strtotime($withdrawal['approved_at'])) : '-' ?></td></tr>
                        <?php if (!empty($withdrawal['reject_reason'])): ?>
                            <tr><th class="text-muted">Ly do tu choi:</th><td class="text-danger"><?= esc($withdrawal['reject_reason']) ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
