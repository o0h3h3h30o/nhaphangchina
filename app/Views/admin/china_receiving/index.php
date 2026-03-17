<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-warehouse me-2"></i>Nhap hang kho Trung Quoc</h4>
    <span class="badge bg-primary fs-6" id="todayBadge">Hom nay: <?= $todayCount ?? 0 ?> don</span>
</div>

<!-- Scan Form -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <!-- Row 1: Tracking code + Weight type -->
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-bold"><i class="fas fa-barcode me-1"></i> Ma van don Trung Quoc</label>
                <input type="text" id="trackingInput" class="form-control form-control-lg" placeholder="Quet hoac nhap ma van don..." autofocus>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold"><i class="fas fa-balance-scale me-1"></i> Loai tinh can</label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="weightType" id="typeActual" value="actual" checked>
                    <label class="btn btn-outline-primary" for="typeActual"><i class="fas fa-weight me-1"></i> Hang nho (can kg)</label>
                    <input type="radio" class="btn-check" name="weightType" id="typeVolume" value="volume">
                    <label class="btn btn-outline-info" for="typeVolume"><i class="fas fa-cube me-1"></i> Hang lon (theo khoi)</label>
                </div>
            </div>
        </div>

        <!-- Row 2: Weight inputs -->
        <div class="row g-3 mt-1 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold"><i class="fas fa-weight me-1"></i> Can nang thuc (kg) <span class="text-danger">*</span></label>
                <input type="number" id="weightInput" class="form-control form-control-lg" step="0.01" min="0.01" placeholder="0.00" disabled>
            </div>

            <!-- Dimension inputs (hidden by default) -->
            <div id="dimensionFields" class="col-md-5 d-none">
                <label class="form-label fw-bold"><i class="fas fa-ruler-combined me-1"></i> Kich thuoc (cm) <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg">
                    <input type="number" id="lengthInput" class="form-control" step="0.1" min="0.1" placeholder="Dai">
                    <span class="input-group-text">x</span>
                    <input type="number" id="widthInput" class="form-control" step="0.1" min="0.1" placeholder="Rong">
                    <span class="input-group-text">x</span>
                    <input type="number" id="heightInput" class="form-control" step="0.1" min="0.1" placeholder="Cao">
                </div>
            </div>

            <!-- Volume weight display -->
            <div id="volumeResult" class="col-md-2 d-none">
                <label class="form-label fw-bold text-info">Can QD (kg)</label>
                <div class="form-control form-control-lg bg-light text-center fw-bold" id="volumeWeightDisplay">-</div>
            </div>

            <div class="col-md-2">
                <button type="button" id="btnSubmit" class="btn btn-success btn-lg w-100" disabled>
                    <i class="fas fa-check me-1"></i> Nhap kho
                </button>
            </div>
            <div class="col-md-2">
                <button type="button" id="btnClear" class="btn btn-outline-secondary btn-lg w-100">
                    <i class="fas fa-redo me-1"></i> Lam moi
                </button>
            </div>
        </div>

        <!-- Chargeable weight info -->
        <div id="chargeableInfo" class="mt-2 d-none">
            <span class="badge bg-success fs-6"><i class="fas fa-calculator me-1"></i> Tinh cuoc theo: <span id="chargeableDisplay">-</span> kg</span>
            <small class="text-muted ms-2">(Tinh cuoc = max(can thuc, can quy doi))</small>
        </div>

        <!-- Order info after scan -->
        <div id="orderInfo" class="mt-2 d-none">
            <span class="badge bg-secondary fs-6"><i class="fas fa-box me-1"></i> Loai hang: <span id="cargoTypeDisplay">-</span></span>
            <span class="badge bg-info fs-6 ms-1" id="divisorBadge"><i class="fas fa-divide me-1"></i> He so chia: <span id="divisorDisplay">6000</span></span>
        </div>

        <small class="text-muted mt-2 d-block">Nhap ma van don → Enter → Can nang → Kich thuoc (neu hang lon) → Enter de nhap kho</small>
    </div>
</div>

