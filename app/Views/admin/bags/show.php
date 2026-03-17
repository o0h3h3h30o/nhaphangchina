<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= site_url('admin/bags') ?>" class="text-decoration-none small"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
        <h4 class="mb-0 mt-2"><i class="fas fa-box-open me-2"></i>Bao <?= esc($bag['bag_code']) ?></h4>
    </div>
    <div>
        <?php
        $statusLabels = [
            'packing'    => ['Dang dong', 'bg-warning text-dark'],
            'sealed'     => ['Da niem phong', 'bg-info'],
            'in_transit' => ['Dang chuyen', 'bg-primary'],
            'arrived_vn' => ['Da den VN', 'bg-success'],
            'unpacked'   => ['Da do bao', 'bg-dark'],
        ];
        $sl = $statusLabels[$bag['status']] ?? ['?', 'bg-secondary'];
        ?>
        <span class="badge <?= $sl[1] ?> fs-6"><?= $sl[0] ?></span>
    </div>
</div>

<!-- Thong tin bao -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="text-muted small">So kien</div>
                        <div class="fs-3 fw-bold text-primary"><?= $bag['total_parcels'] ?></div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small">Tong can (kg)</div>
                        <div class="fs-3 fw-bold"><?= number_format($bag['total_weight'], 2) ?></div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small">Niem phong</div>
                        <div class="small fw-bold"><?= $bag['sealed_at'] ? date('H:i d/m/Y', strtotime($bag['sealed_at'])) : '-' ?></div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small">Xuat kho</div>
                        <div class="small fw-bold"><?= $bag['departed_at'] ? date('H:i d/m/Y', strtotime($bag['departed_at'])) : '-' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?php if ($bag['status'] === 'packing'): ?>
                    <form method="post" action="<?= site_url('admin/bags/' . $bag['id'] . '/seal') ?>" onsubmit="return confirm('Niem phong bao? Sau khi niem phong se khong them/bo kien duoc.')">
                        <?= csrf_field() ?>
                        <button class="btn btn-info w-100 mb-2"><i class="fas fa-lock me-1"></i> Niem phong bao</button>
                    </form>
                <?php endif; ?>

                <?php if ($bag['status'] === 'sealed'): ?>
                    <form method="post" action="<?= site_url('admin/bags/' . $bag['id'] . '/depart') ?>" onsubmit="return confirm('Xuat kho bao nay? Tat ca don hang lien quan se chuyen trang thai van chuyen.')">
                        <?= csrf_field() ?>
                        <button class="btn btn-primary w-100 mb-2"><i class="fas fa-truck me-1"></i> Xuat kho</button>
                    </form>
                <?php endif; ?>

                <?php if ($bag['note']): ?>
                    <div class="text-muted small mt-2"><strong>Ghi chu:</strong> <?= esc($bag['note']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quet kien vao bao (chi khi status = packing) -->
<?php if ($bag['status'] === 'packing'): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-barcode me-2 text-success"></i>Quet kien hang vao bao</h5>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <input type="text" id="scanInput" class="form-control form-control-lg" placeholder="Quet ma van don..." autofocus>
            </div>
            <div class="col-md-4">
                <button id="btnScan" class="btn btn-success btn-lg w-100"><i class="fas fa-plus me-1"></i> Them vao bao</button>
            </div>
        </div>
        <div id="scanAlert" class="mt-2 d-none"></div>
    </div>
</div>
<?php endif; ?>

<!-- Danh sach kien trong bao -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Kien hang trong bao (<?= count($parcels) ?>)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Ma van don</th>
                        <th>Khach hang</th>
                        <th>Can (kg)</th>
                        <th>Kich thuoc</th>
                        <th>Tinh cuoc (kg)</th>
                        <th>Khop don</th>
                        <?php if ($bag['status'] === 'packing'): ?><th>Thao tac</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody id="parcelList">
                    <?php if (!empty($parcels)): ?>
                        <?php foreach ($parcels as $i => $p): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><code class="fw-bold"><?= esc($p['cn_tracking_code']) ?></code></td>
                                <td><?= esc($p['user_name'] ?? '-') ?></td>
                                <td><?= number_format($p['weight'], 2) ?></td>
                                <td>
                                    <?= $p['length_cm'] ? $p['length_cm'].'x'.$p['width_cm'].'x'.$p['height_cm'] : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td class="fw-bold"><?= number_format($p['chargeable_weight'], 2) ?></td>
                                <td>
                                    <?php if ($p['consignment_order_id']): ?>
                                        <span class="badge bg-success"><i class="fas fa-link me-1"></i>Co</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Chua</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($bag['status'] === 'packing'): ?>
                                <td>
                                    <form method="post" action="<?= site_url('admin/bags/' . $bag['id'] . '/remove-parcel/' . $p['id']) ?>"
                                          onsubmit="return confirm('Bo kien nay khoi bao?')" style="display:inline">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="emptyRow"><td colspan="8" class="text-center text-muted py-4">Chua co kien hang nao trong bao</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?php if ($bag['status'] === 'packing'): ?>
<?= $this->section('scripts') ?>
<script>
$(function() {
    var $scan = $('#scanInput');
    var parcelCount = <?= count($parcels) ?>;

    $scan.on('keypress', function(e) {
        if (e.which === 13) { e.preventDefault(); doScan(); }
    });
    $('#btnScan').on('click', doScan);

    function doScan() {
        var code = $.trim($scan.val());
        if (!code) return;

        $('#btnScan').prop('disabled', true);

        $.post('<?= site_url('admin/bags/' . $bag['id'] . '/add-parcel') ?>', {
            tracking_code: code,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(res) {
            if (res.error) {
                showAlert('danger', res.error);
            } else {
                showAlert('success', res.message);
                parcelCount++;
                $('#emptyRow').remove();

                var p = res.parcel;
                var matchBadge = p.matched
                    ? '<span class="badge bg-success"><i class="fas fa-link me-1"></i>Co</span>'
                    : '<span class="badge bg-warning text-dark">Chua</span>';

                var row = '<tr class="table-success">' +
                    '<td>' + parcelCount + '</td>' +
                    '<td><code class="fw-bold">' + escHtml(p.cn_tracking_code) + '</code></td>' +
                    '<td>' + escHtml(p.user_name || '-') + '</td>' +
                    '<td>' + parseFloat(p.weight).toFixed(2) + '</td>' +
                    '<td>-</td>' +
                    '<td class="fw-bold">' + parseFloat(p.chargeable_weight).toFixed(2) + '</td>' +
                    '<td>' + matchBadge + '</td>' +
                    '<td><button class="btn btn-sm btn-outline-danger" disabled><i class="fas fa-times"></i></button></td>' +
                    '</tr>';
                $('#parcelList').prepend(row);
            }

            $scan.val('').focus();
            $('#btnScan').prop('disabled', false);
        }).fail(function() {
            showAlert('danger', 'Loi ket noi.');
            $('#btnScan').prop('disabled', false);
        });
    }

    function showAlert(type, msg) {
        $('#scanAlert').removeClass('d-none alert-success alert-danger')
            .addClass('alert alert-' + type).text(msg);
        if (type === 'success') setTimeout(function(){ $('#scanAlert').addClass('d-none'); }, 2000);
    }

    function escHtml(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>
