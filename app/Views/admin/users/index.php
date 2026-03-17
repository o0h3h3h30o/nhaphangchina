<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-users me-2"></i>Quan ly user</h4>
</div>

<!-- Search/Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="/admin/users" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tim kiem</label>
                <input type="text" name="search" class="form-control" placeholder="Ten, email, so dien thoai..." value="<?= esc($search ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Vai tro</label>
                <select name="role" class="form-select">
                    <option value="">-- Tat ca --</option>
                    <option value="customer" <?= ($role ?? '') === 'customer' ? 'selected' : '' ?>>Customer</option>
                    <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="staff" <?= ($role ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Trang thai</label>
                <select name="status" class="form-select">
                    <option value="">-- Tat ca --</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Hoat dong</option>
                    <option value="locked" <?= ($status ?? '') === 'locked' ? 'selected' : '' ?>>Bi khoa</option>
                    <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Khong hoat dong</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Loc</button>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>So dien thoai</th>
                        <th>Vai tro</th>
                        <th>Trang thai</th>
                        <th>Ngay tao</th>
                        <th class="text-center">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= esc($u['id']) ?></td>
                                <td><strong><?= esc($u['username']) ?></strong></td>
                                <td><?= esc($u['email']) ?></td>
                                <td><?= esc($u['phone'] ?? '-') ?></td>
                                <td>
                                    <?php
                                        $roleBadge = match($u['role'] ?? 'customer') {
                                            'admin' => 'bg-danger',
                                            'staff' => 'bg-warning text-dark',
                                            default => 'bg-info',
                                        };
                                    ?>
                                    <span class="badge <?= $roleBadge ?>"><?= esc(ucfirst($u['role'] ?? 'customer')) ?></span>
                                </td>
                                <td>
                                    <?php
                                        $statusBadge = match($u['status'] ?? 'active') {
                                            'active' => 'bg-success',
                                            'locked' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $statusBadge ?>">
                                        <?= $u['status'] === 'active' ? 'Hoat dong' : ($u['status'] === 'locked' ? 'Bi khoa' : esc($u['status'])) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="/admin/users/<?= esc($u['id']) ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiet">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (($u['status'] ?? 'active') === 'active'): ?>
                                        <form method="post" action="/admin/users/<?= esc($u['id']) ?>/lock" class="d-inline" onsubmit="return confirm('Ban co chac muon khoa user nay?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Khoa">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post" action="/admin/users/<?= esc($u['id']) ?>/unlock" class="d-inline" onsubmit="return confirm('Ban co chac muon mo khoa user nay?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Mo khoa">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
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
