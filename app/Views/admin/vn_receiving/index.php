<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-warehouse me-2"></i>Kho Viet Nam - Nhan bao</h4>
    <a href="<?= site_url('admin/vn-receiving/orphans') ?>" class="btn btn-warning">
        <i class="fas fa-question-circle me-1"></i> Kien vo danh
        <?php
        $orphanCount = \Config\Database::connect()->table('cn_warehouse_parcels')->where('user_id IS NULL')->countAllResults();
        if ($orphanCount > 0): ?>
            <span class="badge bg-danger"><?= $orphanCount ?></span>
        <?php endif; ?>
    </a>
</div>

<!-- Quet kien (check nhanh) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-search me-2 text-info"></i>Tra cuu kien hang</h5>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <input type="text" id="scanInput" class="form-control form-control-lg" placeholder="Quet ma van don de tra cuu..." autofocus>
            </div>
            <div class="col-md-2">
                <button id="btnScan" class="btn btn-info btn-lg w-100"><i class="fas fa-search me-1"></i> Tra cuu</button>
            </div>
        </div>
        <div id="scanResult" class="mt-3 d-none"></div>
    </div>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="d-flex gap-2 align-items-center" method="get">
            <label class="fw-bold small text-muted">Loc:</label>
            <select name="status" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                <option value="">Tat ca</option>
                <option value="in_transit" <?= $status === 'in_transit' ? 'selected' : '' ?>>Dang chuyen</option>
                <option value="arrived_vn" <?= $status === 'arrived_vn' ? 'selected' : '' ?>>Da den - Cho do</option>
                <option value="unpacked" <?= $status === 'unpacked' ? 'selected' : '' ?>>Da do bao</option>
            </select>
        </form>
    </div>
</div>

<!-- Bags list -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ma bao</th>
                        <th>So kien</th>
                        <th>Tong can (kg)</th>
                        <th>Xuat kho TQ</th>
                        <th>Den VN</th>
                        <th>Do bao</th>
                        <th>Trang thai</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $statusLabels = [
                        'in_transit' => ['Dang chuyen', 'bg-primary'],
                        'arrived_vn' => ['Da den - Cho do', 'bg-warning text-dark'],
                        'unpacked'   => ['Da do bao', 'bg-success'],
                    ];
                    ?>
                    <?php if (!empty($bags)): ?>
                        <?php foreach ($bags as $b): ?>
                            <?php $sl = $statusLabels[$b['status']] ?? ['?', 'bg-secondary']; ?>
                            <tr>
                                <td class="fw-bold"><?= esc($b['bag_code']) ?></td>
                                <td><?= $b['total_parcels'] ?></td>
                                <td><?= number_format($b['total_weight'], 2) ?></td>
                                <td class="small"><?= $b['departed_at'] ? date('H:i d/m/Y', strtotime($b['departed_at'])) : '-' ?></td>
                                <td class="small"><?= $b['arrived_at'] ? date('H:i d/m/Y', strtotime($b['arrived_at'])) : '-' ?></td>
                                <td class="small"><?= $b['unpacked_at'] ? date('H:i d/m/Y', strtotime($b['unpacked_at'])) : '-' ?></td>
                                <td><span class="badge <?= $sl[1] ?>"><?= $sl[0] ?></span></td>
                                <td>
                                    <?php if ($b['status'] === 'in_transit'): ?>
                                        <form method="post" action="<?= site_url('admin/vn-receiving/' . $b['id'] . '/arrive') ?>"
                                              onsubmit="return confirm('Xac nhan bao <?= esc($b['bag_code']) ?> da den kho VN?')" style="display:inline">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-warning"><i class="fas fa-check me-1"></i> Da den</button>
                                        </form>
                                    <?php elseif ($b['status'] === 'arrived_vn'): ?>
                                        <form method="post" action="<?= site_url('admin/vn-receiving/' . $b['id'] . '/unpack') ?>"
                                              onsubmit="return confirm('Do bao <?= esc($b['bag_code']) ?>? Tat ca <?= $b['total_parcels'] ?> kien se chuyen trang thai da ve VN.')" style="display:inline">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-success"><i class="fas fa-box-open me-1"></i> Do bao</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">Da hoan thanh</span>
                                    <?php endif; ?>

                                    <a href="<?= site_url('admin/bags/' . $b['id']) ?>" class="btn btn-sm btn-outline-primary ms-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Chua co bao hang nao</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function() {
    var $scan = $('#scanInput');

    $scan.on('keypress', function(e) { if (e.which === 13) { e.preventDefault(); doScan(); } });
    $('#btnScan').on('click', doScan);

    function doScan() {
        var code = $.trim($scan.val());
        if (!code) return;

        $('#btnScan').prop('disabled', true);

        $.post('<?= site_url('admin/vn-receiving/scan') ?>', {
            tracking_code: code,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(res) {
            var $r = $('#scanResult').removeClass('d-none');

            if (res.error) {
                $r.html('<div class="alert alert-danger mb-0"><i class="fas fa-times-circle me-2"></i>' + escHtml(res.error) + '</div>');
            } else {
                var p = res.parcel;
                var html = '<div class="card border-start border-5 border-info">' +
                    '<div class="card-body">' +
                    '<div class="row">' +
                    '<div class="col-md-2"><label class="text-muted small">Ma van don</label><div class="fw-bold">' + escHtml(p.cn_tracking_code) + '</div></div>' +
                    '<div class="col-md-2"><label class="text-muted small">Tinh cuoc</label><div class="fw-bold">' + parseFloat(p.chargeable_weight).toFixed(2) + ' kg</div></div>' +
                    '<div class="col-md-2"><label class="text-muted small">Bao</label><div>' + escHtml(p.bag_code || '-') + '</div></div>' +
                    '<div class="col-md-2"><label class="text-muted small">Khach hang</label><div>' + escHtml(p.user_name || '-') + '</div></div>' +
                    '<div class="col-md-2"><label class="text-muted small">Trang thai kien</label><div><span class="badge bg-info">' + escHtml(p.status) + '</span></div></div>';

                if (res.order) {
                    html += '<div class="col-md-2"><label class="text-muted small">Don ky gui</label><div><span class="badge bg-success">' + escHtml(res.order.order_code) + '</span> <small>' + escHtml(res.order.status) + '</small></div></div>';
                } else {
                    html += '<div class="col-md-2"><label class="text-muted small">Don ky gui</label><div><span class="badge bg-warning text-dark">Chua khop</span></div></div>';
                }

                html += '</div></div></div>';
                $r.html(html);
            }

            $scan.val('').focus();
            $('#btnScan').prop('disabled', false);
        }).fail(function() {
            $('#scanResult').removeClass('d-none').html('<div class="alert alert-danger mb-0">Loi ket noi.</div>');
            $('#btnScan').prop('disabled', false);
        });
    }

    function escHtml(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
});
</script>
<?= $this->endSection() ?>
