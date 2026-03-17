<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Load posts by section for homepage
        $posts = $db->table('posts')
            ->where('is_published', 1)
            ->orderBy('sort_order')
            ->get()
            ->getResultArray();

        $sections = [];
        foreach ($posts as $p) {
            $sections[$p['section']][] = $p;
        }

        // Latest news for homepage
        $latestNews = $db->table('posts p')
            ->select('p.*, c.name as category_name')
            ->join('post_categories c', 'c.id = p.category_id', 'left')
            ->where('p.section', 'tin-tuc')
            ->where('p.is_published', 1)
            ->orderBy('p.created_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        return view('home/index', [
            'sections'   => $sections,
            'latestNews' => $latestNews,
        ]);
    }

    public function tracking()
    {
        $trackingCode = trim($this->request->getPost('tracking_code') ?? '');
        if (!$trackingCode) {
            return redirect()->to('/')->with('tracking_error', 'Vui lòng nhập mã vận đơn.');
        }

        $db = \Config\Database::connect();
        $order = $db->table('consignment_orders')
            ->select('consignment_orders.*, users.username')
            ->join('users', 'users.id = consignment_orders.user_id', 'left')
            ->where('order_code', $trackingCode)
            ->orWhere('cn_tracking_code', $trackingCode)
            ->get()
            ->getRowArray();

        if (!$order) {
            return redirect()->to('/')->with('tracking_error', 'Không tìm thấy đơn hàng với mã: ' . $trackingCode);
        }

        return redirect()->to('/')->with('tracking_result', json_encode($order))->with('tracking_code', $trackingCode);
    }

    public function news()
    {
        $db = \Config\Database::connect();
        $categorySlug = $this->request->getGet('category');

        $categories = $db->table('post_categories')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get()
            ->getResultArray();

        $builder = $db->table('posts p')
            ->select('p.*, c.name as category_name, c.slug as category_slug')
            ->join('post_categories c', 'c.id = p.category_id', 'left')
            ->where('p.section', 'tin-tuc')
            ->where('p.is_published', 1);

        if ($categorySlug) {
            $builder->where('c.slug', $categorySlug);
        }

        // Pagination
        $perPage = 9;
        $page = max(1, (int)($this->request->getGet('page') ?? 1));

        // Clone builder for count
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);
        $totalPages = max(1, ceil($total / $perPage));
        $offset = ($page - 1) * $perPage;

        $news = $builder->orderBy('p.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('home/news', [
            'news'            => $news,
            'categories'      => $categories,
            'currentCategory' => $categorySlug,
            'currentPage'     => $page,
            'totalPages'      => $totalPages,
            'total'           => $total,
        ]);
    }

    public function newsDetail($slug)
    {
        $db = \Config\Database::connect();
        $post = $db->table('posts p')
            ->select('p.*, c.name as category_name')
            ->join('post_categories c', 'c.id = p.category_id', 'left')
            ->where('p.slug', $slug)
            ->where('p.section', 'tin-tuc')
            ->where('p.is_published', 1)
            ->get()
            ->getRowArray();

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Related posts
        $related = $db->table('posts')
            ->where('section', 'tin-tuc')
            ->where('is_published', 1)
            ->where('id !=', $post['id'])
            ->orderBy('created_at', 'DESC')
            ->limit(4)
            ->get()
            ->getResultArray();

        return view('home/news_detail', [
            'post'    => $post,
            'related' => $related,
        ]);
    }

    public function page($slug)
    {
        $db = \Config\Database::connect();
        $post = $db->table('posts')
            ->where('slug', $slug)
            ->where('is_published', 1)
            ->where('section !=', 'tin-tuc')
            ->get()
            ->getRowArray();

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('home/page', ['post' => $post]);
    }
}
