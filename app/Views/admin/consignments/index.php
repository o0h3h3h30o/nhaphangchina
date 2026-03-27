<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-box me-2"></i>Quan ly don ky gui</h4>
</div>

<!-- Scan Tracking Code -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-bold"><i class="fas fa-barcode me-1"></i> Nhap ma van don Trung Quoc</label>
                <div class="input-group input-group-lg">
                    <input type="text" id="trackingCodeInput" class="form-control" placeholder="Quet hoac nhap ma van don..." autofocus>
                    <button class="btn btn-primary" type="button" id="btnLookup"><i class="fas fa-search me-1"></i> Tim</button>
                </div>
                <small class="text-muted">Nhan Enter de tim don hang</small>
            </div>
        </div>
    </div>
</div>

<!-- Lookup Result (hidden by default) -->
<div id="lookupResult" class="d-none">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thong tin don hang</h5>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCloseResult"><i class="fas fa-times"></i> Dong</button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label class="text-muted small">Ma don</label>
                    <div class="fw-bold" id="resOrderCode"></div>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="text-muted small">Ma van don TQ</label>
                    <div id="resTracking"></div>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="text-muted small">Khach hang</label>
                    <div id="resUsername"></div>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="text-muted small">Trang thai</label>
                    <div id="resStatus"></div>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="text-muted small">Mo ta hang</label>
                    <div id="resDescription"></div>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="text-muted small">Dong go</label>
                    <div id="resWoodenCrating"></div>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="text-muted small">Nguoi nhan</label>
                    <div id="resReceiver"></div>
                </div>
            </div>

            <hr>

            <!-- Weight Input Form -->
            <form id="weightForm" method="post">
                <?= csrf_field() ?>
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Can nang thuc te (kg)</label>
                        <input type="number" name="actual_weight" id="inputWeight" class="form-control form-control-lg" step="0.01" min="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ghi chu</label>
                        <input type="text" name="note" class="form-control form-control-lg" placeholder="Ghi chu...">
                    </div>
                    <div class="col-md-3 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-1"></i> Luu can nang</button>
                        <a href="#" id="btnViewDetail" class="btn btn-outline-primary btn-lg"><i class="fas fa-eye"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Error Alert -->