<!-- Alert Messages -->
<div id="alertSuccess" class="alert alert-success d-none">
    <i class="fas fa-check-circle me-2"></i><span id="successMsg"></span>
</div>
<div id="alertError" class="alert alert-danger d-none">
    <i class="fas fa-exclamation-circle me-2"></i><span id="errorMsg"></span>
</div>

<!-- Last Scanned Order Info -->
<div id="lastOrder" class="card border-0 shadow-sm mb-4 d-none border-start border-5 border-success">
    <div class="card-body">
        <h6 class="text-success mb-3"><i class="fas fa-check-circle me-2"></i>Don hang vua nhap</h6>
        <div class="row">
            <div class="col-md-2"><label class="text-muted small">Ma don</label><div class="fw-bold" id="lastOrderCode"></div></div>
            <div class="col-md-2"><label class="text-muted small">Ma van don</label><div id="lastTracking"></div></div>
            <div class="col-md-2"><label class="text-muted small">Can thuc</label><div class="fw-bold" id="lastWeight"></div></div>
            <div class="col-md-2"><label class="text-muted small">Kich thuoc</label><div id="lastDimensions"></div></div>
            <div class="col-md-2"><label class="text-muted small">Tinh cuoc</label><div class="fw-bold text-primary" id="lastChargeable"></div></div>
            <div class="col-md-1"><label class="text-muted small">Khach hang</label><div id="lastUser"></div></div>
            <div class="col-md-1"><label class="text-muted small">Dong go</label><div id="lastWooden"></div></div>
        </div>
    </div>
</div>

