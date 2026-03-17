<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Yeu cau rut tien</h4>
        <a href="<?= site_url('withdrawal/create') ?>" class="btn btn-primary"><i class="fas fa-plus-lg"></i> Rut tien</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ma yeu cau</th>
                            <th>So tien</th>
                            <th>Ngan hang</th>
                            <th>So tai khoan</th>
                            <th>Trang thai</th>
                            <th>Ngay tao</th>
                            <th>Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($withdrawals)): ?>
                            <?php foreach ($withdrawals as $wd): ?>
                                <tr>
                                    <td><?= esc($wd['code']) ?></td>
                                    <td><?= number_format($wd['amount'], 0, ',', '.') ?> VND</td>
                                    <td><?= esc($wd['bank_name'] ?? '-') ?></td>
                                    <td><?= esc($wd['account_number'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'completed' => 'success'];
                                        $color = $statusColors[$wd['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= esc($wd['status']) ?></span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($wd['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= site_url('withdrawal/' . esc($wd['id'])) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Chua co yeu cau rut tien nao.</td>
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
