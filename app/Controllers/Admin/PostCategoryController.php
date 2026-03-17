<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class PostCategoryController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper('text');
    }

    public function index()
    {
        $categories = $this->db->table('post_categories c')
            ->select('c.*, COUNT(p.id) as post_count')
            ->join('posts p', 'p.category_id = c.id AND p.section = "tin-tuc"', 'left')
            ->groupBy('c.id')
            ->orderBy('c.sort_order')
            ->get()
            ->getResultArray();

        return view('admin/post_categories/index', [
            'title'      => 'Danh mục tin tức',
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $name = trim($this->request->getPost('name'));
            $slug = url_title(convert_accented_characters($name), '-', true);

            $existing = $this->db->table('post_categories')->where('slug', $slug)->countAllResults();
            if ($existing) {
                $slug .= '-' . time();
            }

            $this->db->table('post_categories')->insert([
                'name'        => $name,
                'slug'        => $slug,
                'description' => trim($this->request->getPost('description') ?? ''),
                'sort_order'  => (int)($this->request->getPost('sort_order') ?? 0),
                'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            ]);

            return redirect()->to('/admin/post-categories')->with('success', 'Tạo danh mục thành công.');
        }

        return view('admin/post_categories/form', [
            'title'    => 'Tạo danh mục',
            'category' => null,
        ]);
    }

    public function edit($id)
    {
        $category = $this->db->table('post_categories')->where('id', $id)->get()->getRowArray();
        if (!$category) {
            return redirect()->to('/admin/post-categories')->with('error', 'Danh mục không tồn tại.');
        }

        if ($this->request->getMethod() === 'POST') {
            $this->db->table('post_categories')->where('id', $id)->update([
                'name'        => trim($this->request->getPost('name')),
                'description' => trim($this->request->getPost('description') ?? ''),
                'sort_order'  => (int)($this->request->getPost('sort_order') ?? 0),
                'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            ]);

            return redirect()->to('/admin/post-categories')->with('success', 'Cập nhật danh mục thành công.');
        }

        return view('admin/post_categories/form', [
            'title'    => 'Sửa danh mục',
            'category' => $category,
        ]);
    }

    public function delete($id)
    {
        $postCount = $this->db->table('posts')->where('category_id', $id)->countAllResults();
        if ($postCount > 0) {
            return redirect()->to('/admin/post-categories')->with('error', "Không thể xóa: danh mục đang có $postCount bài viết.");
        }

        $this->db->table('post_categories')->where('id', $id)->delete();
        return redirect()->to('/admin/post-categories')->with('success', 'Xóa danh mục thành công.');
    }
}
