<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-user me-2"></i>Chi tiet user #<?= esc($user['id']) ?></h4>
    <a href="/admin/users" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<div class="row g-4">
    <!-- User Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-id-card me-2"></i>Thong tin tai khoan</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width:80px;height:80px;">
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                    <h5 class="mt-2 mb-0"><?= esc($user['username']) ?></h5>
                    <?php
                        $roleBadge = match($user['role'] ?? 'customer') {
                            'admin' => 'bg-danger',
                            'staff' => 'bg-warning text-dark',
                            default => 'bg-info',
                        };
                    ?>
                    <span class="badge <?= $roleBadge ?> mt-1"><?= esc(ucfirst($user['role'] ?? 'customer')) ?></span>
                </div>
                <hr>
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td><?= esc($user['email']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dien thoai:</td>
                        <td><?= esc($user['phone'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Trang thai:</td>
                        <td>
                            <?php
                                $statusBadge = match($user['status'] ?? 'active') {
                                    'active' => 'bg-success',
                                    'locked' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            ?>
                            <span class="badge <?= $statusBadge ?>">
                                <?= ($user['status'] ?? 'active') === 'active' ? 'Hoat dong' : (($user['status'] ?? '') === 'locked' ? 'Bi khoa' : esc($user['status'])) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nhom:</td>
                        <td>
                            <?php if (!empty($currentGroup)): ?>
                                <span class="badge bg-info"><?= esc($currentGroup['name']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">Chua gan nhom</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ngay tao:</td>
                        <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                    </tr>
                </table>
            </div>
            <div class="card-footer bg-white">
                <?php if (($user['status'] ?? 'active') === 'active'): ?>
                    <form method="post" action="/admin/users/<?= esc($user['id']) ?>/lock" onsubmit="return confirm('Ban co chac muon khoa user nay?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger w-100"><i class="fas fa-lock me-1"></i> Khoa tai khoan</button>
                    </form>
                <?php else: ?>
                    <form method="post" action="/admin/users/<?= esc($user['id']) ?>/unlock" onsubmit="return confirm('Ban co chac muon mo khoa user nay?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-unlock me-1"></i> Mo khoa tai khoan</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Wallet -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-wallet me-2"></i>So du vi</h6>
            </div>
            <div class="card-body text-center">
                <div class="h3 text-primary mb-0">
                    <?= number_format($wallet['balance'] ?? 0, 0, ',', '.') ?> VND
                </div>
                <div class="text-muted small mt-1">So du kha dung</div>
            </div>
        </div>

        <!-- Stats -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Thong ke</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tong don hang:</span>
                    <strong><?= number_format($orderCount ?? 0) ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Tong giao dich:</span>
                    <strong><?= number_format($transactionTotal ?? 0, 0, ',', '.') ?> VND</strong>
                </div>
            </div>
        </div>
    </div>

        <!-- Đổi nhóm user -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>Nhom user</h6>
            </div>
            <div class="card-body">
                <form method="post" action="/admin/users/<?= esc($user['id']) ?>/update-group" class="row g-2 align-items-end">
                    <?= csrf_field() ?>
                    <div class="col-md-8">
                        <select name="user_group_id" class="form-select">
                            <option value="">-- Chua gan nhom (mac dinh) --</option>
                            <?php foreach ($userGroups ?? [] as $ug): ?>
                                <option value="<?= esc($ug['id']) ?>" <?= ($user['user_group_id'] ?? '') == $ug['id'] ? 'selected' : '' ?>>
                                    <?= esc($ug['name']) ?> (<?= esc($ug['code']) ?>)
                                    <?= $ug['is_default'] ? ' - Mac dinh' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-1"></i> Cap nhat</button>
                    </div>
                </form>
            </div>
        </div>

    <!-- Right Column -->
    <div class="col-lg-8">
        <!-- Profile Details -->
        <?php if (!empty($profile)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-address-card me-2"></i>Thong tin ca nhan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Ho ten</label>
                            <div><?= esc($profile['full_name'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Dia chi</label>
                            <div><?= esc($profile['address'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Thanh pho</label>
                            <div><?= esc($profile['city'] ?? '-') ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Quan/Huyen</label>
                            <div><?= esc($profile['district'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- User Flags -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-flag me-2"></i>Co hieu (Flags)</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($flags)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Loai</th>
                                    <th>Ghi chu</th>
                                    <th>Ngay tao</th>
                                    <th class="text-center">Xoa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($flags as $flag): ?>
                                    <tr>
                                        <td>
                                            <?php
                                                $flagColor = match($flag['flag_type'] ?? '') {
                                                    'warning' => 'bg-warning text-dark',
                                                    'danger', 'fraud' => 'bg-danger',
                                                    'vip' => 'bg-success',
                                                    default => 'bg-secondary',
                                                };
                                            ?>
                                            <span class="badge <?= $flagColor ?>"><?= esc($flag['flag_type']) ?></span>
                                        </td>
                                        <td><?= esc($flag['note'] ?? '-') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($flag['created_at'])) ?></td>
                                        <td class="text-center">
                                            <form method="post" action="/admin/users/<?= esc($user['id']) ?>/flags/<?= esc($flag['id']) ?>/delete" class="d-inline" onsubmit="return confirm('Xoa flag nay?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-3">Chua co flag nao.</p>
                <?php endif; ?>

                <!-- Add Flag Form -->
                <form method="post" action="/admin/users/<?= esc($user['id']) ?>/flags" class="row g-2 align-items-end">
                    <?= csrf_field() ?>
                    <div class="col-md-4">
                        <label class="form-label">Loai flag</label>
                        <select name="flag_type" class="form-select" required>
                            <option value="">-- Chon --</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Danger</option>
                            <option value="fraud">Fraud</option>
                            <option value="vip">VIP</option>
                            <option value="note">Note</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Ghi chu</label>
                        <input type="text" name="note" class="form-control" placeholder="Ghi chu...">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus me-1"></i> Them flag</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
