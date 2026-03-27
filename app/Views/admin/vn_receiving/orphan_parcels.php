<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-question-circle me-2 text-warning"></i>Kien hang vo danh <span class="badge bg-warning text-dark"><?= $orphanCount ?></span></h4>
    <a href="<?= site_url('admin/vn-receiving') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai kho VN</a>
</div>

<!-- Search -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="d-flex gap-2 align-items-center" method="get">
            <input type="text" name="search" class="form-control form-control-sm" style="width:300px" placeholder="Tim theo ma van don..." value="<?= esc($search) ?>">
            <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            <?php if ($search): ?>
                <a href="<?= site_url('admin/vn-receiving/orphans') ?>" class="btn btn-sm btn-outline-secondary">Xoa loc</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- List -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Ma van don</th>
                        <th>Can (kg)</th>
                        <th>Kich thuoc</th>
                        <th>Tinh cuoc</th>
                        <th>Bao</th>
                        <th>Trang thai</th>
                        <th>Ngay nhap</th>
                        <th style="min-width:280px">Gan cho user</th>
                    </tr>
                </thead>
                <tbody>
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
                            <tr id="row-<?= $p['id'] ?>">
                                <td><?= $i + 1 ?></td>
                                <td><code class="fw-bold"><?= esc($p['cn_tracking_code']) ?></code></td>
                                <td><?= number_format($p['weight'], 2) ?></td>
                                <td>
                                    <?php if ($p['length_cm']): ?>
                                        <?= $p['length_cm'] ?>x<?= $p['width_cm'] ?>x<?= $p['height_cm'] ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= number_format($p['chargeable_weight'], 2) ?></td>
                                <td>
                                    <?php if (!empty($p['bag_code'])): ?>
                                        <span class="badge bg-info"><?= esc($p['bag_code']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge <?= $sl[1] ?>"><?= $sl[0] ?></span></td>
                                <td class="small"><?= date('H:i d/m', strtotime($p['received_at'])) ?></td>
                                <td>
                                    <div class="input-group input-group-sm assign-group" data-parcel-id="<?= $p['id'] ?>">
                                        <input type="text" class="form-control user-search-input" placeholder="Tim user..." autocomplete="off">
                                        <input type="hidden" class="user-id-input">
                                        <button class="btn btn-success btn-assign" disabled><i class="fas fa-user-plus"></i></button>
                                    </div>
                                    <div class="user-dropdown position-relative">
                                        <div class="user-results list-group position-absolute w-100" style="z-index:100;max-height:200px;overflow-y:auto;display:none;"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">
                            <?= $search ? 'Khong tim thay ket qua' : 'Khong co kien hang vo danh nao' ?>
                        </td></tr>
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
    var searchTimer;

    // User search autocomplete
    $(document).on('input', '.user-search-input', function() {
        var $input = $(this);
        var $group = $input.closest('.assign-group');
        var $results = $input.closest('td').find('.user-results');
        var q = $.trim($input.val());

        $group.find('.user-id-input').val('');
        $group.find('.btn-assign').prop('disabled', true);

        clearTimeout(searchTimer);
        if (q.length < 1) { $results.hide().empty(); return; }

        searchTimer = setTimeout(function() {
            $.get('<?= site_url('admin/vn-receiving/search-users') ?>', { q: q }, function(res) {
                if (!res.users || !res.users.length) {
                    $results.html('<div class="list-group-item text-muted small">Khong tim thay</div>').show();
                    return;
                }
                var html = '';
                res.users.forEach(function(u) {
                    html += '<a href="#" class="list-group-item list-group-item-action py-1 px-2 user-option" data-id="' + u.id + '" data-name="' + escHtml(u.username) + '">' +
                        '<strong>' + escHtml(u.username) + '</strong> <small class="text-muted">' + escHtml(u.email || '') + ' ' + escHtml(u.phone || '') + '</small>' +
                        '</a>';
                });
                $results.html(html).show();
            });
        }, 300);
    });

    // Select user
    $(document).on('click', '.user-option', function(e) {
        e.preventDefault();
        var $this = $(this);
        var $td = $this.closest('td');
        var $group = $td.find('.assign-group');

        $group.find('.user-search-input').val($this.data('name'));
        $group.find('.user-id-input').val($this.data('id'));
        $group.find('.btn-assign').prop('disabled', false);
        $td.find('.user-results').hide();
    });

    // Hide results on click outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.assign-group, .user-dropdown').length) {
            $('.user-results').hide();
        }
    });

    // Assign user
    $(document).on('click', '.btn-assign', function() {
        var $btn = $(this);
        var $group = $btn.closest('.assign-group');
        var parcelId = $group.data('parcel-id');
        var userId = $group.find('.user-id-input').val();

        if (!userId) return;

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.post('<?= site_url('admin/vn-receiving/assign-user') ?>', {
            parcel_id: parcelId,
            user_id: userId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(res) {
            if (res.error) {
                alert(res.error);
                $btn.prop('disabled', false).html('<i class="fas fa-user-plus"></i>');
                return;
            }

            // Thay ô gán bằng tên user
            var $td = $group.closest('td');
            var html = '<span class="badge bg-success"><i class="fas fa-user me-1"></i>' + escHtml(res.username) + '</span>';
            if (res.matched_order) {
                html += ' <span class="badge bg-info ms-1"><i class="fas fa-link me-1"></i>' + escHtml(res.matched_order) + '</span>';
            }
            $td.html(html);
        }).fail(function() {
            alert('Loi ket noi.');
            $btn.prop('disabled', false).html('<i class="fas fa-user-plus"></i>');
        });
    });

    function escHtml(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
});
</script>
<?= $this->endSection() ?>
