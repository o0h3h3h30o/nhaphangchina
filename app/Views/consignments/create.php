<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tao don ky gui moi</h4>
        <a href="<?= site_url('consignments') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lai</a>
    </div>

    <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="<?= site_url('consignments/create') ?>" method="post">
                <?= csrf_field() ?>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-box me-2"></i>Thong tin ky gui</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="cn_tracking_code" class="form-label">Ma van don Trung Quoc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cn_tracking_code" name="cn_tracking_code" value="<?= old('cn_tracking_code') ?>" placeholder="VD: SF1234567890" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nguoi nhan</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($receiverName) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="cargo_type" class="form-label">Loai hang <span class="text-danger">*</span></label>
                            <select class="form-select" id="cargo_type" name="cargo_type" required>
                                <option value="general" <?= (old('cargo_type') ?? '') === 'general' ? 'selected' : '' ?>>Hang thuong</option>
                                <option value="hang_lo" <?= (old('cargo_type') ?? '') === 'hang_lo' ? 'selected' : '' ?>>Hang lo</option>
                                <option value="hang_tmdt" <?= (old('cargo_type') ?? '') === 'hang_tmdt' ? 'selected' : '' ?>>Hang TMDT</option>
                                <option value="fragile" <?= (old('cargo_type') ?? '') === 'fragile' ? 'selected' : '' ?>>Hang de vo</option>
                                <option value="special" <?= (old('cargo_type') ?? '') === 'special' ? 'selected' : '' ?>>Hang dac biet</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="product_description" class="form-label">Mieu ta hang hoa <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="product_description" name="product_description" rows="3" placeholder="VD: Quan ao, giay dep, linh kien dien tu..." required><?= old('product_description') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chu mat hang</label>
                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Ghi chu them neu can..."><?= old('note') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="wooden_crating" name="wooden_crating" value="1" <?= old('wooden_crating') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="wooden_crating">
                                    <i class="fas fa-crate me-1"></i> <strong>Yeu cau dong go</strong>
                                    <small class="text-muted d-block">Dong go bao ve hang hoa de vo, co tinh them phi</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Tao don ky gui</button>
                    <a href="<?= site_url('consignments') ?>" class="btn btn-outline-secondary">Huy</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
