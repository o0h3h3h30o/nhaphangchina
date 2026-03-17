<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-minus-circle me-2"></i>Chi tiet rut tien #<?= esc($withdrawal['code']) ?></h4>
    <a href="/admin/withdrawals" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<?php
    $wBadge = match($withdrawal['status'] ?? '') {
        'pending' => 'bg-warning text-dark',
        'approved' => 'bg-info',
        'completed' => 'bg-success',
        'rejected' => 'bg-danger',
        default => 'bg-secondary',
    };
    $wLabel = match($withdrawal['status'] ?? '') {
        'pending' => 'Cho duyet',
        'approved' => 'Da duyet',
        'completed' => 'Hoan thanh',
        'rejected' => 'Tu choi',
        default => esc($withdrawal['status']),
    };
?>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Detail Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thong tin rut tien</h6>
                <span class="badge <?= $wBadge ?> fs-6"><?= $wLabel ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ma rut</label>
                        <div class="fw-bold"><?= esc($withdrawal['code']) ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">So tien</label>
                        <div class="h4 text-danger mb-0"><?= number_format($withdrawal['amount'] ?? 0, 0, ',', '.') ?> VND</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ngay tao</label>
                        <div><?= date('d/m/Y H:i', strtotime($withdrawal['created_at'])) ?></div>
                    </div>
                    <?php if (!empty($withdrawal['processed_at'])): ?>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Ngay xu ly</label>
                            <div><?= date('d/m/Y H:i', strtotime($withdrawal['processed_at'])) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($withdrawal['reason'])): ?>
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Ly do tu choi</label>
                            <div class="text-danger"><?= esc($withdrawal['reason']) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($withdrawal['note'])): ?>
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Ghi chu</label>
                            <div><?= esc($withdrawal['note']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bank Account Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-university me-2"></i>Thong tin tai khoan ngan hang</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ngan hang</label>
                        <div class="fw-bold"><?= esc($withdrawal['bank_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">So tai khoan</label>
                        <div class="fw-bold"><?= esc($withdrawal['bank_account'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Chu tai khoan</label>
                        <div><?= esc($withdrawal['account_holder'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Chi nhanh</label>
                        <div><?= esc($withdrawal['bank_branch'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <?php if (in_array($withdrawal['status'] ?? '', ['pending', 'approved'])): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex gap-2 flex-wrap">
                    <?php if (($withdrawal['status'] ?? '') === 'pending'): ?>
                        <form method="post" action="/admin/withdrawals/<?= esc($withdrawal['id']) ?>/approve" onsubmit="return confirm('Xac nhan duyet yeu cau rut tien nay?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-info text-white"><i class="fas fa-check me-1"></i> Duyet</button>
                        </form>
                    <?php endif; ?>

                    <?php if (($withdrawal['status'] ?? '') === 'approved'): ?>
                        <form method="post" action="/admin/withdrawals/<?= esc($withdrawal['id']) ?>/complete" onsubmit="return confirm('Xac nhan da chuyen tien thanh cong?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success"><i class="fas fa-check-double me-1"></i> Hoan thanh</button>
                        </form>
                    <?php endif; ?>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times me-1"></i> Tu choi
                    </button>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post" action="/admin/withdrawals/<?= esc($withdrawal['id']) ?>/reject">
                            <?= csrf_field() ?>
                            <div class="modal-header">
                                <h5 class="modal-title">Tu choi rut tien</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Ly do tu choi</label>
                                    <textarea name="reason" class="form-control" rows="3" required placeholder="Nhap ly do tu choi..."></textarea>
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
                        <td><a href="/admin/users/<?= esc($withdrawal['user_id']) ?>"><?= esc($withdrawal['username'] ?? '-') ?></a></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td><?= esc($withdrawal['user_email'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dien thoai:</td>
                        <td><?= esc($withdrawal['user_phone'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
