<?php
// app/models/HoSoChamSocCongDong.php

class HoSoChamSocCongDong {
    private $db;
    private $table = 'ho_so_cham_soc_cong_dong';
    private $table_doi_tuong = 'doi_tuong';
    private $table_nguoi_dung = 'nguoi_dung';
    private $table_loai_hinh_cs = 'loai_hinh_cham_soc_cd';
    private $table_nguoi_cham_soc = 'nguoi_cham_soc';

    public function __construct($db) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            throw new InvalidArgumentException('Invalid database connection provided to HoSoChamSocCongDong model.');
        }
    }

    /**
     * Lấy danh sách hồ sơ chăm sóc cộng đồng với tìm kiếm và phân trang
     */
    public function getAll($filters = [], $limit = 15, $offset = 0) {
        try {
            // Thêm ncs.ho_ten và JOIN bảng nguoi_cham_soc
            $sqlData = "SELECT hscc.*, 
                               dt.ho_ten AS ten_doi_tuong, dt.ma_doi_tuong,
                               nd_xet_duyet.ho_ten AS ten_nguoi_xet_duyet,
                               lhcs.ten_loai_hinh AS ten_loai_hinh_cham_soc,
                               ncs.ho_ten AS ten_nguoi_cham_soc 
                        FROM {$this->table} hscc
                        JOIN {$this->table_doi_tuong} dt ON hscc.doi_tuong_id = dt.id
                        LEFT JOIN {$this->table_nguoi_dung} nd_xet_duyet ON hscc.nguoi_xet_duyet_hs_cs_id = nd_xet_duyet.id
                        LEFT JOIN {$this->table_loai_hinh_cs} lhcs ON hscc.loai_hinh_cham_soc_cd_id = lhcs.id
                        LEFT JOIN {$this->table_nguoi_cham_soc} ncs ON hscc.nguoi_cham_soc_id = ncs.id
                        ";
            
            
            $sqlCount = "SELECT COUNT(hscc.id) 
                         FROM {$this->table} hscc
                         JOIN {$this->table_doi_tuong} dt ON hscc.doi_tuong_id = dt.id
                         LEFT JOIN {$this->table_loai_hinh_cs} lhcs ON hscc.loai_hinh_cham_soc_cd_id = lhcs.id
                         LEFT JOIN {$this->table_nguoi_cham_soc} ncs ON hscc.nguoi_cham_soc_id = ncs.id"; 

            $whereClauses = [];
            $params = [];

            if (!empty($filters['searchTerm'])) {
                // Thêm ncs.ho_ten vào điều kiện tìm kiếm
                $whereClauses[] = "(hscc.ma_ho_so_cs LIKE :searchTerm OR dt.ho_ten LIKE :searchTerm OR dt.ma_doi_tuong LIKE :searchTerm OR hscc.noi_dung_de_nghi LIKE :searchTerm OR lhcs.ten_loai_hinh LIKE :searchTerm OR ncs.ho_ten LIKE :searchTerm)";
                $params[':searchTerm'] = '%' . $filters['searchTerm'] . '%';
            }
            if (!empty($filters['doi_tuong_id']) && is_numeric($filters['doi_tuong_id'])) {
                $whereClauses[] = "hscc.doi_tuong_id = :doi_tuong_id_filter";
                $params[':doi_tuong_id_filter'] = $filters['doi_tuong_id'];
            }
            if (!empty($filters['trang_thai'])) {
                $whereClauses[] = "hscc.trang_thai_hs_cs = :trang_thai_filter";
                $params[':trang_thai_filter'] = $filters['trang_thai'];
            }


            if (!empty($whereClauses)) {
                $sqlData .= " WHERE " . implode(' AND ', $whereClauses);
                $sqlCount .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sqlData .= " ORDER BY hscc.ngay_tao DESC LIMIT :limit OFFSET :offset";

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
            error_log("Database Error in HoSoChamSocCongDong::getAll: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }
    /**
     * Lấy thông tin chi tiết một hồ sơ chăm sóc cộng đồng bằng ID
     */
    public function findById($id) {
        try {
            // Thêm ncs.ho_ten và JOIN bảng nguoi_cham_soc
            $sql = "SELECT hscc.*, 
                           dt.ho_ten AS ten_doi_tuong, dt.ma_doi_tuong, dt.ngay_sinh, dt.cccd, dt.dia_chi_thuong_tru,
                           nd_lap.ho_ten AS ten_nguoi_lap_hs_cs,
                           nd_xet_duyet.ho_ten AS ten_nguoi_xet_duyet,
                           lhcs.ten_loai_hinh AS ten_loai_hinh_cham_soc, lhcs.kinh_phi_dinh_muc_du_kien AS kinh_phi_dinh_muc_loai_hinh,
                           ncs.ho_ten as ten_nguoi_cham_soc
                    FROM {$this->table} hscc
                    JOIN {$this->table_doi_tuong} dt ON hscc.doi_tuong_id = dt.id
                    LEFT JOIN {$this->table_nguoi_dung} nd_lap ON hscc.nguoi_lap_hs_cs_id = nd_lap.id
                    LEFT JOIN {$this->table_nguoi_dung} nd_xet_duyet ON hscc.nguoi_xet_duyet_hs_cs_id = nd_xet_duyet.id
                    LEFT JOIN {$this->table_loai_hinh_cs} lhcs ON hscc.loai_hinh_cham_soc_cd_id = lhcs.id
                    LEFT JOIN {$this->table_nguoi_cham_soc} ncs ON hscc.nguoi_cham_soc_id = ncs.id
                    WHERE hscc.id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in HoSoChamSocCongDong::findById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo hồ sơ chăm sóc cộng đồng mới
     */
    public function create($data) {
        // Thay nguoi_cham_soc_chinh bằng nguoi_cham_soc_id
        $fields = [
            'doi_tuong_id', 'ma_ho_so_cs', 'loai_hinh_cham_soc_cd_id', 'ngay_de_nghi_cs', 
            'noi_dung_de_nghi', 'hinh_thuc_cham_soc_cu_the',
            'don_vi_thuc_hien_id', 'ten_don_vi_thuc_hien_ngoai', 'nguoi_cham_soc_id','nguoi_cham_soc_chinh',
            'kinh_phi_du_kien', 'nguon_kinh_phi', 'ngay_bat_dau_cham_soc', 'ngay_ket_thuc_du_kien_cs',
            'nguoi_lap_hs_cs_id', 'nguoi_xet_duyet_hs_cs_id', 'ngay_xet_duyet_hs_cs', 
            'trang_thai_hs_cs', 'ly_do_thay_doi_trang_thai_cs', 'file_dinh_kem_hs_cs_path', 'ghi_chu_hs_cs'
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

        if (empty($params[':loai_hinh_cham_soc_cd_id']) || !is_numeric($params[':loai_hinh_cham_soc_cd_id'])) $params[':loai_hinh_cham_soc_cd_id'] = null;
        if (empty($params[':don_vi_thuc_hien_id']) || !is_numeric($params[':don_vi_thuc_hien_id'])) $params[':don_vi_thuc_hien_id'] = null;
        if (empty($params[':nguoi_cham_soc_id']) || !is_numeric($params[':nguoi_cham_soc_id'])) $params[':nguoi_cham_soc_id'] = null; // Thêm check cho nguoi_cham_soc_id
        if (empty($params[':nguoi_lap_hs_cs_id']) || !is_numeric($params[':nguoi_lap_hs_cs_id'])) $params[':nguoi_lap_hs_cs_id'] = null;
        if (empty($params[':nguoi_xet_duyet_hs_cs_id']) || !is_numeric($params[':nguoi_xet_duyet_hs_cs_id'])) $params[':nguoi_xet_duyet_hs_cs_id'] = null;
        
        $params[':trang_thai_hs_cs'] = $data['trang_thai_hs_cs'] ?? 'cho_xem_xet';

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute($params)) {
                return $this->db->lastInsertId();
            } else {
                error_log("HoSoChamSocCongDong creation failed: DB execution error. Info: " . print_r($stmt->errorInfo(), true));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Database Error in HoSoChamSocCongDong::create: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Cập nhật thông tin hồ sơ chăm sóc cộng đồng
     */
    public function update($id, $data) {
        // Thay nguoi_cham_soc_chinh bằng nguoi_cham_soc_id
        $allowedFields = [
           'loai_hinh_cham_soc_cd_id', 'ngay_de_nghi_cs', 'noi_dung_de_nghi', 'hinh_thuc_cham_soc_cu_the',
           'don_vi_thuc_hien_id', 'ten_don_vi_thuc_hien_ngoai', 'nguoi_cham_soc_id',
           'kinh_phi_du_kien', 'nguon_kinh_phi', 'ngay_bat_dau_cham_soc', 'ngay_ket_thuc_du_kien_cs',
           'nguoi_xet_duyet_hs_cs_id', 'ngay_xet_duyet_hs_cs', 'trang_thai_hs_cs', 
           'ly_do_thay_doi_trang_thai_cs', 'file_dinh_kem_hs_cs_path', 'ghi_chu_hs_cs'
       ];
       $setParts = [];
       $params = [':id' => $id];

       foreach ($allowedFields as $field) {
           if (array_key_exists($field, $data)) {
               $setParts[] = $field . ' = :' . $field;
               $params[':' . $field] = ($data[$field] === '') ? null : $data[$field];
           }
       }
       
       if (isset($params[':loai_hinh_cham_soc_cd_id']) && (empty($params[':loai_hinh_cham_soc_cd_id']) || !is_numeric($params[':loai_hinh_cham_soc_cd_id']))) $params[':loai_hinh_cham_soc_cd_id'] = null;
       if (isset($params[':don_vi_thuc_hien_id']) && (empty($params[':don_vi_thuc_hien_id']) || !is_numeric($params[':don_vi_thuc_hien_id']))) $params[':don_vi_thuc_hien_id'] = null;
       if (isset($params[':nguoi_cham_soc_id']) && (empty($params[':nguoi_cham_soc_id']) || !is_numeric($params[':nguoi_cham_soc_id']))) $params[':nguoi_cham_soc_id'] = null; // Thêm check
       if (isset($params[':nguoi_xet_duyet_hs_cs_id']) && (empty($params[':nguoi_xet_duyet_hs_cs_id']) || !is_numeric($params[':nguoi_xet_duyet_hs_cs_id']))) $params[':nguoi_xet_duyet_hs_cs_id'] = null;
       
       if (empty($setParts)) {
           return true;
       }

       $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";

       try {
           $stmt = $this->db->prepare($sql);
           return $stmt->execute($params);
       } catch (PDOException $e) {
           error_log("Database Error in HoSoChamSocCongDong::update for id {$id}: " . $e->getMessage());
           return false;
       }
   }

    /**
     * Xóa hồ sơ chăm sóc cộng đồng
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in HoSoChamSocCongDong::delete for id {$id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra xem mã hồ sơ đã tồn tại chưa
     */
    public function maHoSoExists($maHoSo, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE ma_ho_so_cs = :ma_ho_so"; // Sử dụng tên cột mới
            $params = [':ma_ho_so' => $maHoSo];
            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database Error in HoSoChamSocCongDong::maHoSoExists: " . $e->getMessage());
            return true;
        }
    }
    
    /**
     * Lấy danh sách các trạng thái hồ sơ chăm sóc cộng đồng
     */
    public function getTrangThaiOptions() {
        return [ // Sử dụng các giá trị enum từ CSDL mới
            'cho_xem_xet' => 'Chờ xem xét',
            'cho_duyet' => 'Chờ duyệt',
            'da_phe_duyet' => 'Đã phê duyệt',
            'khong_du_dieu_kien' => 'Không đủ điều kiện',
            'tam_dung' => 'Tạm dừng',
            'da_ket_thuc' => 'Đã kết thúc',
            'huy_bo' => 'Hủy bỏ'
        ];
    }

    // Các hàm count, sum giữ nguyên hoặc điều chỉnh tên cột `trang_thai` thành `trang_thai_hs_cs`
    public function countAll() {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table}";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) { /* ... */ return 0; }
    }

    public function countByTrangThai($trangThai) {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE trang_thai_hs_cs = :trang_thai";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':trang_thai', $trangThai);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) { /* ... */ return 0; }
    }

    public function sumKinhPhiDuKienByTrangThai($trangThai) {
        try {
            $sql = "SELECT SUM(kinh_phi_du_kien) FROM {$this->table} WHERE trang_thai_hs_cs = :trang_thai";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':trang_thai', $trangThai);
            $stmt->execute();
            return (float) $stmt->fetchColumn();
        } catch (PDOException $e) { /* ... */ return 0.0; }
    }

    public function countDistinctDoiTuongByTrangThai($trangThai) {
        try {
            $sql = "SELECT COUNT(DISTINCT doi_tuong_id) FROM {$this->table} WHERE trang_thai_hs_cs = :trang_thai";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':trang_thai', $trangThai);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) { /* ... */ return 0; }
    }

    /**
     * Lấy TOÀN BỘ danh sách hồ sơ chăm sóc cộng đồng để xuất file, có áp dụng bộ lọc.
     * Hàm này KHÔNG phân trang.
     * @param array $filters Mảng các điều kiện lọc (searchTerm, doiTuongId, trangThai)
     * @return array Mảng danh sách hồ sơ hoặc mảng rỗng nếu lỗi.
     */
    public function getAllForExport($filters = []) {
        try {
            // Câu lệnh SQL này giống hệt trong hàm getAll để lấy đủ thông tin
            $sql = "SELECT hscc.*, 
                           dt.ho_ten AS ten_doi_tuong, dt.ma_doi_tuong,
                           nd_lap.ho_ten AS ten_nguoi_lap,
                           nd_xet_duyet.ho_ten AS ten_nguoi_xet_duyet,
                           lhcs.ten_loai_hinh AS ten_loai_hinh_cham_soc,
                           ncs.ho_ten AS ten_nguoi_cham_soc 
                    FROM {$this->table} hscc
                    JOIN {$this->table_doi_tuong} dt ON hscc.doi_tuong_id = dt.id
                    LEFT JOIN {$this->table_nguoi_dung} nd_lap ON hscc.nguoi_lap_hs_cs_id = nd_lap.id
                    LEFT JOIN {$this->table_nguoi_dung} nd_xet_duyet ON hscc.nguoi_xet_duyet_hs_cs_id = nd_xet_duyet.id
                    LEFT JOIN {$this->table_loai_hinh_cs} lhcs ON hscc.loai_hinh_cham_soc_cd_id = lhcs.id
                    LEFT JOIN {$this->table_nguoi_cham_soc} ncs ON hscc.nguoi_cham_soc_id = ncs.id";
            
            $whereClauses = [];
            $params = [];

            // Áp dụng các bộ lọc
            if (!empty($filters['searchTerm'])) {
                $whereClauses[] = "(hscc.ma_ho_so_cs LIKE :searchTerm OR dt.ho_ten LIKE :searchTerm OR dt.ma_doi_tuong LIKE :searchTerm OR hscc.noi_dung_de_nghi LIKE :searchTerm OR lhcs.ten_loai_hinh LIKE :searchTerm OR ncs.ho_ten LIKE :searchTerm)";
                $params[':searchTerm'] = '%' . $filters['searchTerm'] . '%';
            }
            if (!empty($filters['doi_tuong_id']) && is_numeric($filters['doi_tuong_id'])) {
                $whereClauses[] = "hscc.doi_tuong_id = :doi_tuong_id_filter";
                $params[':doi_tuong_id_filter'] = $filters['doi_tuong_id'];
            }
            if (!empty($filters['trang_thai'])) {
                $whereClauses[] = "hscc.trang_thai_hs_cs = :trang_thai_filter";
                $params[':trang_thai_filter'] = $filters['trang_thai'];
            }

            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(' AND ', $whereClauses);
            }

            // Sắp xếp nhưng KHÔNG GIỚI HẠN (LIMIT/OFFSET) số lượng
            $sql .= " ORDER BY hscc.ngay_tao DESC";

            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Database Error in HoSoChamSocCongDong::getAllForExport: " . $e->getMessage());
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }
    
}
?>