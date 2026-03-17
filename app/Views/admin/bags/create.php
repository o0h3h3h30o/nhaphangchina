<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= site_url('admin/bags') ?>" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 500px;">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-plus-circle me-2 text-success"></i>Tao bao hang moi</h5>
    </div>
    <div class="card-body">
        <form method="post" action="<?= site_url('admin/bags/create') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-bold">Ghi chu (tuy chon)</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Ghi chu ve bao hang..."></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-plus me-1"></i> Tao bao</button>
        </form>
        <p class="text-muted small mt-3 mb-0">Ma bao se duoc tu dong tao. Sau khi tao, ban co the quet kien hang vao bao.</p>
    </div>
</div>
<?= $this->endSection() ?>
