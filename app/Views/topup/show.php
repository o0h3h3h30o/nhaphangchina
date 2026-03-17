<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Chi tiet yeu cau nap tien</h4>
        <a href="<?= site_url('topup') ?>" class="btn btn-outline-secondary">Quay lai</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Thong tin</h5></div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th class="text-muted" width="40%">Ma yeu cau:</th><td><?= esc($topup['code']) ?></td></tr>
                        <tr><th class="text-muted">So tien:</th><td class="fw-bold"><?= number_format($topup['amount'], 0, ',', '.') ?> VND</td></tr>
                        <tr><th class="text-muted">Ngan hang:</th><td><?= esc($topup['bank_name'] ?? '-') ?></td></tr>
                        <tr><th class="text-muted">Noi dung CK:</th><td><?= esc($topup['transfer_content'] ?? '-') ?></td></tr>
                        <tr>
                            <th class="text-muted">Trang thai:</th>
                            <td>
                                <?php
                                $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                                $color = $statusColors[$topup['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= esc($topup['status']) ?></span>
                            </td>
                        </tr>
                        <tr><th class="text-muted">Ngay tao:</th><td><?= date('d/m/Y H:i', strtotime($topup['created_at'])) ?></td></tr>
                        <tr><th class="text-muted">Ngay duyet:</th><td><?= !empty($topup['approved_at']) ? date('d/m/Y H:i', strtotime($topup['approved_at'])) : '-' ?></td></tr>
                        <?php if (!empty($topup['reject_reason'])): ?>
                            <tr><th class="text-muted">Ly do tu choi:</th><td class="text-danger"><?= esc($topup['reject_reason']) ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <?php if (!empty($topup['receipt_image'])): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white"><h5 class="mb-0">Anh hoa don</h5></div>
                    <div class="card-body text-center">
                        <img src="<?= base_url($topup['receipt_image']) ?>" alt="Receipt" class="img-fluid rounded" style="max-height: 400px;">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