<div id="lookupError" class="alert alert-danger d-none"></div>
<!-- Success Alert -->
<div id="lookupSuccess" class="alert alert-success d-none"></div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/consignments') ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tim kiem</label>
                <input type="text" name="search" class="form-control" placeholder="Ma don, tracking TQ..." value="<?= esc($search ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Trang thai</label>
                <select name="status" class="form-select">
                    <option value="">-- Tat ca --</option>
                    <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Nhap</option>
                    <option value="submitted" <?= ($status ?? '') === 'submitted' ? 'selected' : '' ?>>Da gui</option>
                    <option value="received_cn" <?= ($status ?? '') === 'received_cn' ? 'selected' : '' ?>>Kho TQ</option>
                    <option value="in_transit_cn_vn" <?= ($status ?? '') === 'in_transit_cn_vn' ? 'selected' : '' ?>>Van chuyen</option>
                    <option value="received_vn" <?= ($status ?? '') === 'received_vn' ? 'selected' : '' ?>>Kho VN</option>
                    <option value="fee_calculated" <?= ($status ?? '') === 'fee_calculated' ? 'selected' : '' ?>>Da tinh phi</option>
                    <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>Hoan thanh</option>
                    <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>>Da huy</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Loc</button>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ma don</th>
                        <th>Ma van don TQ</th>
                        <th>Ma KH</th>
                        <th>User</th>
                        <th>Mo ta</th>
                        <th>Trang thai</th>
                        <th>Can nang (kg)</th>
                        <th>Dong go</th>
                        <th>Ngay tao</th>
                        <th class="text-center">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $c): ?>
                            <tr>
                                <td><strong><?= esc($c['order_code']) ?></strong></td>
                                <td><code><?= esc($c['cn_tracking_code'] ?? '-') ?></code></td>
                                <td><span class="badge bg-info">HP<?= esc($c['user_id'] ?? '?') ?></span></td>
                                <td><?= esc($c['username'] ?? '-') ?></td>
                                <td><?= esc(mb_strimwidth($c['product_description'] ?? $c['product_name'] ?? '-', 0, 30, '...')) ?></td>
                                <td>
                                    <?php
                                        $sBadge = match($c['status'] ?? '') {
                                            'draft' => 'bg-light text-dark',
                                            'submitted' => 'bg-info',
                                            'received_cn' => 'bg-primary',
                                            'packed_for_truck' => 'bg-primary',
                                            'in_transit_cn_vn' => 'bg-warning text-dark',
                                            'received_vn' => 'bg-success',
                                            'fee_calculated' => 'bg-info',
                                            'waiting_payment' => 'bg-warning text-dark',
                                            'ready_for_delivery' => 'bg-success',
                                            'delivering' => 'bg-primary',
                                            'completed' => 'bg-dark',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                        $sLabel = match($c['status'] ?? '') {
                                            'draft' => 'Nhap',
                                            'submitted' => 'Da gui',
                                            'received_cn' => 'Kho TQ',
                                            'packed_for_truck' => 'Dong hang',
                                            'in_transit_cn_vn' => 'Van chuyen',
                                            'received_vn' => 'Kho VN',
                                            'fee_calculated' => 'Da tinh phi',
                                            'waiting_payment' => 'Cho TT',
                                            'ready_for_delivery' => 'San sang giao',
                                            'delivering' => 'Dang giao',
                                            'completed' => 'Hoan thanh',
                                            'cancelled' => 'Da huy',
                                            default => esc($c['status']),
                                        };
                                    ?>
                                    <span class="badge <?= $sBadge ?>"><?= $sLabel ?></span>
                                </td>
                                <td><?= $c['actual_weight'] ? number_format($c['actual_weight'], 2) : '<span class="text-muted">-</span>' ?></td>
                                <td>
                                    <?php if (!empty($c['wooden_crating'])): ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-box me-1"></i>Co</span>
                                    <?php else: ?>
                                        <span class="text-muted">Khong</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="<?= site_url('admin/consignments/' . esc($c['id'])) ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiet">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Khong co du lieu</td>
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

<?= $this->section('scripts') ?>
<script>
$(function() {
    var $input = $('#trackingCodeInput');
    var $result = $('#lookupResult');
    var $error = $('#lookupError');
    var $success = $('#lookupSuccess');
    var currentOrderId = null;

    function doLookup() {
        var code = $.trim($input.val());
        if (!code) return;

        $error.addClass('d-none');
        $success.addClass('d-none');
        $result.addClass('d-none');

        $.post('<?= site_url('admin/consignments/lookup') ?>', {
            tracking_code: code,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(res) {
            if (res.error) {
                $error.text(res.error).removeClass('d-none');
                return;
            }
            var o = res.order;
            currentOrderId = o.id;

            $('#resOrderCode').text(o.order_code);
            $('#resTracking').text(o.cn_tracking_code || '-');
            $('#resUsername').text(o.username || '-');
            $('#resDescription').text(o.product_description || o.product_name || '-');
            $('#resReceiver').text(o.vn_receiver_name || '-');
            $('#resWoodenCrating').html(o.wooden_crating == 1
                ? '<span class="badge bg-warning text-dark"><i class="fas fa-box me-1"></i>Co dong go</span>'
                : '<span class="text-muted">Khong</span>');

            var statusMap = {
                'draft': 'Nhap', 'submitted': 'Da gui', 'received_cn': 'Kho TQ',
                'in_transit_cn_vn': 'Van chuyen', 'received_vn': 'Kho VN',
                'fee_calculated': 'Da tinh phi', 'completed': 'Hoan thanh', 'cancelled': 'Da huy'
            };
            $('#resStatus').html('<span class="badge bg-info">' + (statusMap[o.status] || o.status) + '</span>');

            $('#weightForm').attr('action', '<?= site_url('admin/consignments/') ?>' + o.id + '/weight');
            $('#btnViewDetail').attr('href', '<?= site_url('admin/consignments/') ?>' + o.id);

            if (o.actual_weight) {
                $('#inputWeight').val(o.actual_weight);
            } else {
                $('#inputWeight').val('');
            }

            $result.removeClass('d-none');
            $('#inputWeight').focus();
        }).fail(function() {
            $error.text('Loi ket noi, vui long thu lai.').removeClass('d-none');
        });
    }

    // Enter to search
    $input.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            doLookup();
        }
    });

    $('#btnLookup').on('click', doLookup);

    $('#btnCloseResult').on('click', function() {
        $result.addClass('d-none');
        $input.val('').focus();
    });

    // Weight form submit via AJAX
    $('#weightForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        $.post($form.attr('action'), $form.serialize(), function() {
            $success.text('Luu can nang thanh cong!').removeClass('d-none');
            $result.addClass('d-none');
            $input.val('').focus();
            // Reload table after short delay
            setTimeout(function() { location.reload(); }, 1000);
        }).fail(function() {
            $error.text('Loi khi luu can nang.').removeClass('d-none');
        });
    });
});
</script>
<?= $this->endSection() ?>
