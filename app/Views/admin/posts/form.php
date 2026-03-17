<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-<?= $post ? 'edit' : 'plus' ?> me-2"></i><?= $post ? 'Sửa bài viết' : 'Tạo bài viết' ?></h4>
    <a href="/admin/posts" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= $post ? '/admin/posts/' . $post['id'] . '/edit' : '/admin/posts/create' ?>" enctype="multipart/form-data" id="postForm">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="<?= esc($post['title'] ?? '') ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Mục <span class="text-danger">*</span></label>
                    <select name="section" class="form-select" required id="sectionSelect">
                        <?php foreach ($sections as $key => $label): ?>
                            <option value="<?= $key ?>" <?= ($post['section'] ?? '') === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4" id="categoryWrap" style="<?= ($post['section'] ?? '') === 'tin-tuc' ? '' : 'display:none;' ?>">
                    <label class="form-label fw-bold">Danh mục tin tức</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Không chọn --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-bold">Mô tả ngắn</label>
                    <textarea name="excerpt" class="form-control" rows="2"><?= esc($post['excerpt'] ?? '') ?></textarea>
                    <small class="text-muted">Hiển thị trên trang chủ</small>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Icon (Font Awesome)</label>
                    <div class="input-group">
                        <span class="input-group-text" id="iconPreview">
                            <i class="<?= esc($post['icon'] ?? 'fas fa-file') ?>"></i>
                        </span>
                        <input type="text" name="icon" class="form-control" value="<?= esc($post['icon'] ?? '') ?>" placeholder="fas fa-star" id="iconInput">
                    </div>
                    <small class="text-muted">VD: fas fa-star</small>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Thứ tự</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= esc($post['sort_order'] ?? 0) ?>" min="0">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Nội dung chi tiết</label>
                    <div id="quillEditor" style="height:350px;background:#fff;"><?= $post['content'] ?? '' ?></div>
                    <input type="hidden" name="content" id="contentHidden">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Hình ảnh</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <?php if (!empty($post['image'])): ?>
                        <div class="mt-2">
                            <img src="/<?= esc($post['image']) ?>" alt="" style="max-height:100px;border-radius:6px;">
                            <small class="text-muted d-block">Ảnh hiện tại</small>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublished"
                            <?= ($post['is_published'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isPublished">Xuất bản (hiển thị trên trang chủ)</label>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger"><i class="fas fa-save me-1"></i> <?= $post ? 'Cập nhật' : 'Tạo mới' ?></button>
                <a href="/admin/posts" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
var quill = new Quill('#quillEditor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'align': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['blockquote', 'code-block'],
            ['link', 'image', 'video'],
            ['clean']
        ]
    },
    placeholder: 'Nhập nội dung bài viết...'
});

document.getElementById('postForm').addEventListener('submit', function() {
    document.getElementById('contentHidden').value = quill.root.innerHTML;
});

document.getElementById('iconInput').addEventListener('input', function() {
    document.querySelector('#iconPreview i').className = this.value || 'fas fa-file';
});

document.getElementById('sectionSelect').addEventListener('change', function() {
    document.getElementById('categoryWrap').style.display = this.value === 'tin-tuc' ? '' : 'none';
});
</script>

<?= $this->endSection() ?>
