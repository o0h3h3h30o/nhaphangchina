<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-warehouse me-2"></i>Kho Trung Quoc</h4>
    <div>
        <span class="badge bg-primary fs-6 me-2">Hom nay: <?= $todayCount ?> kien</span>
        <span class="badge bg-warning text-dark fs-6">Chua dong bao: <?= $unpackedCount ?> kien</span>
    </div>
</div>

<!-- Nhap kien hang -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-plus-circle me-2 text-success"></i>Nhap kien hang</h5>
    </div>
    <div class="card-body">
        <!-- Row 1: Tracking + Weight type -->
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold"><i class="fas fa-barcode me-1"></i> Ma van don TQ</label>
                <input type="text" id="trackingInput" class="form-control form-control-lg" placeholder="Quet hoac nhap ma..." autofocus>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold"><i class="fas fa-balance-scale me-1"></i> Loai tinh can</label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="weightType" id="typeActual" value="actual" checked>
                    <label class="btn btn-outline-primary" for="typeActual"><i class="fas fa-weight me-1"></i> Can thuc (kg)</label>
                    <input type="radio" class="btn-check" name="weightType" id="typeVolume" value="volume">
                    <label class="btn btn-outline-info" for="typeVolume"><i class="fas fa-cube me-1"></i> Theo khoi</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Khach hang (tuy chon)</label>
                <input type="text" id="userSearch" class="form-control" placeholder="Tim user...">
                <input type="hidden" id="userId">
            </div>
        </div>

        <!-- Row 2: Weight + Dimensions -->
        <div class="row g-3 mt-1 align-items-end">
            <div class="col-md-2" id="weightField">
                <label class="form-label fw-bold">Can thuc (kg)</label>
                <input type="number" id="weightInput" class="form-control form-control-lg" step="0.01" min="0" placeholder="0.00">
                <small class="text-muted d-none" id="weightHint">Bo trong = lay can QD</small>
            </div>

            <div id="dimensionFields" class="col-md-4 d-none">
                <label class="form-label fw-bold"><i class="fas fa-ruler-combined me-1"></i> Kich thuoc (cm)</label>
                <div class="input-group input-group-lg">
                    <input type="number" id="lengthInput" class="form-control" step="0.1" placeholder="Dai">
                    <span class="input-group-text">x</span>
                    <input type="number" id="widthInput" class="form-control" step="0.1" placeholder="Rong">
                    <span class="input-group-text">x</span>
                    <input type="number" id="heightInput" class="form-control" step="0.1" placeholder="Cao">
                </div>
            </div>

            <div id="volumeResult" class="col-md-2 d-none">
                <label class="form-label fw-bold text-info">Can QD (kg)</label>
                <div class="form-control form-control-lg bg-light text-center fw-bold" id="volumeWeightDisplay">-</div>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold">Ghi chu</label>
                <input type="text" id="noteInput" class="form-control" placeholder="Ghi chu...">
            </div>

            <div class="col-md-1">
                <button type="button" id="btnSubmit" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-check"></i>
                </button>
            </div>
            <div class="col-md-1">
                <button type="button" id="btnClear" class="btn btn-outline-secondary btn-lg w-100">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>

        <div id="chargeableInfo" class="mt-2 d-none">
            <span class="badge bg-success fs-6"><i class="fas fa-calculator me-1"></i> Tinh cuoc: <span id="chargeableDisplay">-</span> kg</span>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<div id="alertSuccess" class="alert alert-success d-none"><i class="fas fa-check-circle me-2"></i><span id="successMsg"></span></div>
<div id="alertError" class="alert alert-danger d-none"><i class="fas fa-exclamation-circle me-2"></i><span id="errorMsg"></span></div>

<!-- Last received -->
<div id="lastParcel" class="card border-0 shadow-sm mb-4 d-none border-start border-5 border-success">
    <div class="card-body">
        <h6 class="text-success mb-3"><i class="fas fa-check-circle me-2"></i>Kien hang vua nhap</h6>
        <div class="row">
            <div class="col-md-2"><label class="text-muted small">Ma van don</label><div class="fw-bold" id="lastTracking"></div></div>
            <div class="col-md-2"><label class="text-muted small">Can thuc</label><div id="lastWeight"></div></div>
            <div class="col-md-2"><label class="text-muted small">Kich thuoc</label><div id="lastDimensions"></div></div>
            <div class="col-md-2"><label class="text-muted small">Tinh cuoc</label><div class="fw-bold text-primary" id="lastChargeable"></div></div>
            <div class="col-md-2"><label class="text-muted small">Khach hang</label><div id="lastUser"></div></div>
            <div class="col-md-2"><label class="text-muted small">Khop don</label><div id="lastMatch"></div></div>
        </div>
    </div>
