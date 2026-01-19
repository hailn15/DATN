<?php
// app/models/HoTroKhanCap.php

class HoTroKhanCap {
    private $db;
    private $table = 'ho_tro_khan_cap';
    private $table_doi_tuong = 'doi_tuong';
    private $table_nguoi_dung = 'nguoi_dung';
    private $table_loai_hinh_kc = 'loai_hinh_ho_tro_khan_cap'; // Đã khai báo ở lần trước

    public function __construct($db) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            throw new InvalidArgumentException('Invalid database connection provided to HoTroKhanCap model.');
        }
    }

    /**
     * Lấy danh sách hỗ trợ khẩn cấp với tìm kiếm và phân trang
     * @param array $filters Mảng các điều kiện lọc (searchTerm, doiTuongId, trangThai)
     * @param int $limit Số lượng bản ghi mỗi trang
     * @param int $offset Vị trí bắt đầu lấy
     * @return array Mảng chứa 'data' (danh sách) và 'total' (tổng số bản ghi khớp)
     */
    public function getAll($filters = [], $limit = 15, $offset = 0) {
        try {
            $sqlData = "SELECT htkc.*, 
                               dt.ho_ten AS ten_doi_tuong_lien_quan, dt.ma_doi_tuong,
                               nd_xuly.ho_ten AS ten_nguoi_xu_ly,
                               lhkc.ten_loai_hinh AS ten_loai_hinh_ho_tro
                        FROM {$this->table} htkc
                        LEFT JOIN {$this->table_doi_tuong} dt ON htkc.doi_tuong_id = dt.id
                        LEFT JOIN {$this->table_nguoi_dung} nd_xuly ON htkc.nguoi_xu_ly_hs_kc_id = nd_xuly.id -- Sử dụng tên cột mới từ CSDL
                        LEFT JOIN {$this->table_loai_hinh_kc} lhkc ON htkc.loai_hinh_ho_tro_kc_id = lhkc.id";
            
            $sqlCount = "SELECT COUNT(htkc.id) 
                         FROM {$this->table} htkc
                         LEFT JOIN {$this->table_doi_tuong} dt ON htkc.doi_tuong_id = dt.id
                         LEFT JOIN {$this->table_loai_hinh_kc} lhkc ON htkc.loai_hinh_ho_tro_kc_id = lhkc.id";

            $whereClauses = [];
            $params = [];

            if (!empty($filters['searchTerm'])) {
                $whereClauses[] = "(htkc.ma_ho_so_kc LIKE :searchTerm OR htkc.ho_ten_nguoi_nhan LIKE :searchTerm OR htkc.ly_do_ho_tro LIKE :searchTerm OR dt.ma_doi_tuong LIKE :searchTerm OR dt.ho_ten LIKE :searchTerm OR lhkc.ten_loai_hinh LIKE :searchTerm)";
                $params[':searchTerm'] = '%' . $filters['searchTerm'] . '%';
            }
             if (!empty($filters['doi_tuong_id']) && is_numeric($filters['doi_tuong_id'])) {
                $whereClauses[] = "htkc.doi_tuong_id = :doi_tuong_id_filter";
                $params[':doi_tuong_id_filter'] = $filters['doi_tuong_id'];
            }
            if (!empty($filters['trang_thai'])) {
                $whereClauses[] = "htkc.trang_thai_hs_kc = :trang_thai_filter"; // Sử dụng tên cột mới từ CSDL
                $params[':trang_thai_filter'] = $filters['trang_thai'];
            }

            if (!empty($whereClauses)) {
                $sqlData .= " WHERE " . implode(' AND ', $whereClauses);
                $sqlCount .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sqlData .= " ORDER BY htkc.ngay_tao DESC LIMIT :limit OFFSET :offset";

            $stmtData = $this->db->prepare($sqlData);
            $stmtData->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmtData->bindParam(':offset', $offset, PDO::PARAM_INT);
            foreach ($params as $key => &$val) {
                $stmtData->bindParam($key, $val);
            }
            $stmtData->execute();
            $data = $stmtData->fetchAll(PDO::FETCH_ASSOC);

            $stmtCount = $this->db->prepare($sqlCount);
             foreach ($params as $key => &$val) {
                $stmtCount->bindParam($key, $val);
            }
            $stmtCount->execute();
            $total = $stmtCount->fetchColumn();

            return ['data' => $data, 'total' => $total];

        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::getAll: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * Lấy thông tin chi tiết một hỗ trợ khẩn cấp bằng ID
     * @param int $id ID hỗ trợ
     * @return array|false Mảng thông tin hoặc false nếu không tìm thấy/lỗi
     */
    public function findById($id) {
        try {
            $sql = "SELECT htkc.*, 
                           dt.ho_ten AS ten_doi_tuong_lien_quan, dt.ma_doi_tuong,
                           nd_lap.ho_ten AS ten_nguoi_lap_hs_kc, -- Sử dụng tên cột mới
                           nd_xuly.ho_ten AS ten_nguoi_xu_ly,
                           lhkc.ten_loai_hinh AS ten_loai_hinh_ho_tro, 
                           lhkc.gia_tri_ho_tro_dinh_muc AS gia_tri_dinh_muc_loai_hinh,
                           lhkc.mo_ta_hien_vat_dinh_muc AS hien_vat_dinh_muc_loai_hinh
                    FROM {$this->table} htkc
                    LEFT JOIN {$this->table_doi_tuong} dt ON htkc.doi_tuong_id = dt.id
                    LEFT JOIN {$this->table_nguoi_dung} nd_lap ON htkc.nguoi_lap_hs_kc_id = nd_lap.id -- Sử dụng tên cột mới
                    LEFT JOIN {$this->table_nguoi_dung} nd_xuly ON htkc.nguoi_xu_ly_hs_kc_id = nd_xuly.id -- Sử dụng tên cột mới
                    LEFT JOIN {$this->table_loai_hinh_kc} lhkc ON htkc.loai_hinh_ho_tro_kc_id = lhkc.id
                    WHERE htkc.id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::findById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo hỗ trợ khẩn cấp mới
     * @param array $data Dữ liệu từ form
     * @return int|false ID mới hoặc false nếu lỗi
     */
    public function create($data) {
        // Sử dụng tên cột mới từ CSDL
        $fields = [
            'ma_ho_so_kc', 'doi_tuong_id', 'loai_hinh_ho_tro_kc_id',
            'ho_ten_nguoi_nhan', 'cccd_nguoi_nhan', 'sdt_nguoi_nhan', 'dia_chi_nguoi_nhan', 
            'ly_do_ho_tro', 'hinh_thuc_ho_tro_cu_the', 
            'gia_tri_ho_tro_tien_mat', 'mo_ta_hien_vat_ho_tro',
            'ngay_de_xuat_ht', 'ngay_xu_ly_ht', 
            'nguoi_lap_hs_kc_id', 'nguoi_xu_ly_hs_kc_id', 
            'trang_thai_hs_kc', 'ly_do_tu_choi_huy_bo_kc', 
            'file_dinh_kem_hs_kc_path', 'ghi_chu_hs_kc'
        ];
        
        $params = [];
        $placeholders = [];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $params[':' . $field] = ($data[$field] === '') ? null : $data[$field];
            } else {
                $params[':' . $field] = null;
            }
            $placeholders[] = ':' . $field;
        }
        
        // Xử lý các trường đặc biệt
        if (empty($params[':loai_hinh_ho_tro_kc_id']) || !is_numeric($params[':loai_hinh_ho_tro_kc_id'])) $params[':loai_hinh_ho_tro_kc_id'] = null;
        if (empty($params[':doi_tuong_id']) || !is_numeric($params[':doi_tuong_id'])) $params[':doi_tuong_id'] = null;
        if (empty($params[':nguoi_lap_hs_kc_id']) || !is_numeric($params[':nguoi_lap_hs_kc_id'])) $params[':nguoi_lap_hs_kc_id'] = null;
        if (empty($params[':nguoi_xu_ly_hs_kc_id']) || !is_numeric($params[':nguoi_xu_ly_hs_kc_id'])) $params[':nguoi_xu_ly_hs_kc_id'] = null;
        
        if (isset($params[':gia_tri_ho_tro_tien_mat']) && (empty($params[':gia_tri_ho_tro_tien_mat']) || !is_numeric($params[':gia_tri_ho_tro_tien_mat']))) {
            $params[':gia_tri_ho_tro_tien_mat'] = null;
        }
        
        $params[':trang_thai_hs_kc'] = $data['trang_thai_hs_kc'] ?? 'cho_xem_xet';
        if (empty($params[':ngay_de_xuat_ht'])) $params[':ngay_de_xuat_ht'] = date('Y-m-d');


        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute($params)) {
                return $this->db->lastInsertId();
            } else {
                 error_log("HoTroKhanCap creation failed: DB execution error. Info: " . print_r($stmt->errorInfo(), true) . " SQL: " . $sql . " Params: " . print_r($params, true));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::create: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật thông tin hỗ trợ khẩn cấp
     * @param int $id ID hỗ trợ
     * @param array $data Dữ liệu cập nhật
     * @return bool True nếu thành công, False nếu lỗi
     */
    public function update($id, $data) {
        // Sử dụng tên cột mới từ CSDL
        $allowedFields = [
            // 'ma_ho_so_kc', // Thường không cho sửa
            'doi_tuong_id', 'loai_hinh_ho_tro_kc_id',
            'ho_ten_nguoi_nhan', 'cccd_nguoi_nhan', 'sdt_nguoi_nhan', 'dia_chi_nguoi_nhan', 
            'ly_do_ho_tro', 'hinh_thuc_ho_tro_cu_the', 
            'gia_tri_ho_tro_tien_mat', 'mo_ta_hien_vat_ho_tro',
            'ngay_de_xuat_ht', 'ngay_xu_ly_ht', 
            // 'nguoi_lap_hs_kc_id', // Thường không cho sửa
            'nguoi_xu_ly_hs_kc_id', 
            'trang_thai_hs_kc', 'ly_do_tu_choi_huy_bo_kc', 
            'file_dinh_kem_hs_kc_path', 'ghi_chu_hs_kc'
        ];
        $setParts = [];
        $params = [':id' => $id];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $setParts[] = $field . ' = :' . $field;
                $params[':' . $field] = ($data[$field] === '') ? null : $data[$field];
            }
        }
        
        // Xử lý các trường đặc biệt
        if (isset($params[':loai_hinh_ho_tro_kc_id']) && (empty($params[':loai_hinh_ho_tro_kc_id']) || !is_numeric($params[':loai_hinh_ho_tro_kc_id']))) $params[':loai_hinh_ho_tro_kc_id'] = null;
        if (isset($params[':doi_tuong_id']) && (empty($params[':doi_tuong_id']) || !is_numeric($params[':doi_tuong_id']))) $params[':doi_tuong_id'] = null;
        if (isset($params[':nguoi_xu_ly_hs_kc_id']) && (empty($params[':nguoi_xu_ly_hs_kc_id']) || !is_numeric($params[':nguoi_xu_ly_hs_kc_id']))) $params[':nguoi_xu_ly_hs_kc_id'] = null;
        
        if (isset($params[':gia_tri_ho_tro_tien_mat']) && (empty($params[':gia_tri_ho_tro_tien_mat']) || !is_numeric($params[':gia_tri_ho_tro_tien_mat']))) {
            $params[':gia_tri_ho_tro_tien_mat'] = null;
        }

        if (empty($setParts)) {
            return true; 
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::update for id {$id}: " . $e->getMessage() . " Params: " . print_r($params, true));
            return false;
        }
    }

    /**
     * Xóa hỗ trợ khẩn cấp
     * @param int $id ID
     * @return bool
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::delete for id {$id}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy danh sách các trạng thái hỗ trợ khẩn cấp
     * @return array
     */
    public function getTrangThaiOptions() {
        // Sử dụng các giá trị enum từ CSDL mới
        return [
            'cho_xem_xet' => 'Chờ xem xét',
            'cho_duyet' => 'Chờ duyệt',
            'da_ho_tro' => 'Đã hỗ trợ',
            'khong_du_dieu_kien' => 'Không đủ điều kiện',
            'huy_bo' => 'Hủy bỏ'
        ];
    }

    /**
     * Kiểm tra xem mã hồ sơ KC đã tồn tại chưa (trừ chính nó khi update)
     */
    public function maHoSoKCExists($maHoSoKC, $excludeId = null) {
        if (empty($maHoSoKC)) return false; 
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE ma_ho_so_kc = :ma_ho_so_kc"; // Sử dụng tên cột mới
            $params = [':ma_ho_so_kc' => $maHoSoKC];
            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::maHoSoKCExists: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Đếm tổng số hỗ trợ khẩn cấp
     * @return int
     */
    public function countAll() {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table}";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::countAll: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đếm số hỗ trợ khẩn cấp theo trạng thái
     * @param string $trangThai
     * @return int
     */
    public function countByTrangThai($trangThai) {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE trang_thai_hs_kc = :trang_thai"; // Sử dụng tên cột mới
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':trang_thai', $trangThai);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::countByTrangThai: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tính tổng giá trị hỗ trợ theo trạng thái và hình thức
     * @param string $trangThai
     * @param string|null $hinhThucHoTro (vd: 'tien_mat'. Nếu null, không lọc theo hình thức)
     * @return float
     */
    public function sumGiaTriHoTroByTrangThai($trangThai, $hinhThucHoTro = null) {
        try {
            $sql = "SELECT SUM(gia_tri_ho_tro_tien_mat) FROM {$this->table} WHERE trang_thai_hs_kc = :trang_thai"; // Sử dụng tên cột mới
            $params = [':trang_thai' => $trangThai];

            // Lưu ý: cột 'hinh_thuc_ho_tro' cũ đã được thay bằng 'hinh_thuc_ho_tro_cu_the'
            // Nếu bạn muốn lọc theo 'hinh_thuc_ho_tro_cu_the', cần điều chỉnh ở đây
            if ($hinhThucHoTro !== null) {
                // $sql .= " AND hinh_thuc_ho_tro_cu_the LIKE :hinh_thuc_ho_tro"; // Ví dụ nếu muốn tìm gần đúng
                // $params[':hinh_thuc_ho_tro'] = '%' . $hinhThucHoTro . '%';
                // Hoặc nếu bạn có các giá trị cố định cho hình thức thì:
                // $sql .= " AND hinh_thuc_ho_tro_cu_the = :hinh_thuc_ho_tro";
                // $params[':hinh_thuc_ho_tro'] = $hinhThucHoTro;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (float) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::sumGiaTriHoTroByTrangThai: " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Lấy TOÀN BỘ danh sách hỗ trợ khẩn cấp để xuất file, có áp dụng bộ lọc.
     * Hàm này KHÔNG phân trang.
     * @param array $filters Mảng các điều kiện lọc (searchTerm, doiTuongId, trangThai)
     * @return array Mảng danh sách hồ sơ hoặc mảng rỗng nếu lỗi.
     */
    public function getAllForExport($filters = []) {
        try {
            // Câu lệnh SQL này giống hệt trong hàm getAll để lấy đủ thông tin
            $sql = "SELECT htkc.*, 
                           dt.ho_ten AS ten_doi_tuong_lien_quan, dt.ma_doi_tuong,
                           nd_lap.ho_ten AS ten_nguoi_lap,
                           nd_xuly.ho_ten AS ten_nguoi_xu_ly,
                           lhkc.ten_loai_hinh AS ten_loai_hinh_ho_tro
                    FROM {$this->table} htkc
                    LEFT JOIN {$this->table_doi_tuong} dt ON htkc.doi_tuong_id = dt.id
                    LEFT JOIN {$this->table_nguoi_dung} nd_lap ON htkc.nguoi_lap_hs_kc_id = nd_lap.id
                    LEFT JOIN {$this->table_nguoi_dung} nd_xuly ON htkc.nguoi_xu_ly_hs_kc_id = nd_xuly.id
                    LEFT JOIN {$this->table_loai_hinh_kc} lhkc ON htkc.loai_hinh_ho_tro_kc_id = lhkc.id";
            
            $whereClauses = [];
            $params = [];

            // Áp dụng các bộ lọc
            if (!empty($filters['searchTerm'])) {
                $whereClauses[] = "(htkc.ma_ho_so_kc LIKE :searchTerm OR htkc.ho_ten_nguoi_nhan LIKE :searchTerm OR htkc.ly_do_ho_tro LIKE :searchTerm OR dt.ma_doi_tuong LIKE :searchTerm OR dt.ho_ten LIKE :searchTerm OR lhkc.ten_loai_hinh LIKE :searchTerm)";
                $params[':searchTerm'] = '%' . $filters['searchTerm'] . '%';
            }
            if (!empty($filters['doi_tuong_id']) && is_numeric($filters['doi_tuong_id'])) {
                $whereClauses[] = "htkc.doi_tuong_id = :doi_tuong_id_filter";
                $params[':doi_tuong_id_filter'] = $filters['doi_tuong_id'];
            }
            if (!empty($filters['trang_thai'])) {
                $whereClauses[] = "htkc.trang_thai_hs_kc = :trang_thai_filter";
                $params[':trang_thai_filter'] = $filters['trang_thai'];
            }

            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(' AND ', $whereClauses);
            }

            // Sắp xếp nhưng KHÔNG GIỚI HẠN (LIMIT/OFFSET) số lượng
            $sql .= " ORDER BY htkc.ngay_tao DESC";

            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::getAllForExport: " . $e->getMessage());
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }

    /**
     * Tính tổng giá trị hỗ trợ (tiền mặt) theo trạng thái.
     * @param string $trangThai
     * @return float
     */
    public function sumGiaTriTienMatByTrangThai($trangThai) {
        try {
            // Sử dụng cột `gia_tri_ho_tro_tien_mat` để tính tổng
            $sql = "SELECT SUM(gia_tri_ho_tro_tien_mat) FROM {$this->table} WHERE trang_thai_hs_kc = :trang_thai";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':trang_thai', $trangThai);
            $stmt->execute();
            return (float) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Error in HoTroKhanCap::sumGiaTriTienMatByTrangThai: " . $e->getMessage());
            return 0.0;
        }
    }
}
?>