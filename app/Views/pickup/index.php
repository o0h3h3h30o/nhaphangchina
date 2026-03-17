<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="mb-4"><i class="fas fa-truck-loading me-2"></i>Yeu cau lay hang</h4>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Create Pickup Request -->
    <?php if (!empty($readyOrders)): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white"><h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tao yeu cau lay hang</h5></div>
        <div class="card-body">
            <form action="<?= site_url('pickup/create') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Chon don hang can lay <span class="text-danger">*</span></label>
                        <select class="form-select" name="consignment_order_id" required>
                            <option value="">-- Chon don hang --</option>
                            <?php foreach ($readyOrders as $ro): ?>
                                <option value="<?= esc($ro['id']) ?>">
                                    <?= esc($ro['order_code']) ?> - <?= esc($ro['product_name'] ?? '') ?>
                                    (<?= number_format($ro['actual_weight'] ?? 0, 2) ?>kg)
                                    <?php if (!empty($ro['total_fee']) && $ro['total_fee'] > 0): ?>
                                        - Phi: <?= number_format($ro['total_fee'], 0, ',', '.') ?> VND
                                    <?php else: ?>
                                        - Phi: Tu dong tinh
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ghi chu</label>
                        <input type="text" class="form-control" name="note" placeholder="Ghi chu them (neu co)">
                    </div>
                </div>

                <hr class="my-3">
                <h6 class="fw-bold mb-3"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Thong tin nguoi nhan</h6>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Ten nguoi nhan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="receiver_name" required placeholder="Ho va ten nguoi nhan">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">So dien thoai <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="receiver_phone" required placeholder="0xxx xxx xxx">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Dia chi giao hang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="receiver_address" required placeholder="So nha, duong, phuong/xa, quan/huyen, tinh/TP">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Tao yeu cau lay hang</button>
                    <small class="text-muted ms-2">Phi van chuyen se duoc tu dong tinh va tru tu vi khi tao yeu cau.</small>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pickup Requests Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sach yeu cau lay hang</h5>
            <span class="badge bg-primary"><?= $total ?? 0 ?> yeu cau</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ma don hang</th>
                            <th>Nguoi nhan</th>
                            <th>SDT</th>
                            <th>Dia chi</th>
                            <th>Trang thai</th>
                            <th>Ngay tao</th>
                            <th>Thao tac</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td>
                                        <a href="<?= site_url('consignments/' . esc($req['consignment_order_id'])) ?>">
                                            <?= esc($req['order_code'] ?? '-') ?>
                                        </a>
                                    </td>
                                    <td><?= esc($req['receiver_name'] ?? '-') ?></td>
                                    <td><?= esc($req['receiver_phone'] ?? '-') ?></td>
                                    <td><small><?= esc(mb_substr($req['receiver_address'] ?? '-', 0, 40)) ?></small></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'requested'  => 'warning',
                                            'confirmed'  => 'info',
                                            'scheduled'  => 'primary',
                                            'picked_up'  => 'success',
                                            'completed'  => 'success',
                                            'cancelled'  => 'danger',
                                            'missed'     => 'secondary',
                                        ];
                                        $statusLabels = [
                                            'requested'  => 'Dang cho',
                                            'confirmed'  => 'Da xac nhan',
                                            'scheduled'  => 'Da hen lich',
                                            'picked_up'  => 'Da lay hang',
                                            'completed'  => 'Hoan thanh',
                                            'cancelled'  => 'Da huy',
                                            'missed'     => 'Bo lo',
                                        ];
                                        $color = $statusColors[$req['status']] ?? 'secondary';
                                        $label = $statusLabels[$req['status']] ?? $req['status'];
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= esc($label) ?></span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></td>
                                    <td>
                                        <?php if ($req['status'] === 'requested'): ?>
                                            <form method="post" action="<?= site_url('pickup/' . esc($req['id']) . '/cancel') ?>" class="d-inline" onsubmit="return confirm('Ban co chac chan muon huy yeu cau nay?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-times-circle me-1"></i>Huy</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Chua co yeu cau lay hang nao.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($pager)): ?>
            <div class="card-footer bg-white">
                <?= $pager ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
