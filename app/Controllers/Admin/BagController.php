<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class BagController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Danh sách bao
     */
    public function index()
    {
        $status = $this->request->getGet('status') ?? '';

        $query = $this->db->table('cn_bags b')
            ->select('b.*, p.username as packed_by_name, u.username as unpacked_by_name')
            ->join('users p', 'p.id = b.packed_by', 'left')
            ->join('users u', 'u.id = b.unpacked_by', 'left');

        if ($status) {
            $query->where('b.status', $status);
        }

        $bags = $query->orderBy('b.created_at', 'DESC')->get()->getResultArray();

        return view('admin/bags/index', [
            'title'  => 'Quan ly bao hang',
            'bags'   => $bags,
            'status' => $status,
        ]);
    }

    /**
     * Tạo bao mới - hiển thị form (2 tabs: Excel + Manual)
     */
    public function create()
    {
        return view('admin/bags/create', ['title' => 'Tao bao moi']);
    }

    /**
     * Helper: Tạo mã bao unique
     */
    private function generateBagCode()
    {
        $bagCode = 'BAO-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        while ($this->db->table('cn_bags')->where('bag_code', $bagCode)->countAllResults() > 0) {
            $bagCode = 'BAO-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        }
        return $bagCode;
    }

    /**
     * Helper: Xử lý 1 dòng dữ liệu (tracking, weight, date, qty) → thêm vào bao
     * Return: ['created' => bool, 'tracking' => string]
     */
    private function processRow($bagId, $trackingCode, $weight, $date, $qty)
    {
        $staffId = session()->get('user_id');
        $now     = date('Y-m-d H:i:s');
        $receivedAt = $date ? $date . ' ' . date('H:i:s') : $now;

        // Check parcel đã tồn tại chưa
        $parcel = $this->db->table('cn_warehouse_parcels')
            ->where('cn_tracking_code', $trackingCode)
            ->get()->getRowArray();

        if ($parcel) {
            // Đã tồn tại → nếu chưa thuộc bao nào thì gán vào bao
            if (!$parcel['bag_id'] && $parcel['status'] === 'received') {
                $this->db->table('cn_warehouse_parcels')
                    ->where('id', $parcel['id'])
                    ->update([
                        'bag_id' => $bagId,
                        'status' => 'packed',
                        'weight' => $weight > 0 ? $weight : $parcel['weight'],
                        'chargeable_weight' => $weight > 0 ? $weight : $parcel['chargeable_weight'],
                    ]);

                $this->db->table('cn_bags')
                    ->where('id', $bagId)
                    ->set('total_parcels', 'total_parcels + 1', false)
                    ->set('total_weight', 'total_weight + ' . ($weight > 0 ? $weight : (float)$parcel['chargeable_weight']), false)
                    ->update();
            }
            return ['created' => false, 'tracking' => $trackingCode];
        }

        // Chưa tồn tại → auto-match với đơn ký gửi
        $matchedOrder = $this->db->table('consignment_orders')
            ->where('cn_tracking_code', $trackingCode)
            ->whereIn('status', ['draft', 'submitted'])
            ->get()->getRowArray();

        $consignmentOrderId = null;
        $userId = null;
        if ($matchedOrder) {
            $consignmentOrderId = $matchedOrder['id'];
            $userId = $matchedOrder['user_id'];
        }

        $chargeableWeight = $weight > 0 ? $weight : 0;

        // Insert kiện hàng mới
        $this->db->table('cn_warehouse_parcels')->insert([
            'cn_tracking_code'     => $trackingCode,
            'consignment_order_id' => $consignmentOrderId,
            'weight'               => $chargeableWeight,
            'chargeable_weight'    => $chargeableWeight,
            'cargo_type'           => $matchedOrder['cargo_type'] ?? 'general',
            'bag_id'               => $bagId,
            'user_id'              => $userId,
            'received_by'          => $staffId,
            'status'               => 'packed',
            'received_at'          => $receivedAt,
        ]);
        $parcelId = $this->db->insertID();

        // Cập nhật tổng bao
        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->set('total_parcels', 'total_parcels + 1', false)
            ->set('total_weight', 'total_weight + ' . $chargeableWeight, false)
            ->update();

        // Nếu match đơn ký gửi → cập nhật trạng thái
        if ($matchedOrder) {
            $this->db->table('consignment_orders')
                ->where('id', $matchedOrder['id'])
                ->update([
                    'actual_weight' => $chargeableWeight,
                    'status'        => 'received_cn',
                    'cn_parcel_id'  => $parcelId,
                    'updated_at'    => $now,
                ]);

            $this->db->table('consignment_status_histories')->insert([
                'consignment_order_id' => $matchedOrder['id'],
                'from_status'          => $matchedOrder['status'],
                'to_status'            => 'received_cn',
                'changed_by'           => $staffId,
                'note'                 => 'Nhap kho TQ - import bao',
                'created_at'           => $now,
            ]);

            $this->db->table('tracking_events')->insert([
                'consignment_order_id' => $matchedOrder['id'],
                'event_type'           => 'status_change',
                'title'                => 'Da nhap kho Trung Quoc',
                'description'          => "Can: {$chargeableWeight}kg",
                'location'             => 'Kho Trung Quoc',
                'handler'              => session()->get('user_name'),
                'created_by'           => $staffId,
                'event_at'             => $now,
                'created_at'           => $now,
            ]);
        }

        return ['created' => true, 'tracking' => $trackingCode];
    }

    /**
     * AJAX: Parse Excel → trả JSON để preview trên form
     */
    public function parseExcel()
    {
        $file = $this->request->getFile('excel_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['error' => 'Vui long chon file Excel.']);
        }

        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            return $this->response->setJSON(['error' => 'Chi ho tro file .xlsx, .xls, .csv']);
        }

        $tmpPath = $file->getTempName();
        $rows = ($ext === 'csv') ? $this->parseCsv($tmpPath) : $this->parseXlsx($tmpPath);

        if (empty($rows)) {
            return $this->response->setJSON(['error' => 'File khong co du lieu.']);
        }

        // Check từng tracking code đã tồn tại chưa
        foreach ($rows as &$row) {
            $existing = $this->db->table('cn_warehouse_parcels')
                ->where('cn_tracking_code', $row['tracking'])
                ->get()->getRowArray();
            $row['exists'] = !empty($existing);
        }

        return $this->response->setJSON(['rows' => $rows]);
    }

    /**
     * AJAX: Import từ Excel
     */
    public function importExcel()
    {
        $file = $this->request->getFile('excel_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['error' => 'Vui long chon file Excel.']);
        }

        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            return $this->response->setJSON(['error' => 'Chi ho tro file .xlsx, .xls, .csv']);
        }

        // Di chuyển file tạm
        $tmpPath = $file->getTempName();

        // Đọc file Excel bằng PHP (không cần thư viện ngoài cho xlsx đơn giản)
        $rows = [];
        if ($ext === 'csv') {
            $rows = $this->parseCsv($tmpPath);
        } else {
            $rows = $this->parseXlsx($tmpPath);
        }

        if (empty($rows)) {
            return $this->response->setJSON(['error' => 'File khong co du lieu.']);
        }

        $note = trim($this->request->getPost('note') ?? '');

        // Tạo bao mới
        $bagCode = $this->generateBagCode();
        $this->db->table('cn_bags')->insert([
            'bag_code'  => $bagCode,
            'packed_by' => session()->get('user_id'),
            'note'      => $note,
            'status'    => 'packing',
        ]);
        $bagId = $this->db->insertID();

        $this->db->transStart();

        $created  = 0;
        $existing = 0;

        foreach ($rows as $row) {
            $date     = $row['date'] ?? date('Y-m-d');
            $tracking = trim($row['tracking'] ?? '');
            $qty      = max(1, (int)($row['qty'] ?? 1));
            $weight   = (float)($row['weight'] ?? 0);

            if (empty($tracking)) continue;

            $result = $this->processRow($bagId, $tracking, $weight, $date, $qty);
            if ($result['created']) {
                $created++;
            } else {
                $existing++;
            }
        }

        $this->db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'message' => "Da tao bao {$bagCode} voi " . ($created + $existing) . " kien hang.",
            'bag_url' => site_url('admin/bags/' . $bagId),
            'details' => [
                'total'    => $created + $existing,
                'created'  => $created,
                'existing' => $existing,
            ],
        ]);
    }

    /**
     * AJAX: Import từng dòng (manual)
     */
    public function importManual()
    {
        $rows = $this->request->getPost('rows');
        $note = trim($this->request->getPost('note') ?? '');

        if (empty($rows) || !is_array($rows)) {
            return $this->response->setJSON(['error' => 'Chua nhap du lieu.']);
        }

        // Validate
        foreach ($rows as $i => $row) {
            if (empty(trim($row['tracking'] ?? ''))) {
                return $this->response->setJSON(['error' => 'Dong ' . ($i + 1) . ': Ma van don khong duoc de trong.']);
            }
            if ((float)($row['weight'] ?? 0) <= 0) {
                return $this->response->setJSON(['error' => 'Dong ' . ($i + 1) . ': Khoi luong phai lon hon 0.']);
            }
        }

        // Tạo bao mới
        $bagCode = $this->generateBagCode();
        $this->db->table('cn_bags')->insert([
            'bag_code'  => $bagCode,
            'packed_by' => session()->get('user_id'),
            'note'      => $note,
            'status'    => 'packing',
        ]);
        $bagId = $this->db->insertID();

        $this->db->transStart();

        $created  = 0;
        $existing = 0;

        foreach ($rows as $row) {
            $tracking = trim($row['tracking'] ?? '');
            $weight   = (float)($row['weight'] ?? 0);
            $date     = $row['date'] ?? date('Y-m-d');
            $qty      = max(1, (int)($row['qty'] ?? 1));

            if (empty($tracking)) continue;

            $result = $this->processRow($bagId, $tracking, $weight, $date, $qty);
            if ($result['created']) {
                $created++;
            } else {
                $existing++;
            }
        }

        $this->db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'message' => "Da tao bao {$bagCode} voi " . ($created + $existing) . " kien hang.",
            'bag_url' => site_url('admin/bags/' . $bagId),
            'details' => [
                'total'    => $created + $existing,
                'created'  => $created,
                'existing' => $existing,
            ],
        ]);
    }

    /**
     * Parse CSV file
     */
    private function parseCsv($path)
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) < 2) continue;
                // Bỏ qua header nếu có
                if (strtolower(trim($data[0])) === 'ngay' || strtolower(trim($data[0])) === 'date') continue;

                $rows[] = [
                    'date'     => $this->parseDate($data[0] ?? ''),
                    'tracking' => trim($data[1] ?? ''),
                    'qty'      => (int)($data[2] ?? 1),
                    'weight'   => (float)($data[3] ?? 0),
                ];
            }
            fclose($handle);
        }
        return $rows;
    }

    /**
     * Parse XLSX file (dùng SimpleXLSX nếu có, fallback ZipArchive)
     */
    private function parseXlsx($path)
    {
        $rows = [];

        // Dùng ZipArchive để đọc xlsx (built-in PHP)
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return $rows;
        }

        // Đọc shared strings
        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml) {
            $ssDoc = new \DOMDocument();
            $ssDoc->loadXML($ssXml);
            $siElements = $ssDoc->getElementsByTagName('si');
            foreach ($siElements as $si) {
                $text = '';
                $tElements = $si->getElementsByTagName('t');
                foreach ($tElements as $t) {
                    $text .= $t->textContent;
                }
                $sharedStrings[] = $text;
            }
        }

        // Đọc sheet1
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXml) {
            $zip->close();
            return $rows;
        }

        $doc = new \DOMDocument();
        $doc->loadXML($sheetXml);
        $rowElements = $doc->getElementsByTagName('row');

        foreach ($rowElements as $rowEl) {
            $cells = $rowEl->getElementsByTagName('c');
            $rowData = [];

            foreach ($cells as $cell) {
                $colRef = preg_replace('/[0-9]+/', '', $cell->getAttribute('r'));
                $colIdx = $this->colToIndex($colRef);
                $type   = $cell->getAttribute('t');
                $vNode  = $cell->getElementsByTagName('v');

                $value = '';
                if ($vNode->length > 0) {
                    $value = $vNode->item(0)->textContent;
                    if ($type === 's') {
                        $value = $sharedStrings[(int)$value] ?? $value;
                    }
                }

                $rowData[$colIdx] = $value;
            }

            if (empty($rowData)) continue;

            // Cột: 0=Ngày, 1=Mã vận đơn, 2=Số kiện, 3=Khối lượng
            $dateVal  = $rowData[0] ?? '';
            $tracking = trim($rowData[1] ?? '');
            $qty      = (int)($rowData[2] ?? 1);
            $weight   = (float)($rowData[3] ?? 0);

            if (empty($tracking)) continue;
            // Bỏ header
            if (strtolower($tracking) === 'ma van don' || strtolower($tracking) === 'tracking') continue;

            $rows[] = [
                'date'     => $this->parseDate($dateVal),
                'tracking' => $tracking,
                'qty'      => max(1, $qty),
                'weight'   => $weight,
            ];
        }

        $zip->close();
        return $rows;
    }

    /**
     * Convert Excel column letter to index (A=0, B=1, ...)
     */
    private function colToIndex($col)
    {
        $col = strtoupper($col);
        $idx = 0;
        for ($i = 0; $i < strlen($col); $i++) {
            $idx = $idx * 26 + (ord($col[$i]) - ord('A') + 1);
        }
        return $idx - 1;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($value)
    {
        if (empty($value)) return date('Y-m-d');

        // Excel serial date number
        if (is_numeric($value) && (int)$value > 40000) {
            $unixDate = ((int)$value - 25569) * 86400;
            return date('Y-m-d', $unixDate);
        }

        // Thử parse date string
        $ts = strtotime($value);
        if ($ts) {
            return date('Y-m-d', $ts);
        }

        return date('Y-m-d');
    }

    /**
     * Chi tiết bao + danh sách kiện
     */
    public function show($id)
    {
        $bag = $this->db->table('cn_bags')->where('id', $id)->get()->getRowArray();
        if (!$bag) {
            return redirect()->to('/admin/bags')->with('error', 'Bao khong ton tai.');
        }

        $parcels = $this->db->table('cn_warehouse_parcels p')
            ->select('p.*, u.username as user_name')
            ->join('users u', 'u.id = p.user_id', 'left')
            ->where('p.bag_id', $id)
            ->orderBy('p.updated_at', 'DESC')
            ->get()->getResultArray();

        return view('admin/bags/show', [
            'title'   => 'Bao ' . $bag['bag_code'],
            'bag'     => $bag,
            'parcels' => $parcels,
        ]);
    }

    /**
     * AJAX: Thêm kiện vào bao (scan barcode)
     */
    public function addParcel($bagId)
    {
        $bag = $this->db->table('cn_bags')->where('id', $bagId)->get()->getRowArray();
        if (!$bag) {
            return $this->response->setJSON(['error' => 'Bao khong ton tai.']);
        }
        if ($bag['status'] !== 'packing') {
            return $this->response->setJSON(['error' => 'Bao da niem phong, khong the them kien.']);
        }

        $trackingCode = trim($this->request->getPost('tracking_code') ?? '');
        if (empty($trackingCode)) {
            return $this->response->setJSON(['error' => 'Nhap ma van don.']);
        }

        $parcel = $this->db->table('cn_warehouse_parcels')
            ->where('cn_tracking_code', $trackingCode)
            ->get()->getRowArray();

        if (!$parcel) {
            return $this->response->setJSON(['error' => 'Kien hang khong ton tai trong kho.']);
        }
        if ($parcel['bag_id']) {
            $existingBag = $this->db->table('cn_bags')->where('id', $parcel['bag_id'])->get()->getRowArray();
            return $this->response->setJSON(['error' => 'Kien da nam trong bao ' . ($existingBag['bag_code'] ?? $parcel['bag_id'])]);
        }
        if ($parcel['status'] !== 'received') {
            return $this->response->setJSON(['error' => 'Kien khong o trang thai cho dong bao.']);
        }

        // Thêm vào bao
        $this->db->table('cn_warehouse_parcels')
            ->where('id', $parcel['id'])
            ->update([
                'bag_id' => $bagId,
                'status' => 'packed',
            ]);

        // Cập nhật tổng bao
        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->set('total_parcels', 'total_parcels + 1', false)
            ->set('total_weight', 'total_weight + ' . (float) $parcel['chargeable_weight'], false)
            ->update();

        // Lấy user name
        $userName = '';
        if ($parcel['user_id']) {
            $u = $this->db->table('users')->where('id', $parcel['user_id'])->get()->getRowArray();
            $userName = $u['username'] ?? '';
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Da them kien ' . $trackingCode . ' vao bao.',
            'parcel'  => [
                'id'                => $parcel['id'],
                'cn_tracking_code'  => $parcel['cn_tracking_code'],
                'weight'            => $parcel['weight'],
                'chargeable_weight' => $parcel['chargeable_weight'],
                'user_name'         => $userName,
                'matched'           => !empty($parcel['consignment_order_id']),
            ],
        ]);
    }

    /**
     * Bỏ kiện khỏi bao
     */
    public function removeParcel($bagId, $parcelId)
    {
        $bag = $this->db->table('cn_bags')->where('id', $bagId)->get()->getRowArray();
        if (!$bag || $bag['status'] !== 'packing') {
            return redirect()->back()->with('error', 'Khong the bo kien khi bao da niem phong.');
        }

        $parcel = $this->db->table('cn_warehouse_parcels')
            ->where('id', $parcelId)
            ->where('bag_id', $bagId)
            ->get()->getRowArray();

        if (!$parcel) {
            return redirect()->back()->with('error', 'Kien hang khong nam trong bao nay.');
        }

        $this->db->table('cn_warehouse_parcels')
            ->where('id', $parcelId)
            ->update(['bag_id' => null, 'status' => 'received']);

        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->set('total_parcels', 'GREATEST(total_parcels - 1, 0)', false)
            ->set('total_weight', 'GREATEST(total_weight - ' . (float) $parcel['chargeable_weight'] . ', 0)', false)
            ->update();

        return redirect()->back()->with('success', 'Da bo kien ' . $parcel['cn_tracking_code'] . ' khoi bao.');
    }

    /**
     * Niêm phong bao
     */
    public function seal($bagId)
    {
        $bag = $this->db->table('cn_bags')->where('id', $bagId)->get()->getRowArray();
        if (!$bag || $bag['status'] !== 'packing') {
            return redirect()->back()->with('error', 'Khong the niem phong bao nay.');
        }
        if ($bag['total_parcels'] <= 0) {
            return redirect()->back()->with('error', 'Bao chua co kien hang nao.');
        }

        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->update([
                'status'    => 'sealed',
                'sealed_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->back()->with('success', 'Da niem phong bao ' . $bag['bag_code']);
    }

    /**
     * Xuất kho - bao bắt đầu vận chuyển
     */
    public function depart($bagId)
    {
        $bag = $this->db->table('cn_bags')->where('id', $bagId)->get()->getRowArray();
        if (!$bag || !in_array($bag['status'], ['sealed'])) {
            return redirect()->back()->with('error', 'Bao chua niem phong hoac da xuat kho.');
        }

        $staffId = session()->get('user_id');
        $now     = date('Y-m-d H:i:s');

        $this->db->transStart();

        // Cập nhật bao
        $this->db->table('cn_bags')
            ->where('id', $bagId)
            ->update([
                'status'      => 'in_transit',
                'departed_at' => $now,
            ]);

        // Cập nhật tất cả kiện trong bao
        $this->db->table('cn_warehouse_parcels')
            ->where('bag_id', $bagId)
            ->update(['status' => 'in_transit']);

        // Cập nhật đơn ký gửi liên quan → in_transit_cn_vn
        $parcels = $this->db->table('cn_warehouse_parcels')
            ->where('bag_id', $bagId)
            ->where('consignment_order_id IS NOT NULL')
            ->get()->getResultArray();

        foreach ($parcels as $p) {
            $order = $this->db->table('consignment_orders')
                ->where('id', $p['consignment_order_id'])
                ->get()->getRowArray();

            if ($order && in_array($order['status'], ['received_cn', 'packed_for_truck'])) {
                $this->db->table('consignment_orders')
                    ->where('id', $order['id'])
                    ->update(['status' => 'in_transit_cn_vn', 'updated_at' => $now]);

                $this->db->table('consignment_status_histories')->insert([
                    'consignment_order_id' => $order['id'],
                    'from_status'          => $order['status'],
                    'to_status'            => 'in_transit_cn_vn',
                    'changed_by'           => $staffId,
                    'note'                 => 'Xuat kho - Bao ' . $bag['bag_code'],
                    'created_at'           => $now,
                ]);

                $this->db->table('tracking_events')->insert([
                    'consignment_order_id' => $order['id'],
                    'event_type'           => 'status_change',
                    'title'                => 'Dang van chuyen ve Viet Nam',
                    'description'          => 'Bao ' . $bag['bag_code'],
                    'location'             => 'Kho Trung Quoc',
                    'handler'              => session()->get('user_name'),
                    'created_by'           => $staffId,
                    'event_at'             => $now,
                    'created_at'           => $now,
                ]);
            }
        }

        $this->db->transComplete();

        return redirect()->back()->with('success', 'Da xuat kho bao ' . $bag['bag_code'] . ' voi ' . $bag['total_parcels'] . ' kien.');
    }
}
