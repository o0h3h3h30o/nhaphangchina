<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h4>
    <span class="text-muted"><?= date('d/m/Y H:i') ?></span>
</div>

<!-- Stats Row 1 -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Tong user</div>
                        <div class="h3 mb-0 mt-1"><?= number_format($totalUsers ?? 0) ?></div>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-users fa-lg text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Don dang xu ly</div>
                        <div class="h3 mb-0 mt-1"><?= number_format($activeOrders ?? 0) ?></div>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-spinner fa-lg text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Nap tien cho duyet</div>
                        <div class="h3 mb-0 mt-1"><?= number_format($pendingTopups ?? 0) ?></div>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-plus-circle fa-lg text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Rut tien cho duyet</div>
                        <div class="h3 mb-0 mt-1"><?= number_format($pendingWithdrawals ?? 0) ?></div>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-minus-circle fa-lg text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row 2 -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Cho nhap kho TQ</div>
                        <div class="h3 mb-0 mt-1"><?= number_format($waitingCN ?? 0) ?></div>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-warehouse fa-lg text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Dang van chuyen</div>
                        <div class="h3 mb-0 mt-1"><?= number_format($inTransit ?? 0) ?></div>
                    </div>
                    <div class="bg-secondary bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-shipping-fast fa-lg text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Da ve kho VN</div>
                        <div class="h3 mb-0 mt-1"><?= number_format($arrivedVN ?? 0) ?></div>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-check-circle fa-lg text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Cho thanh toan</div>
                        <div class="h3 mb-0 mt-1"><?= number_format($waitingPayment ?? 0) ?></div>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-clock fa-lg text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Thao tac nhanh</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-auto">
                        <a href="/admin/topups?status=pending" class="btn btn-outline-success">
                            <i class="fas fa-plus-circle me-1"></i> Duyet nap tien
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/withdrawals?status=pending" class="btn btn-outline-danger">
                            <i class="fas fa-minus-circle me-1"></i> Duyet rut tien
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/consignments?status=received_cn" class="btn btn-outline-info">
                            <i class="fas fa-box me-1"></i> Don cho xu ly
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/truck_trips/create" class="btn btn-outline-primary">
                            <i class="fas fa-truck me-1"></i> Tao chuyen xe
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/deliveries" class="btn btn-outline-secondary">
                            <i class="fas fa-shipping-fast me-1"></i> Giao hang
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/shipping_rates" class="btn btn-outline-dark">
                            <i class="fas fa-tags me-1"></i> Cau hinh gia
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