</div>

<!-- Filter + Danh sach -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sach kien hang</h5>
        <form class="d-flex gap-2" method="get">
            <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($date) ?>">
            <select name="status" class="form-select form-select-sm" style="width:auto">
                <option value="">Tat ca</option>
                <option value="received" <?= $status === 'received' ? 'selected' : '' ?>>Moi nhap</option>
                <option value="packed" <?= $status === 'packed' ? 'selected' : '' ?>>Da dong bao</option>
                <option value="in_transit" <?= $status === 'in_transit' ? 'selected' : '' ?>>Dang chuyen</option>
                <option value="arrived_vn" <?= $status === 'arrived_vn' ? 'selected' : '' ?>>Da ve VN</option>
            </select>
            <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>
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
                        <th>Can QD</th>
                        <th>Tinh cuoc</th>
                        <th>Bao</th>
                        <th>Khop don</th>
                        <th>Trang thai</th>
                        <th>Thoi gian</th>
                    </tr>
                </thead>
                <tbody id="parcelBody">
                    <?php
                    $statusLabels = [
                        'received'   => ['Moi nhap', 'bg-info'],
                        'packed'     => ['Da dong bao', 'bg-secondary'],
                        'in_transit' => ['Dang chuyen', 'bg-primary'],
                        'arrived_vn' => ['Da ve VN', 'bg-success'],
                        'completed'  => ['Hoan thanh', 'bg-dark'],
                    ];
                    ?>
                    <?php if (!empty($parcels)): ?>
                        <?php foreach ($parcels as $i => $p): ?>
                            <?php $sl = $statusLabels[$p['status']] ?? ['?', 'bg-secondary']; ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><code class="fw-bold"><?= esc($p['cn_tracking_code']) ?></code></td>
                                <td><?= esc($p['user_name'] ?? '-') ?></td>
                                <td><?= number_format($p['weight'], 2) ?></td>
                                <td>
                                    <?php if ($p['length_cm']): ?>
                                        <?= $p['length_cm'] ?>x<?= $p['width_cm'] ?>x<?= $p['height_cm'] ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $p['volume_weight'] ? number_format($p['volume_weight'], 2) : '<span class="text-muted">-</span>' ?></td>
                                <td class="fw-bold"><?= number_format($p['chargeable_weight'], 2) ?></td>
                                <td>
                                    <?php if ($p['bag_code']): ?>
                                        <span class="badge bg-info"><?= esc($p['bag_code']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['consignment_order_id']): ?>
                                        <span class="badge bg-success"><i class="fas fa-link me-1"></i>Co</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Chua</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge <?= $sl[1] ?>"><?= $sl[0] ?></span></td>
                                <td class="small"><?= date('H:i d/m', strtotime($p['received_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="11" class="text-center text-muted py-4">Khong co kien hang nao</td></tr>
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
    var $tracking = $('#trackingInput');
    var $weight   = $('#weightInput');
    var $length   = $('#lengthInput');
    var $width    = $('#widthInput');
    var $height   = $('#heightInput');

    // Toggle weight type
    $('input[name="weightType"]').on('change', function() {
        var isVol = $(this).val() === 'volume';
        $('#dimensionFields').toggleClass('d-none', !isVol);
        $('#volumeResult').toggleClass('d-none', !isVol);
        $('#chargeableInfo').toggleClass('d-none', !isVol);
        $('#weightHint').toggleClass('d-none', !isVol);
        if (!isVol) {
            $length.val(''); $width.val(''); $height.val('');
            $('#volumeWeightDisplay').text('-');
            $('#chargeableDisplay').text('-');
        }
    });

    // Calc volume weight
    $('#lengthInput, #widthInput, #heightInput, #weightInput').on('input', function() {
        if ($('input[name="weightType"]:checked').val() !== 'volume') return;
        var l = parseFloat($length.val()) || 0;
        var w = parseFloat($width.val()) || 0;
        var h = parseFloat($height.val()) || 0;
        var a = parseFloat($weight.val()) || 0;
        if (l > 0 && w > 0 && h > 0) {
            var vw = Math.round((l * w * h) / 6000 * 100) / 100;
            $('#volumeWeightDisplay').text(vw.toFixed(2));
            if (a > 0) {
                $('#chargeableDisplay').text(Math.max(a, vw).toFixed(2));
                $('#chargeableInfo').removeClass('d-none');
            }
        }
    });

    // Enter flows
    $tracking.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            if ($('input[name="weightType"]:checked').val() === 'volume') $length.focus();
            else $weight.focus();
        }
    });
    $weight.on('keypress', function(e) { if (e.which === 13) { e.preventDefault(); doSubmit(); } });
    $height.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            // Nếu có cân thực thì nhảy tới, nếu không thì submit luôn
            if (!$weight.val()) doSubmit();
            else $weight.focus();
        }
    });

    $('#btnSubmit').on('click', doSubmit);
    $('#btnClear').on('click', resetForm);

    function doSubmit() {
        var code   = $.trim($tracking.val());
        var weight = parseFloat($weight.val()) || 0;
        var wType  = $('input[name="weightType"]:checked').val();

        if (!code) { showError('Nhap ma van don.'); $tracking.focus(); return; }

        if (wType === 'volume') {
            var l = parseFloat($length.val()) || 0;
            var w = parseFloat($width.val()) || 0;
            var h = parseFloat($height.val()) || 0;
            if (l <= 0 || w <= 0 || h <= 0) {
                showError('Nhap day du kich thuoc.'); $length.focus(); return;
            }
            // Nếu không nhập cân thực, tự lấy cân quy đổi
            if (weight <= 0) {
                weight = Math.round((l * w * h) / 6000 * 100) / 100;
            }
        } else {
            if (weight <= 0) { showError('Nhap can nang.'); $weight.focus(); return; }
        }

        var post = {
            tracking_code: code, weight: weight, weight_type: wType,
            note: $.trim($('#noteInput').val()),
            user_id: $('#userId').val() || '',
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        };

        if (wType === 'volume') {
            post.length = parseFloat($length.val()) || 0;
            post.width  = parseFloat($width.val()) || 0;
            post.height = parseFloat($height.val()) || 0;
        }

        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.post('<?= site_url('admin/cn-warehouse/receive') ?>', post, function(res) {
            if (res.error) {
                showError(res.error);
                if (res.already_received) setTimeout(resetForm, 1500);
                $('#btnSubmit').prop('disabled', false).html('<i class="fas fa-check"></i>');
                return;
            }

            var p = res.parcel;
            showSuccess(res.message);

            // Show last parcel
            $('#lastTracking').text(p.cn_tracking_code);
            $('#lastWeight').text(parseFloat(p.weight).toFixed(2) + ' kg');
            $('#lastDimensions').html(p.length_cm ? p.length_cm+'x'+p.width_cm+'x'+p.height_cm+' cm' : '<span class="text-muted">-</span>');
            $('#lastChargeable').text(parseFloat(p.chargeable_weight).toFixed(2) + ' kg');
            $('#lastUser').text(p.user_name || '-');
            $('#lastMatch').html(p.matched
                ? '<span class="badge bg-success"><i class="fas fa-link me-1"></i>' + escHtml(p.match_info.order_code) + '</span>'
                : '<span class="badge bg-warning text-dark">Chua khop</span>');
            $('#lastParcel').removeClass('d-none');

            resetForm();
        }).fail(function() {
            showError('Loi ket noi.');
            $('#btnSubmit').prop('disabled', false).html('<i class="fas fa-check"></i>');
        });
    }

    function resetForm() {
        $tracking.val('').focus();
        $weight.val('');
        $length.val(''); $width.val(''); $height.val('');
        $('#noteInput').val('');
        $('#userId').val('');
        $('#userSearch').val('');
        $('#btnSubmit').prop('disabled', false).html('<i class="fas fa-check"></i>');
        $('#volumeWeightDisplay').text('-');
        $('#chargeableDisplay').text('-');
        $('#chargeableInfo').addClass('d-none');
        hideAlerts();
    }

    function showSuccess(msg) { $('#alertError').addClass('d-none'); $('#successMsg').text(msg); $('#alertSuccess').removeClass('d-none'); setTimeout(function(){ $('#alertSuccess').addClass('d-none'); }, 3000); }
    function showError(msg) { $('#alertSuccess').addClass('d-none'); $('#errorMsg').text(msg); $('#alertError').removeClass('d-none'); }
    function hideAlerts() { $('#alertSuccess, #alertError').addClass('d-none'); }
    function escHtml(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
});
</script>
<?= $this->endSection() ?>
