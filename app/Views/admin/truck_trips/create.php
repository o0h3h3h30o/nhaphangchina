<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Tao chuyen xe moi</h4>
    <a href="<?= site_url('admin/truck-trips') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= site_url('admin/truck-trips/create') ?>">
            <?= csrf_field() ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Ten xe <span class="text-danger">*</span></label>
                    <input type="text" name="truck_name" class="form-control" value="<?= esc(old('truck_name') ?? '') ?>" required placeholder="VD: Xe tai 5 tan">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bien so xe <span class="text-danger">*</span></label>
                    <input type="text" name="plate_number" class="form-control" value="<?= esc(old('plate_number') ?? '') ?>" required placeholder="VD: 29C-12345">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tuyen duong <span class="text-danger">*</span></label>
                    <input type="text" name="route" class="form-control" value="<?= esc(old('route') ?? 'CN-VN') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kho xuat phat</label>
                    <input type="text" name="origin_warehouse" class="form-control" value="<?= esc(old('origin_warehouse') ?? '') ?>" placeholder="VD: Kho Quang Chau">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kho dich</label>
                    <input type="text" name="destination_warehouse" class="form-control" value="<?= esc(old('destination_warehouse') ?? '') ?>" placeholder="VD: Kho Ha Noi">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ngay xep hang</label>
                    <input type="date" name="loading_date" class="form-control" value="<?= esc(old('loading_date') ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ngay xuat phat</label>
                    <input type="date" name="departure_date" class="form-control" value="<?= esc(old('departure_date') ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Du kien den</label>
                    <input type="date" name="estimated_arrival" class="form-control" value="<?= esc(old('estimated_arrival') ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Ghi chu</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="Ghi chu them..."><?= esc(old('note') ?? '') ?></textarea>
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Luu</button>
                <a href="<?= site_url('admin/truck-trips') ?>" class="btn btn-secondary">Huy</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
