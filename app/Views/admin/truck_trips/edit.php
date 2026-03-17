<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Sua chuyen xe #<?= esc($trip['trip_code'] ?? '') ?></h4>
    <a href="<?= site_url('admin/truck-trips') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= site_url('admin/truck-trips/' . $trip['id'] . '/edit') ?>">
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
                    <input type="text" name="truck_name" class="form-control" value="<?= esc(old('truck_name') ?? $trip['truck_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bien so xe <span class="text-danger">*</span></label>
                    <input type="text" name="plate_number" class="form-control" value="<?= esc(old('plate_number') ?? $trip['plate_number'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tuyen duong <span class="text-danger">*</span></label>
                    <input type="text" name="route" class="form-control" value="<?= esc(old('route') ?? $trip['route'] ?? 'CN-VN') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kho xuat phat</label>
                    <input type="text" name="origin_warehouse" class="form-control" value="<?= esc(old('origin_warehouse') ?? $trip['origin_warehouse'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kho dich</label>
                    <input type="text" name="destination_warehouse" class="form-control" value="<?= esc(old('destination_warehouse') ?? $trip['destination_warehouse'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ngay xep hang</label>
                    <input type="date" name="loading_date" class="form-control" value="<?= esc(old('loading_date') ?? $trip['loading_date'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ngay xuat phat</label>
                    <input type="date" name="departure_date" class="form-control" value="<?= esc(old('departure_date') ?? $trip['departure_date'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Du kien den</label>
                    <input type="date" name="estimated_arrival" class="form-control" value="<?= esc(old('estimated_arrival') ?? $trip['estimated_arrival'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Ghi chu</label>
                    <textarea name="note" class="form-control" rows="3"><?= esc(old('note') ?? $trip['note'] ?? '') ?></textarea>
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Cap nhat</button>
                <a href="<?= site_url('admin/truck-trips') ?>" class="btn btn-secondary">Huy</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
