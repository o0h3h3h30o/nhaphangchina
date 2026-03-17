<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="mb-4">Chinh sua don #<?= esc($order['order_code']) ?></h4>

    <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('consignments/' . esc($order['id']) . '/update') ?>" method="post">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Thong tin hang hoa -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white"><h5 class="mb-0">Thong tin hang hoa</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="cn_tracking_code" class="form-label">Ma van don Trung Quoc</label>
                            <input type="text" class="form-control" id="cn_tracking_code" name="cn_tracking_code" value="<?= old('cn_tracking_code', $order['cn_tracking_code'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Ten san pham <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="product_name" name="product_name" value="<?= old('product_name', $order['product_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="product_description" class="form-label">Mo ta san pham</label>
                            <textarea class="form-control" id="product_description" name="product_description" rows="3"><?= old('product_description', $order['product_description'] ?? '') ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="package_count" class="form-label">So kien</label>
                                <input type="number" class="form-control" id="package_count" name="package_count" value="<?= old('package_count', $order['package_count'] ?? 1) ?>" min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="estimated_weight" class="form-label">Can nang uoc tinh (kg)</label>
                                <input type="number" class="form-control" id="estimated_weight" name="estimated_weight" value="<?= old('estimated_weight', $order['estimated_weight'] ?? '') ?>" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="declared_value" class="form-label">Gia tri khai bao (VND)</label>
                                <input type="number" class="form-control" id="declared_value" name="declared_value" value="<?= old('declared_value', $order['declared_value'] ?? '') ?>" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cargo_type" class="form-label">Loai hang</label>
                                <?php $cargoType = old('cargo_type', $order['cargo_type'] ?? 'general'); ?>
                                <select class="form-select" id="cargo_type" name="cargo_type">
                                    <option value="general" <?= $cargoType === 'general' ? 'selected' : '' ?>>Hang thuong</option>
                                    <option value="fragile" <?= $cargoType === 'fragile' ? 'selected' : '' ?>>Hang de vo</option>
                                    <option value="special" <?= $cargoType === 'special' ? 'selected' : '' ?>>Hang dac biet</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="cn_warehouse" class="form-label">Kho Trung Quoc</label>
                            <input type="text" class="form-control" id="cn_warehouse" name="cn_warehouse" value="<?= old('cn_warehouse', $order['cn_warehouse'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thong tin nguoi nhan -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white"><h5 class="mb-0">Thong tin nguoi nhan tai Viet Nam</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="vn_receiver_name" class="form-label">Ho ten nguoi nhan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vn_receiver_name" name="vn_receiver_name" value="<?= old('vn_receiver_name', $order['vn_receiver_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="vn_receiver_phone" class="form-label">So dien thoai <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vn_receiver_phone" name="vn_receiver_phone" value="<?= old('vn_receiver_phone', $order['vn_receiver_phone'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="vn_receiver_address" class="form-label">Dia chi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vn_receiver_address" name="vn_receiver_address" value="<?= old('vn_receiver_address', $order['vn_receiver_address'] ?? '') ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="vn_receiver_city" class="form-label">Tinh/Thanh pho</label>
                                <input type="text" class="form-control" id="vn_receiver_city" name="vn_receiver_city" value="<?= old('vn_receiver_city', $order['vn_receiver_city'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="vn_receiver_district" class="form-label">Quan/Huyen</label>
                                <input type="text" class="form-control" id="vn_receiver_district" name="vn_receiver_district" value="<?= old('vn_receiver_district', $order['vn_receiver_district'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="vn_receiver_ward" class="form-label">Phuong/Xa</label>
                                <input type="text" class="form-control" id="vn_receiver_ward" name="vn_receiver_ward" value="<?= old('vn_receiver_ward', $order['vn_receiver_ward'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chu</label>
                            <textarea class="form-control" id="note" name="note" rows="3"><?= old('note', $order['note'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit options -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="save_action" id="save_draft" value="draft" checked>
                            <label class="form-check-label" for="save_draft">Luu nhap</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="save_action" id="save_submit" value="submit">
                            <label class="form-check-label" for="save_submit">Gui don luon</label>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Cap nhat</button>
                            <a href="<?= site_url('consignments/' . esc($order['id'])) ?>" class="btn btn-outline-secondary">Huy</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
