<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="fas fa-cog me-2"></i>Cài đặt website</h3>
</div>

<form method="post" action="/admin/settings">
    <?= csrf_field() ?>

    <?php
    $groupLabels = [
        'general' => 'Thông tin chung',
        'contact' => 'Liên hệ',
        'social'  => 'Mạng xã hội',
    ];
    $groupIcons = [
        'general' => 'fas fa-info-circle',
        'contact' => 'fas fa-address-book',
        'social'  => 'fas fa-share-alt',
    ];
    ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <?php $first = true; ?>
        <?php foreach ($groups as $group => $settings): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $first ? 'active' : '' ?>"
                        id="tab-<?= esc($group) ?>"
                        data-bs-toggle="tab"
                        data-bs-target="#pane-<?= esc($group) ?>"
                        type="button" role="tab">
                    <i class="<?= $groupIcons[$group] ?? 'fas fa-cog' ?> me-1"></i>
                    <?= esc($groupLabels[$group] ?? ucfirst($group)) ?>
                </button>
            </li>
            <?php $first = false; ?>
        <?php endforeach; ?>
    </ul>

    <!-- Tab content -->
    <div class="tab-content">
        <?php $first = true; ?>
        <?php foreach ($groups as $group => $settings): ?>
            <div class="tab-pane fade <?= $first ? 'show active' : '' ?>"
                 id="pane-<?= esc($group) ?>" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="<?= $groupIcons[$group] ?? 'fas fa-cog' ?> me-2"></i>
                            <?= esc($groupLabels[$group] ?? ucfirst($group)) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($settings as $setting): ?>
                            <div class="mb-3">
                                <label for="setting-<?= esc($setting['setting_key']) ?>" class="form-label fw-semibold">
                                    <?= esc($setting['setting_label']) ?>
                                </label>
                                <?php if (in_array($setting['setting_key'], ['google_map_embed', 'footer_text'])): ?>
                                    <textarea class="form-control"
                                              id="setting-<?= esc($setting['setting_key']) ?>"
                                              name="settings[<?= esc($setting['setting_key']) ?>]"
                                              rows="3"><?= esc($setting['setting_value']) ?></textarea>
                                <?php else: ?>
                                    <input type="text"
                                           class="form-control"
                                           id="setting-<?= esc($setting['setting_key']) ?>"
                                           name="settings[<?= esc($setting['setting_key']) ?>]"
                                           value="<?= esc($setting['setting_value']) ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php $first = false; ?>
        <?php endforeach; ?>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save me-2"></i>Lưu cài đặt
        </button>
    </div>
</form>

<?= $this->endSection() ?>
