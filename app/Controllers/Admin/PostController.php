<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class PostController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper('text');
    }

    private function getSections()
    {
        return [
            'gioi-thieu'  => 'Giới thiệu',
            'chinh-sach'  => 'Chính sách',
            'quy-dinh'    => 'Quy định vận chuyển',
            'huong-dan'   => 'Hướng dẫn',
            'tin-tuc'     => 'Tin tức',
        ];
    }

    private function getCategories()
    {
        return $this->db->table('post_categories')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get()
            ->getResultArray();
    }

    public function index()
    {
        $section = $this->request->getGet('section');

        $builder = $this->db->table('posts p')
            ->select('p.*, c.name as category_name')
            ->join('post_categories c', 'c.id = p.category_id', 'left');
        if ($section) {
            $builder->where('p.section', $section);
        }
        $posts = $builder->orderBy('p.section')->orderBy('p.sort_order')->get()->getResultArray();

        return view('admin/posts/index', [
            'title'          => 'Quản lý bài viết',
            'posts'          => $posts,
            'sections'       => $this->getSections(),
            'currentSection' => $section,
        ]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $title = trim($this->request->getPost('title'));
            $slug = url_title(convert_accented_characters($title), '-', true);

            // Check slug unique
            $existing = $this->db->table('posts')->where('slug', $slug)->countAllResults();
            if ($existing) {
                $slug .= '-' . time();
            }

            $categoryId = $this->request->getPost('category_id');

            $data = [
                'title'        => $title,
                'slug'         => $slug,
                'excerpt'      => trim($this->request->getPost('excerpt') ?? ''),
                'content'      => $this->request->getPost('content') ?? '',
                'section'      => $this->request->getPost('section'),
                'category_id'  => $categoryId ?: null,
                'icon'         => trim($this->request->getPost('icon') ?? ''),
                'is_published' => $this->request->getPost('is_published') ? 1 : 0,
                'sort_order'   => (int)($this->request->getPost('sort_order') ?? 0),
                'created_by'   => session()->get('user_id'),
            ];

            // Handle image upload
            $image = $this->request->getFile('image');
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $newName = $image->getRandomName();
                $image->move(FCPATH . 'uploads/posts', $newName);
                $data['image'] = 'uploads/posts/' . $newName;
            }

            $this->db->table('posts')->insert($data);
            return redirect()->to('/admin/posts')->with('success', 'Tạo bài viết thành công.');
        }

        return view('admin/posts/form', [
            'title'      => 'Tạo bài viết',
            'post'       => null,
            'sections'   => $this->getSections(),
            'categories' => $this->getCategories(),
        ]);
    }

    public function edit($id)
    {
        $post = $this->db->table('posts')->where('id', $id)->get()->getRowArray();
        if (!$post) {
            return redirect()->to('/admin/posts')->with('error', 'Bài viết không tồn tại.');
        }

        if ($this->request->getMethod() === 'POST') {
            $categoryId = $this->request->getPost('category_id');

            $data = [
                'title'        => trim($this->request->getPost('title')),
                'excerpt'      => trim($this->request->getPost('excerpt') ?? ''),
                'content'      => $this->request->getPost('content') ?? '',
                'section'      => $this->request->getPost('section'),
                'category_id'  => $categoryId ?: null,
                'icon'         => trim($this->request->getPost('icon') ?? ''),
                'is_published' => $this->request->getPost('is_published') ? 1 : 0,
                'sort_order'   => (int)($this->request->getPost('sort_order') ?? 0),
            ];

            // Handle image upload
            $image = $this->request->getFile('image');
            if ($image && $image->isValid() && !$image->hasMoved()) {
                if (!empty($post['image']) && file_exists(FCPATH . $post['image'])) {
                    unlink(FCPATH . $post['image']);
                }
                $newName = $image->getRandomName();
                $image->move(FCPATH . 'uploads/posts', $newName);
                $data['image'] = 'uploads/posts/' . $newName;
            }

            $this->db->table('posts')->where('id', $id)->update($data);
            return redirect()->to('/admin/posts')->with('success', 'Cập nhật bài viết thành công.');
        }

        return view('admin/posts/form', [
            'title'      => 'Sửa bài viết',
            'post'       => $post,
            'sections'   => $this->getSections(),
            'categories' => $this->getCategories(),
        ]);
    }

    public function delete($id)
    {
        $post = $this->db->table('posts')->where('id', $id)->get()->getRowArray();
        if (!$post) {
            return redirect()->to('/admin/posts')->with('error', 'Bài viết không tồn tại.');
        }

        if (!empty($post['image']) && file_exists(FCPATH . $post['image'])) {
            unlink(FCPATH . $post['image']);
        }

        $this->db->table('posts')->where('id', $id)->delete();
        return redirect()->to('/admin/posts')->with('success', 'Xóa bài viết thành công.');
    }

    public function togglePublish($id)
    {
        $post = $this->db->table('posts')->where('id', $id)->get()->getRowArray();
        if (!$post) {
            return redirect()->to('/admin/posts')->with('error', 'Bài viết không tồn tại.');
        }

        $this->db->table('posts')->where('id', $id)->update([
            'is_published' => $post['is_published'] ? 0 : 1,
        ]);

        $status = $post['is_published'] ? 'ẩn' : 'hiện';
        return redirect()->to('/admin/posts')->with('success', "Đã $status bài viết.");
    }
}
