<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= site_url('admin/bags') ?>" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<h4 class="mb-4"><i class="fas fa-plus-circle me-2 text-success"></i>Tao bao hang moi</h4>

<!-- Tabs -->
<ul class="nav nav-tabs mb-0" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tabExcel"><i class="fas fa-file-excel me-1"></i> Upload Excel</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tabManual"><i class="fas fa-keyboard me-1"></i> Nhap tung dong</a>
    </li>
</ul>

<div class="tab-content">
    <!-- TAB 1: Upload Excel -->
    <div class="tab-pane fade show active" id="tabExcel">
        <div class="card border-0 shadow-sm border-top-0 rounded-0 rounded-bottom">
            <div class="card-body">
                <div class="alert alert-info small mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    File Excel gom 4 cot: <strong>Ngay | Ma van don | So kien | Khoi luong (kg)</strong><br>
                    Dong dau la du lieu (khong can tieu de). Neu ma don chua co tren he thong se tu dong them vao kho.
                </div>

                <!-- Step 1: Upload file -->
                <div id="excelStep1">
                    <form id="excelUploadForm" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Chon file Excel <span class="text-danger">*</span></label>
                                <input type="file" name="excel_file" id="excelFile" class="form-control" accept=".xlsx,.xls,.csv" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ghi chu bao (tuy chon)</label>
                                <input type="text" name="note" id="excelNote" class="form-control" placeholder="VD: Hang tmdt 20/3">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-outline-primary" id="btnParse">
                                <i class="fas fa-eye me-1"></i> Doc file & Xem truoc
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 2: Preview + Edit + Submit -->
                <div id="excelStep2" class="d-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-table me-1"></i> Xem truoc du lieu (<span id="previewCount">0</span> dong)</h6>
                        <button class="btn btn-sm btn-outline-secondary" id="btnBackStep1"><i class="fas fa-arrow-left me-1"></i> Chon file khac</button>
                    </div>

                    <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                        <table class="table table-bordered table-sm align-middle mb-0" id="previewTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th style="width:140px">Ngay</th>
                                    <th>Ma van don</th>
                                    <th style="width:80px">So kien</th>
                                    <th style="width:120px">KL (kg)</th>
                                    <th style="width:120px">Trang thai</th>
                                    <th style="width:40px">
                                        <input type="checkbox" id="checkAll" checked title="Chon tat ca">
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-success" id="btnConfirmImport">
                            <i class="fas fa-check me-1"></i> Xac nhan tao bao
                        </button>
                        <span class="text-muted small align-self-center" id="selectedInfo"></span>
                    </div>
                </div>

                <div id="excelResult" class="mt-3 d-none"></div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Nhap tung dong -->
    <div class="tab-pane fade" id="tabManual">
        <div class="card border-0 shadow-sm border-top-0 rounded-0 rounded-bottom">
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Ghi chu bao (tuy chon)</label>
                        <input type="text" id="manualNote" class="form-control" placeholder="Ghi chu...">
                    </div>
                </div>

                <h6 class="fw-bold mb-2"><i class="fas fa-list me-1"></i> Danh sach kien hang</h6>
                <table class="table table-bordered align-middle" id="manualTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:160px">Ngay</th>
                            <th>Ma van don</th>
                            <th style="width:100px">So kien</th>
                            <th style="width:130px">KL (kg)</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="date" class="form-control form-control-sm rDate" value="<?= date('Y-m-d') ?>"></td>
                            <td><input type="text" class="form-control form-control-sm rTracking" placeholder="Ma van don"></td>
                            <td><input type="number" class="form-control form-control-sm rQty" value="1" min="1"></td>
                            <td><input type="number" class="form-control form-control-sm rWeight" step="0.01" min="0.01" placeholder="kg"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow" title="Xoa"><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-secondary mb-3" id="btnAddRow">
                    <i class="fas fa-plus me-1"></i> Them dong
                </button>

                <div>
                    <button type="button" class="btn btn-success" id="btnManualSubmit">
                        <i class="fas fa-save me-1"></i> Tao bao
                    </button>
                </div>

                <div id="manualResult" class="mt-3 d-none"></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function() {
    var parsedData = [];
    var csrfName  = '<?= csrf_token() ?>';
    var csrfHash  = '<?= csrf_hash() ?>';

    // ====== EXCEL TAB ======

    // Step 1: Parse Excel → Preview
    $('#excelUploadForm').on('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this);
        $('#btnParse').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Dang doc...');

        $.ajax({
            url: '<?= site_url('admin/bags/parse-excel') ?>',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.error) {
                    alert(res.error);
                } else {
                    parsedData = res.rows;
                    renderPreview(res.rows);
                    $('#excelStep1').addClass('d-none');
                    $('#excelStep2').removeClass('d-none');
                }
                $('#btnParse').prop('disabled', false).html('<i class="fas fa-eye me-1"></i> Doc file & Xem truoc');
            },
            error: function() {
                alert('Loi ket noi server.');
                $('#btnParse').prop('disabled', false).html('<i class="fas fa-eye me-1"></i> Doc file & Xem truoc');
            }
        });
    });

    function renderPreview(rows) {
        var tbody = $('#previewTable tbody');
        tbody.empty();
        $('#previewCount').text(rows.length);

        for (var i = 0; i < rows.length; i++) {
            var r = rows[i];
            var statusBadge = r.exists
                ? '<span class="badge bg-secondary">Da co</span>'
                : '<span class="badge bg-success">Moi</span>';

            var tr = '<tr>' +
                '<td class="text-muted small">' + (i+1) + '</td>' +
                '<td><input type="date" class="form-control form-control-sm pDate" data-idx="'+i+'" value="' + escAttr(r.date) + '"></td>' +
                '<td><input type="text" class="form-control form-control-sm pTracking" data-idx="'+i+'" value="' + escAttr(r.tracking) + '"></td>' +
                '<td><input type="number" class="form-control form-control-sm pQty" data-idx="'+i+'" value="' + r.qty + '" min="1"></td>' +
                '<td><input type="number" class="form-control form-control-sm pWeight" data-idx="'+i+'" value="' + r.weight + '" step="0.01"></td>' +
                '<td>' + statusBadge + '</td>' +
                '<td><input type="checkbox" class="pCheck" data-idx="'+i+'" checked></td>' +
                '</tr>';
            tbody.append(tr);
        }
        updateSelectedInfo();
    }

    // Check all toggle
    $('#checkAll').on('change', function() {
        var checked = $(this).prop('checked');
        $('.pCheck').prop('checked', checked);
        updateSelectedInfo();
    });
    $(document).on('change', '.pCheck', updateSelectedInfo);

    function updateSelectedInfo() {
        var total = $('.pCheck').length;
        var selected = $('.pCheck:checked').length;
        $('#selectedInfo').text('Da chon ' + selected + '/' + total + ' dong');
    }

    // Back to step 1
    $('#btnBackStep1').on('click', function() {
        $('#excelStep2').addClass('d-none');
        $('#excelStep1').removeClass('d-none');
        $('#excelResult').addClass('d-none');
    });

    // Step 2: Confirm import
    $('#btnConfirmImport').on('click', function() {
        // Thu thập data từ preview table (có thể đã edit)
        var rows = [];
        $('#previewTable tbody tr').each(function() {
            var $tr = $(this);
            if (!$tr.find('.pCheck').prop('checked')) return;
            rows.push({
                date: $tr.find('.pDate').val(),
                tracking: $.trim($tr.find('.pTracking').val()),
                qty: parseInt($tr.find('.pQty').val()) || 1,
                weight: parseFloat($tr.find('.pWeight').val()) || 0
            });
        });

        if (rows.length === 0) {
            alert('Chua chon dong nao.');
            return;
        }

        var postData = {};
        postData[csrfName] = csrfHash;
        postData['note'] = $.trim($('#excelNote').val());
        for (var i = 0; i < rows.length; i++) {
            postData['rows['+i+'][date]']     = rows[i].date;
            postData['rows['+i+'][tracking]'] = rows[i].tracking;
            postData['rows['+i+'][qty]']      = rows[i].qty;
            postData['rows['+i+'][weight]']   = rows[i].weight;
        }

        $('#btnConfirmImport').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Dang tao bao...');

        $.post('<?= site_url('admin/bags/import-manual') ?>', postData, function(res) {
            if (res.error) {
                $('#excelResult').removeClass('d-none').html('<div class="alert alert-danger">' + res.error + '</div>');
            } else {
                var html = '<div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> ' + res.message + '</div>';
                if (res.details) {
                    html += '<div class="small mb-2"><strong>Tong:</strong> ' + res.details.total + ' | <strong>Them moi:</strong> ' + res.details.created + ' | <strong>Da co:</strong> ' + res.details.existing + '</div>';
                }
                if (res.bag_url) {
                    html += '<a href="' + res.bag_url + '" class="btn btn-primary btn-sm"><i class="fas fa-eye me-1"></i> Xem bao</a>';
                }
                $('#excelResult').removeClass('d-none').html(html);
                $('#excelStep2').addClass('d-none');
            }
            $('#btnConfirmImport').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Xac nhan tao bao');
        }).fail(function() {
            $('#excelResult').removeClass('d-none').html('<div class="alert alert-danger">Loi ket noi server.</div>');
            $('#btnConfirmImport').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Xac nhan tao bao');
        });
    });

    // ====== MANUAL TAB ======
    var rowIdx = 1;

    $('#btnAddRow').on('click', function() {
        var row = '<tr>' +
            '<td><input type="date" class="form-control form-control-sm rDate" value="<?= date('Y-m-d') ?>"></td>' +
            '<td><input type="text" class="form-control form-control-sm rTracking" placeholder="Ma van don"></td>' +
            '<td><input type="number" class="form-control form-control-sm rQty" value="1" min="1"></td>' +
            '<td><input type="number" class="form-control form-control-sm rWeight" step="0.01" min="0.01" placeholder="kg"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow"><i class="fas fa-times"></i></button></td>' +
            '</tr>';
        $('#manualTable tbody').append(row);
        $('#manualTable tbody tr:last .rTracking').focus();
    });

    $(document).on('click', '.btnRemoveRow', function() {
        if ($('#manualTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });

    // Enter in weight → add new row
    $(document).on('keypress', '#manualTable .rWeight', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var $tr = $(this).closest('tr');
            if ($tr.is('#manualTable tbody tr:last')) {
                $('#btnAddRow').click();
            }
        }
    });

    $('#btnManualSubmit').on('click', function() {
        var rows = [];
        var valid = true;
        $('#manualTable tbody tr').each(function(i) {
            var $tr = $(this);
            var tracking = $.trim($tr.find('.rTracking').val());
            var weight   = parseFloat($tr.find('.rWeight').val()) || 0;

            if (!tracking) { alert('Dong ' + (i+1) + ': Nhap ma van don.'); valid = false; return false; }
            if (weight <= 0) { alert('Dong ' + (i+1) + ': Nhap khoi luong.'); valid = false; return false; }

            rows.push({
                date: $tr.find('.rDate').val(),
                tracking: tracking,
                qty: parseInt($tr.find('.rQty').val()) || 1,
                weight: weight
            });
        });
        if (!valid || rows.length === 0) return;

        var postData = {};
        postData[csrfName] = csrfHash;
        postData['note'] = $.trim($('#manualNote').val());
        for (var i = 0; i < rows.length; i++) {
            postData['rows['+i+'][date]']     = rows[i].date;
            postData['rows['+i+'][tracking]'] = rows[i].tracking;
            postData['rows['+i+'][qty]']      = rows[i].qty;
            postData['rows['+i+'][weight]']   = rows[i].weight;
        }

        $('#btnManualSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Dang tao...');

        $.post('<?= site_url('admin/bags/import-manual') ?>', postData, function(res) {
            if (res.error) {
                $('#manualResult').removeClass('d-none').html('<div class="alert alert-danger">' + res.error + '</div>');
            } else {
                var html = '<div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> ' + res.message + '</div>';
                if (res.details) {
                    html += '<div class="small mb-2"><strong>Tong:</strong> ' + res.details.total + ' | <strong>Them moi:</strong> ' + res.details.created + ' | <strong>Da co:</strong> ' + res.details.existing + '</div>';
                }
                if (res.bag_url) {
                    html += '<a href="' + res.bag_url + '" class="btn btn-primary btn-sm"><i class="fas fa-eye me-1"></i> Xem bao</a>';
                }
                $('#manualResult').removeClass('d-none').html(html);
            }
            $('#btnManualSubmit').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Tao bao');
        }).fail(function() {
            $('#manualResult').removeClass('d-none').html('<div class="alert alert-danger">Loi ket noi server.</div>');
            $('#btnManualSubmit').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Tao bao');
        });
    });

    function escAttr(s) { return $('<div>').text(s||'').html(); }
});
</script>
<?= $this->endSection() ?>
