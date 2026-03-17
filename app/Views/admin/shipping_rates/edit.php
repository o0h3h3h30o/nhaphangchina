<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Sua gia cuoc</h4>
    <a href="/admin/shipping-rates" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= site_url('admin/shipping-rates/' . $rate['id'] . '/edit') ?>">
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
                <div class="col-md-12">
                    <label class="form-label">Nhom user</label>
                    <?php $groupVal = old('user_group_id') ?? ($rate['user_group_id'] ?? ''); ?>
                    <select name="user_group_id" class="form-select">
                        <option value="">-- Mac dinh (tat ca nhom) --</option>
                        <?php foreach ($userGroups ?? [] as $ug): ?>
                            <option value="<?= esc($ug['id']) ?>" <?= $groupVal == $ug['id'] ? 'selected' : '' ?>>
                                <?= esc($ug['name']) ?> (<?= esc($ug['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">De trong = gia mac dinh ap dung cho tat ca nhom. Chon nhom = gia rieng cho nhom do.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tuyen duong <span class="text-danger">*</span></label>
                    <input type="text" name="route" class="form-control" value="<?= esc(old('route') ?? $rate['route'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Loai hang <span class="text-danger">*</span></label>
                    <input type="text" name="cargo_type" class="form-control" value="<?= esc(old('cargo_type') ?? $rate['cargo_type'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gia / kg (VND) <span class="text-danger">*</span></label>
                    <input type="number" name="rate_per_kg" class="form-control" value="<?= esc(old('rate_per_kg') ?? $rate['rate_per_kg'] ?? '') ?>" required min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">KL toi thieu (kg)</label>
                    <input type="number" name="min_weight" class="form-control" value="<?= esc(old('min_weight') ?? $rate['min_weight'] ?? '0.5') ?>" step="0.01" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phuong thuc lam tron</label>
                    <?php $roundVal = old('rounding_method') ?? $rate['rounding_method'] ?? 'ceil'; ?>
                    <select name="rounding_method" class="form-select">
                        <option value="ceil" <?= $roundVal === 'ceil' ? 'selected' : '' ?>>Lam tron len</option>
                        <option value="floor" <?= $roundVal === 'floor' ? 'selected' : '' ?>>Lam tron xuong</option>
                        <option value="round" <?= $roundVal === 'round' ? 'selected' : '' ?>>Lam tron thong thuong</option>
                        <option value="none" <?= $roundVal === 'none' ? 'selected' : '' ?>>Khong lam tron</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phu phi de vo (VND)</label>
                    <input type="number" name="extra_fee_fragile" class="form-control" value="<?= esc(old('extra_fee_fragile') ?? $rate['extra_fee_fragile'] ?? '0') ?>" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phu phi cong kenh (VND)</label>
                    <input type="number" name="extra_fee_bulky" class="form-control" value="<?= esc(old('extra_fee_bulky') ?? $rate['extra_fee_bulky'] ?? '0') ?>" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phu phi dac biet (VND)</label>
                    <input type="number" name="extra_fee_special" class="form-control" value="<?= esc(old('extra_fee_special') ?? $rate['extra_fee_special'] ?? '0') ?>" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">He so chia khoi</label>
                    <input type="number" name="volume_divisor" class="form-control" value="<?= esc(old('volume_divisor') ?? $rate['volume_divisor'] ?? '6000') ?>" min="1" placeholder="6000">
                    <small class="text-muted">Can QD = DxRxC / he so. VD: 6000 (hang lo), 5000 (TMDT)</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ap dung tu ngay</label>
                    <input type="date" name="effective_from" class="form-control" value="<?= esc(old('effective_from') ?? ($rate['effective_from'] ?? '')) ?>">
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Cap nhat</button>
                <a href="/admin/shipping-rates" class="btn btn-secondary">Huy</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