<!-- Today's Received Orders -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Don hang da nhap hom nay (<?= $todayCount ?? 0 ?>)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="todayTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Ma don</th>
                        <th>Ma van don TQ</th>
                        <th>Khach hang</th>
                        <th>Can thuc (kg)</th>
                        <th>Kich thuoc (cm)</th>
                        <th>Can QD (kg)</th>
                        <th>Tinh cuoc (kg)</th>
                        <th>Dong go</th>
                        <th>Thoi gian</th>
                    </tr>
                </thead>
                <tbody id="todayBody">
                    <?php if (!empty($todayOrders)): ?>
                        <?php foreach ($todayOrders as $i => $o): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= esc($o['order_code']) ?></strong></td>
                                <td><code><?= esc($o['cn_tracking_code'] ?? '-') ?></code></td>
                                <td><?= esc($o['username'] ?? '-') ?></td>
                                <td><?= number_format($o['actual_weight'] ?? 0, 2) ?></td>
                                <td>
                                    <?php if (!empty($o['package_length'])): ?>
                                        <?= $o['package_length'] ?>x<?= $o['package_width'] ?>x<?= $o['package_height'] ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($o['volume_weight'])): ?>
                                        <?= number_format($o['volume_weight'], 2) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= number_format($o['chargeable_weight'] ?? $o['actual_weight'] ?? 0, 2) ?></td>
                                <td>
                                    <?php if (!empty($o['wooden_crating'])): ?>
                                        <span class="badge bg-warning text-dark">Co</span>
                                    <?php else: ?>
                                        <span class="text-muted">Khong</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('H:i:s', strtotime($o['updated_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="emptyRow">
                            <td colspan="10" class="text-center text-muted py-4">Chua co don hang nao duoc nhap hom nay</td>
                        </tr>
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
    var $tracking  = $('#trackingInput');
    var $weight    = $('#weightInput');
    var $length    = $('#lengthInput');
    var $width     = $('#widthInput');
    var $height    = $('#heightInput');
    var $btnSubmit = $('#btnSubmit');
    var scanCount  = <?= $todayCount ?? 0 ?>;
    var currentDivisor = 6000;
    var currentCargoType = 'general';

    // Toggle weight type
    $('input[name="weightType"]').on('change', function() {
        var isVolume = $(this).val() === 'volume';
        $('#dimensionFields').toggleClass('d-none', !isVolume);
        $('#volumeResult').toggleClass('d-none', !isVolume);
        $('#chargeableInfo').toggleClass('d-none', !isVolume);
        if (!isVolume) {
            $length.val('');
            $width.val('');
            $height.val('');
            $('#volumeWeightDisplay').text('-');
            $('#chargeableDisplay').text('-');
        } else {
            calcVolume();
        }
    });

    // Auto-calculate volume weight on dimension input
    $('#lengthInput, #widthInput, #heightInput, #weightInput').on('input', calcVolume);

    function calcVolume() {
        if ($('input[name="weightType"]:checked').val() !== 'volume') return;
        var l = parseFloat($length.val()) || 0;
        var w = parseFloat($width.val()) || 0;
        var h = parseFloat($height.val()) || 0;
        var actualW = parseFloat($weight.val()) || 0;

        if (l > 0 && w > 0 && h > 0) {
            var vw = (l * w * h) / currentDivisor;
            vw = Math.round(vw * 100) / 100;
            $('#volumeWeightDisplay').text(vw.toFixed(2));
            if (actualW > 0) {
                var chargeable = Math.max(actualW, vw);
                $('#chargeableDisplay').text(chargeable.toFixed(2));
                $('#chargeableInfo').removeClass('d-none');
            }
        } else {
            $('#volumeWeightDisplay').text('-');
            $('#chargeableDisplay').text('-');
        }
    }

    // Tracking input: Enter → lookup order → get divisor → move to weight
    $tracking.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var code = $.trim($tracking.val());
            if (!code) return;

            // Lookup order to get cargo_type, then fetch divisor
            $.post('<?= site_url('admin/consignments/lookup') ?>', {
                tracking_code: code,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            }, function(res) {
                if (res.success && res.order) {
                    currentCargoType = res.order.cargo_type || 'general';
                    var cargoLabels = {
                        'general': 'Hang thuong',
                        'hang_lo': 'Hang lo',
                        'hang_tmdt': 'Hang TMDT',
                        'fragile': 'Hang de vo',
                        'bulky': 'Hang cong kenh',
                        'special': 'Hang dac biet'
                    };
                    $('#cargoTypeDisplay').text(cargoLabels[currentCargoType] || currentCargoType);

                    // Fetch divisor for this cargo_type
                    $.get('<?= site_url('admin/china-receiving/divisor') ?>', {cargo_type: currentCargoType}, function(dRes) {
                        currentDivisor = dRes.divisor || 6000;
                        $('#divisorDisplay').text(currentDivisor.toLocaleString());
                        $('#orderInfo').removeClass('d-none');
                        calcVolume();
                    });
                } else {
                    // Order not found yet, use default
                    $('#orderInfo').addClass('d-none');
                    currentDivisor = 6000;
                }
                $weight.prop('disabled', false).focus();
                $btnSubmit.prop('disabled', false);
            }).fail(function() {
                // Still allow weight entry even if lookup fails
                $weight.prop('disabled', false).focus();
                $btnSubmit.prop('disabled', false);
            });
        }
    });

    // Weight input: Enter → submit (actual) or move to dimensions (volume)
    $weight.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            if ($('input[name="weightType"]:checked').val() === 'volume') {
                $length.focus();
            } else {
                doSubmit();
            }
        }
    });

    // Height input: Enter → submit (last dimension field)
    $height.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            doSubmit();
        }
    });

    $btnSubmit.on('click', doSubmit);

    $('#btnClear').on('click', function() {
        resetForm();
    });

    function doSubmit() {
        var code       = $.trim($tracking.val());
        var weight     = parseFloat($weight.val());
        var weightType = $('input[name="weightType"]:checked').val();

        if (!code) { showError('Vui long nhap ma van don.'); $tracking.focus(); return; }
        if (!weight || weight <= 0) { showError('Vui long nhap can nang hop le.'); $weight.focus(); return; }

        var postData = {
            tracking_code: code,
            weight: weight,
            weight_type: weightType,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        };

        if (weightType === 'volume') {
            var l = parseFloat($length.val()) || 0;
            var w = parseFloat($width.val()) || 0;
            var h = parseFloat($height.val()) || 0;
            if (l <= 0 || w <= 0 || h <= 0) {
                showError('Vui long nhap day du kich thuoc (dai, rong, cao).');
                $length.focus();
                return;
            }
            postData.length = l;
            postData.width  = w;
            postData.height = h;
        }

        $btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Dang xu ly...');

        $.post('<?= site_url('admin/china-receiving/process') ?>', postData, function(res) {
            if (res.error) {
                showError(res.error);
                $btnSubmit.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Nhap kho');
                if (res.already_received) {
                    setTimeout(function() { resetForm(); }, 2000);
                }
                return;
            }

            var o = res.order;
            scanCount++;

            // Show success
            showSuccess(res.message);

            // Show last order info
            $('#lastOrderCode').text(o.order_code);
            $('#lastTracking').text(o.cn_tracking_code);
            $('#lastWeight').text(weight.toFixed(2) + ' kg');

            if (o.package_length) {
                $('#lastDimensions').text(o.package_length + 'x' + o.package_width + 'x' + o.package_height + ' cm');
            } else {
                $('#lastDimensions').html('<span class="text-muted">-</span>');
            }

            var cw = parseFloat(o.chargeable_weight) || weight;
            $('#lastChargeable').text(cw.toFixed(2) + ' kg');

            $('#lastUser').text(o.username || '-');
            $('#lastWooden').html(o.wooden_crating == 1
                ? '<span class="badge bg-warning text-dark">Co</span>'
                : '<span class="text-muted">Khong</span>');
            $('#lastOrder').removeClass('d-none');

            // Add to table
            $('#emptyRow').remove();
            var dimText = o.package_length
                ? o.package_length + 'x' + o.package_width + 'x' + o.package_height
                : '<span class="text-muted">-</span>';
            var vwText = o.volume_weight
                ? parseFloat(o.volume_weight).toFixed(2)
                : '<span class="text-muted">-</span>';

            var row = '<tr class="table-success">' +
                '<td>' + scanCount + '</td>' +
                '<td><strong>' + escHtml(o.order_code) + '</strong></td>' +
                '<td><code>' + escHtml(o.cn_tracking_code) + '</code></td>' +
                '<td>' + escHtml(o.username || '-') + '</td>' +
                '<td>' + weight.toFixed(2) + '</td>' +
                '<td>' + dimText + '</td>' +
                '<td>' + vwText + '</td>' +
                '<td class="fw-bold">' + cw.toFixed(2) + '</td>' +
                '<td>' + (o.wooden_crating == 1 ? '<span class="badge bg-warning text-dark">Co</span>' : '<span class="text-muted">Khong</span>') + '</td>' +
                '<td>' + new Date().toLocaleTimeString('vi-VN') + '</td>' +
                '</tr>';
            $('#todayBody').prepend(row);

            // Update count badge
            $('#todayBadge').text('Hom nay: ' + scanCount + ' don');

            // Reset for next scan
            resetForm();

        }).fail(function() {
            showError('Loi ket noi, vui long thu lai.');
            $btnSubmit.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Nhap kho');
        });
    }

    function resetForm() {
        $tracking.val('').focus();
        $weight.val('').prop('disabled', true);
        $length.val('');
        $width.val('');
        $height.val('');
        $btnSubmit.prop('disabled', true).html('<i class="fas fa-check me-1"></i> Nhap kho');
        $('#volumeWeightDisplay').text('-');
        $('#chargeableDisplay').text('-');
        $('#chargeableInfo').addClass('d-none');
        $('#orderInfo').addClass('d-none');
        currentDivisor = 6000;
        currentCargoType = 'general';
        hideAlerts();
    }

    function showSuccess(msg) {
        $('#alertError').addClass('d-none');
        $('#successMsg').text(msg);
        $('#alertSuccess').removeClass('d-none');
        setTimeout(function() { $('#alertSuccess').addClass('d-none'); }, 3000);
    }

    function showError(msg) {
        $('#alertSuccess').addClass('d-none');
        $('#errorMsg').text(msg);
        $('#alertError').removeClass('d-none');
    }

    function hideAlerts() {
        $('#alertSuccess, #alertError').addClass('d-none');
    }

    function escHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
});
</script>
<?= $this->endSection() ?>
