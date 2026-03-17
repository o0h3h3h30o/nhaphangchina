<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Chi tiet nap tien #<?= esc($topup['code']) ?></h4>
    <a href="/admin/topups" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Detail Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thong tin nap tien</h6>
                <?php
                    $tBadge = match($topup['status'] ?? '') {
                        'pending' => 'bg-warning text-dark',
                        'approved' => 'bg-success',
                        'rejected' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                    $tLabel = match($topup['status'] ?? '') {
                        'pending' => 'Cho duyet',
                        'approved' => 'Da duyet',
                        'rejected' => 'Tu choi',
                        default => esc($topup['status']),
                    };
                ?>
                <span class="badge <?= $tBadge ?> fs-6"><?= $tLabel ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ma nap</label>
                        <div class="fw-bold"><?= esc($topup['code']) ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">So tien</label>
                        <div class="h4 text-success mb-0"><?= number_format($topup['amount'] ?? 0, 0, ',', '.') ?> VND</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ngan hang</label>
                        <div><?= esc($topup['bank_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Noi dung chuyen khoan</label>
                        <div><?= esc($topup['transfer_content'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ngay tao</label>
                        <div><?= date('d/m/Y H:i', strtotime($topup['created_at'])) ?></div>
                    </div>
                    <?php if (!empty($topup['reject_reason'])): ?>
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Ly do tu choi</label>
                            <div class="text-danger"><?= esc($topup['reject_reason']) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($topup['note'])): ?>
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Ghi chu</label>
                            <div><?= esc($topup['note']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Receipt Image -->
        <?php if (!empty($topup['receipt_image'])): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-image me-2"></i>Hinh anh bien lai</h6>
                </div>
                <div class="card-body text-center">
                    <img src="<?= esc($topup['receipt_image']) ?>" alt="Bien lai" class="img-fluid rounded" style="max-height: 500px;">
                </div>
            </div>
        <?php endif; ?>

        <!-- Actions -->
        <?php if (($topup['status'] ?? '') === 'pending'): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex gap-2">
                    <form method="post" action="/admin/topups/<?= esc($topup['id']) ?>/approve" onsubmit="return confirm('Xac nhan duyet yeu cau nap tien nay?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i> Duyet nap tien</button>
                    </form>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times me-1"></i> Tu choi
                    </button>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post" action="/admin/topups/<?= esc($topup['id']) ?>/reject">
                            <?= csrf_field() ?>
                            <div class="modal-header">
                                <h5 class="modal-title">Tu choi nap tien</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Ly do tu choi</label>
                                    <textarea name="reject_reason" class="form-control" rows="3" required placeholder="Nhap ly do tu choi..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huy</button>
                                <button type="submit" class="btn btn-danger">Tu choi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- User Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Thong tin user</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">User:</td>
                        <td><a href="/admin/users/<?= esc($topup['user_id']) ?>"><?= esc($topup['username'] ?? '-') ?></a></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td><?= esc($topup['user_email'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dien thoai:</td>
                        <td><?= esc($topup['user_phone'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
