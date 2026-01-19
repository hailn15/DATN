<?php
// app/models/HoSoTroCap.php

class HoSoTroCap {
    private $db;
    private $table = 'ho_so_tro_cap';
    private $table_doi_tuong = 'doi_tuong';
    private $table_nguoi_dung = 'nguoi_dung';
    private $table_loai_doi_tuong = 'loai_doi_tuong'; // Thêm để join lấy tên loại đối tượng
    private $table_muc_tro_cap = 'muc_tro_cap_hang_thang'; // Thêm để join lấy tên mức trợ cấp

    public function __construct($db) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            throw new InvalidArgumentException('Invalid database connection provided to HoSoTroCap model.');
        }
    }

    /**
     * Lấy danh sách hồ sơ trợ cấp với tìm kiếm và phân trang
     * @param array $filters Mảng các điều kiện lọc (searchTerm, doiTuongId, trangThai)
     * @param int $limit Số lượng bản ghi mỗi trang
     * @param int $offset Vị trí bắt đầu lấy
     * @return array Mảng chứa 'data' (danh sách hồ sơ) và 'total' (tổng số bản ghi khớp)
     */
    public function getAll($filters = [], $limit = 15, $offset = 0) {
        try {
            $sqlData = "SELECT hstc.*, 
                               dt.ho_ten AS ten_doi_tuong, dt.ma_doi_tuong,
                               nd_duyet.ho_ten AS ten_nguoi_duyet,
                               mtc.ten_muc AS ten_muc_tro_cap, mtc.ma_muc AS ma_muc_tro_cap
                        FROM {$this->table} hstc
                        LEFT JOIN {$this->table_doi_tuong} dt ON hstc.doi_tuong_id = dt.id
                        LEFT JOIN {$this->table_nguoi_dung} nd_duyet ON hstc.nguoi_duyet_id = nd_duyet.id
                        LEFT JOIN {$this->table_muc_tro_cap} mtc ON hstc.muc_tro_cap_id = mtc.id";
            
            $sqlCount = "SELECT COUNT(hstc.id) 
                         FROM {$this->table} hstc
                         LEFT JOIN {$this->table_doi_tuong} dt ON hstc.doi_tuong_id = dt.id
                         LEFT JOIN {$this->table_muc_tro_cap} mtc ON hstc.muc_tro_cap_id = mtc.id"; // Thêm join cho count

            $whereClauses = [];
            $params = [];

            if (!empty($filters['searchTerm'])) {
                $whereClauses[] = "(hstc.ma_ho_so LIKE :searchTerm OR dt.ho_ten LIKE :searchTerm OR dt.ma_doi_tuong LIKE :searchTerm OR mtc.ten_muc LIKE :searchTerm)";
                $params[':searchTerm'] = '%' . $filters['searchTerm'] . '%';
            }

            if (!empty($filters['doi_tuong_id']) && is_numeric($filters['doi_tuong_id'])) {
                $whereClauses[] = "hstc.doi_tuong_id = :doi_tuong_id_filter";
                $params[':doi_tuong_id_filter'] = $filters['doi_tuong_id'];
            }

            if (!empty($filters['trang_thai'])) {
                $whereClauses[] = "hstc.trang_thai = :trang_thai_filter";
                $params[':trang_thai_filter'] = $filters['trang_thai'];
            }
            
            if (!empty($whereClauses)) {
                $sqlData .= " WHERE " . implode(' AND ', $whereClauses);
                $sqlCount .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sqlData .= " ORDER BY hstc.ngay_tao DESC LIMIT :limit OFFSET :offset";

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
            error_log("Database Error in HoSoTroCap::getAll: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * Lấy thông tin chi tiết một hồ sơ bằng ID
     * @param int $id ID hồ sơ
     * @return array|false Mảng thông tin hoặc false nếu không tìm thấy/lỗi
     */
    public function findById($id) {
        try {
            $sql = "SELECT hstc.*, 
                           dt.ho_ten AS ten_doi_tuong, dt.ma_doi_tuong, dt.ngay_sinh, dt.cccd, dt.dia_chi_thuong_tru,
                           ldt.ten_loai AS ten_loai_doi_tuong,
                           nd_duyet.ho_ten AS ten_nguoi_duyet,
                           nd_lap.ho_ten AS ten_nguoi_lap,
                           mtc.ten_muc AS ten_muc_tro_cap, mtc.ma_muc AS ma_muc_tro_cap, mtc.so_tien_ap_dung AS so_tien_muc_chuan
                    FROM {$this->table} hstc
                    JOIN {$this->table_doi_tuong} dt ON hstc.doi_tuong_id = dt.id
                    LEFT JOIN {$this->table_loai_doi_tuong} ldt ON dt.loai_doi_tuong_id = ldt.id
                    LEFT JOIN {$this->table_nguoi_dung} nd_duyet ON hstc.nguoi_duyet_id = nd_duyet.id
                    LEFT JOIN {$this->table_nguoi_dung} nd_lap ON hstc.nguoi_lap_id = nd_lap.id
                    LEFT JOIN {$this->table_muc_tro_cap} mtc ON hstc.muc_tro_cap_id = mtc.id
                    WHERE hstc.id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in HoSoTroCap::findById: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy các hồ sơ trợ cấp của một đối tượng cụ thể
     * @param int $doi_tuong_id ID của đối tượng
     * @return array Danh sách hồ sơ hoặc mảng rỗng
     */
    public function findByDoiTuongId($doi_tuong_id) {
        try {
            $sql = "SELECT hstc.*, 
                           nd_duyet.ho_ten AS ten_nguoi_duyet,
                           mtc.ten_muc AS ten_muc_tro_cap
                    FROM {$this->table} hstc
                    LEFT JOIN {$this->table_nguoi_dung} nd_duyet ON hstc.nguoi_duyet_id = nd_duyet.id
                    LEFT JOIN {$this->table_muc_tro_cap} mtc ON hstc.muc_tro_cap_id = mtc.id
                    WHERE hstc.doi_tuong_id = :doi_tuong_id
                    ORDER BY hstc.ngay_tao DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':doi_tuong_id', $doi_tuong_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in HoSoTroCap::findByDoiTuongId: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Tạo hồ sơ trợ cấp mới
     * @param array $data Dữ liệu hồ sơ từ form
     * @return int|false ID hồ sơ mới hoặc false nếu lỗi
     */
    public function create($data) {
        $fields = [
            'doi_tuong_id', 'muc_tro_cap_id', 'ma_ho_so', 'ngay_de_nghi_huong', 
            'ngay_bat_dau_huong', 'ngay_ket_thuc_huong',
            'ly_do_tro_cap', 'muc_tro_cap_hang_thang',
            'nguoi_lap_id', 'nguoi_duyet_id', 'ngay_duyet', 'trang_thai', 
            'ly_do_thay_doi_trang_thai', 'file_dinh_kem_hs_path', 'ghi_chu_hs'
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
        
        // Xử lý các giá trị đặc biệt hoặc mặc định
        if (empty($params[':muc_tro_cap_id']) || !is_numeric($params[':muc_tro_cap_id'])) $params[':muc_tro_cap_id'] = null;
        if (empty($params[':nguoi_lap_id']) || !is_numeric($params[':nguoi_lap_id'])) $params[':nguoi_lap_id'] = null; // Nên lấy từ user đăng nhập
        if (empty($params[':nguoi_duyet_id']) || !is_numeric($params[':nguoi_duyet_id'])) $params[':nguoi_duyet_id'] = null;
        
        $params[':trang_thai'] = $data['trang_thai'] ?? 'cho_xem_xet';
        // muc_tro_cap_hang_thang (số tiền) nên được set từ muc_tro_cap_id trong controller trước khi gọi hàm này.

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute($params)) {
                return $this->db->lastInsertId();
            } else {
                error_log("HoSoTroCap creation failed: DB execution error. Info: " . print_r($stmt->errorInfo(), true) . " Params: " . print_r($params, true));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Database Error in HoSoTroCap::create: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
            return false;
        }
    }

    /**
     * Cập nhật thông tin hồ sơ trợ cấp
     * @param int $id ID hồ sơ
     * @param array $data Dữ liệu cập nhật từ form
     * @return bool True nếu thành công, False nếu lỗi
     */
    public function update($id, $data) {
        $allowedFields = [
            'muc_tro_cap_id', // Cho phép cập nhật mức trợ cấp
            // 'ma_ho_so',  // Thường không cho sửa mã hồ sơ
            'ngay_de_nghi_huong',
            'ngay_bat_dau_huong', 'ngay_ket_thuc_huong',
            'ly_do_tro_cap', 'muc_tro_cap_hang_thang', // Số tiền cụ thể
            // 'nguoi_lap_id', // Thường không cho sửa người lập
            'nguoi_duyet_id', 'ngay_duyet', 'trang_thai', 
            'ly_do_thay_doi_trang_thai',
            'file_dinh_kem_hs_path', 'ghi_chu_hs'
        ];
        $setParts = [];
        $params = [':id' => $id];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) { 
                $setParts[] = $field . ' = :' . $field;
                $params[':' . $field] = ($data[$field] === '') ? null : $data[$field];
            }
        }
        
        if (isset($params[':muc_tro_cap_id']) && (!is_numeric($params[':muc_tro_cap_id']) || empty($params[':muc_tro_cap_id']))) {
             $params[':muc_tro_cap_id'] = null;
        }
        if (isset($params[':nguoi_duyet_id']) && (!is_numeric($params[':nguoi_duyet_id']) || empty($params[':nguoi_duyet_id']))) {
             $params[':nguoi_duyet_id'] = null;
        }
        // muc_tro_cap_hang_thang (số tiền) nên được set từ muc_tro_cap_id trong controller nếu muc_tro_cap_id thay đổi

        if (empty($setParts)) {
            error_log("HoSoTroCap update failed: No fields to update for id {$id}.");
            return true; 
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database Error in HoSoTroCap::update for id {$id}: " . $e->getMessage() . " Params: " . print_r($params, true));
            return false;
        }
    }

    /**
     * Xóa hồ sơ trợ cấp
     * @param int $id ID hồ sơ
     * @return bool True nếu thành công, False nếu lỗi
     */
    public function delete($id) {
    /*
    // === TẠM THỜI VÔ HIỆU HÓA KHỐI CODE KIỂM TRA NÀY ===
    $checkSql = "SELECT COUNT(id) FROM chi_tra_hang_thang WHERE ho_so_tro_cap_id = :ho_so_id";
    $checkStmt = $this->db->prepare($checkSql);
    $checkStmt->bindParam(':ho_so_id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    if ($checkStmt->fetchColumn() > 0) {
        error_log("Cannot delete HoSoTroCap {$id} as it has related payment records in chi_tra_hang_thang.");
        return false;
    }
    */

    try {
        // Chỉ thực hiện lệnh xóa
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        if($e->getCode() == '23000'){ 
             error_log("Cannot delete HoSoTroCap {$id} due to foreign key constraint. Error: " . $e->getMessage());
        } else {
            error_log("Database Error in HoSoTroCap::delete for id {$id}: " . $e->getMessage());
        }
        return false;
    }
}

    /**
     * Kiểm tra xem mã hồ sơ đã tồn tại chưa (trừ chính nó khi update)
     * @param string $maHoSo Mã cần kiểm tra
     * @param int|null $excludeId ID của hồ sơ hiện tại (khi update) để loại trừ
     * @return bool True nếu tồn tại, False nếu không
     */
    public function maHoSoExists($maHoSo, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE ma_ho_so = :ma_ho_so";
            $params = [':ma_ho_so' => $maHoSo];
            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database Error in HoSoTroCap::maHoSoExists: " . $e->getMessage());
            return true; 
        }
    }
    
    /**
     * Kiểm tra xem đối tượng đã có hồ sơ trợ cấp nào đang trong quá trình xử lý hoặc đang hưởng chưa
     * (trạng thái: cho_xem_xet, cho_duyet, da_phe_duyet_dang_huong)
     * @param int $doiTuongId ID Đối tượng
     * @param int|null $excludeHoSoId ID hồ sơ hiện tại đang chỉnh sửa (để loại trừ chính nó)
     * @return bool True nếu có hồ sơ đang hoạt động/chờ, False nếu không
     */
    public function hasActiveOrPendingHoSoForDoiTuong($doiTuongId, $excludeHoSoId = null) {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} 
                    WHERE doi_tuong_id = :doi_tuong_id 
                    AND trang_thai IN ('cho_xem_xet', 'cho_duyet', 'da_phe_duyet_dang_huong')";
            $params = [':doi_tuong_id' => $doiTuongId];

            if ($excludeHoSoId !== null) {
                $sql .= " AND id != :exclude_ho_so_id";
                $params[':exclude_ho_so_id'] = $excludeHoSoId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database Error in HoSoTroCap::hasActiveOrPendingHoSoForDoiTuong: " . $e->getMessage());
            return true; 
        }
    }

    /**
     * Lấy danh sách các trạng thái hồ sơ trợ cấp
     * @return array
     */
    public function getTrangThaiOptions() {
        return [
            'cho_xem_xet' => 'Chờ xem xét',
            'cho_duyet' => 'Chờ duyệt',
            'da_phe_duyet_dang_huong' => 'Đã duyệt - Đang hưởng',
            'khong_du_dieu_kien' => 'Không đủ điều kiện',
            'tam_dung_huong' => 'Tạm dừng hưởng',
            'da_dung_huong' => 'Đã dừng hưởng',
            'da_chuyen_co_so_khac' => 'Đã chuyển cơ sở khác' // Thêm trạng thái mới từ CSDL
        ];
    }

    /**
     * Đếm tổng số hồ sơ trợ cấp
     * @return int
     */
    public function countAll() {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table}";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Error in HoSoTroCap::countAll: " . $e->getMessage());
            return 0;
        }
    }

    public function countByTrangThai($trangThai) {
        try {
            // Đúng: cột tên là `trang_thai`
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE trang_thai = :trang_thai";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':trang_thai', $trangThai);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) { 
            error_log("DB Error in HoSoTroCap::countByTrangThai: " . $e->getMessage());
            return 0; 
        }
    }

    /**
     * Lấy TOÀN BỘ danh sách hồ sơ để xuất file, có áp dụng bộ lọc.
     * Hàm này KHÔNG phân trang.
     * @param array $filters Mảng các điều kiện lọc (searchTerm, trang_thai, etc.)
     * @return array Mảng danh sách hồ sơ hoặc mảng rỗng nếu lỗi.
     */
    public function getAllForExport($filters = []) {
        try {
            // Câu lệnh SQL này giống hệt trong hàm getAll để lấy đủ thông tin
            $sql = "SELECT hstc.*, 
                           dt.ho_ten AS ten_doi_tuong, dt.ma_doi_tuong,
                           mtc.ten_muc AS ten_muc_tro_cap, mtc.ma_muc AS ma_muc_tro_cap,
                           nd_lap.ho_ten AS ten_nguoi_lap,
                           nd_duyet.ho_ten AS ten_nguoi_duyet
                    FROM ho_so_tro_cap hstc
                    LEFT JOIN doi_tuong dt ON hstc.doi_tuong_id = dt.id
                    LEFT JOIN muc_tro_cap_hang_thang mtc ON hstc.muc_tro_cap_id = mtc.id
                    LEFT JOIN nguoi_dung nd_lap ON hstc.nguoi_lap_id = nd_lap.id
                    LEFT JOIN nguoi_dung nd_duyet ON hstc.nguoi_duyet_id = nd_duyet.id";

            $whereClauses = [];
            $params = [];

            if (!empty($filters['searchTerm'])) {
                $whereClauses[] = "(hstc.ma_ho_so LIKE :searchTerm OR dt.ho_ten LIKE :searchTerm OR dt.ma_doi_tuong LIKE :searchTerm OR mtc.ten_muc LIKE :searchTerm)";
                $params[':searchTerm'] = '%' . $filters['searchTerm'] . '%';
            }
            if (!empty($filters['doi_tuong_id'])) {
                $whereClauses[] = "hstc.doi_tuong_id = :doi_tuong_id_filter";
                $params[':doi_tuong_id_filter'] = $filters['doi_tuong_id'];
            }
            if (!empty($filters['trang_thai'])) {
                $whereClauses[] = "hstc.trang_thai = :trang_thai_filter";
                $params[':trang_thai_filter'] = $filters['trang_thai'];
            }

            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(' AND ', $whereClauses);
            }

            // Sắp xếp nhưng KHÔNG GIỚI HẠN (LIMIT/OFFSET) số lượng
            $sql .= " ORDER BY hstc.ngay_tao DESC";

            $stmt = $this->db->prepare($sql);
            
            // Bind các tham số đã lọc
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Database Error in HoSoTroCap::getAllForExport: " . $e->getMessage());
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }

    /**
     * Tính tổng số tiền trợ cấp hàng tháng theo trạng thái
     * @param string $trangThai Trạng thái hồ sơ
     * @return float Tổng số tiền
     */
    public function sumMucTroCapByTrangThai($trangThai) {
        try {
            // Sử dụng cột 'muc_tro_cap_hang_thang' để tính tổng
            $sql = "SELECT SUM(muc_tro_cap_hang_thang) FROM {$this->table} WHERE trang_thai = :trang_thai";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':trang_thai', $trangThai);
            $stmt->execute();
            // Trả về float, nếu không có bản ghi nào thì fetchColumn trả về NULL, ép kiểu (float) sẽ thành 0.0
            return (float) $stmt->fetchColumn(); 
        } catch (PDOException $e) {
            error_log("Database Error in HoSoTroCap::sumMucTroCapByTrangThai: " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Đếm số lượng đối tượng (duy nhất) đang hưởng trợ cấp theo trạng thái
     * @param string $trangThai Trạng thái hồ sơ
     * @return int Số lượng đối tượng
     */
    public function countDistinctDoiTuongByTrangThai($trangThai) {
        try {
            $sql = "SELECT COUNT(DISTINCT doi_tuong_id) FROM {$this->table} WHERE trang_thai = :trang_thai";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':trang_thai', $trangThai);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Error in HoSoTroCap::countDistinctDoiTuongByTrangThai: " . $e->getMessage());
            return 0;
        }
    }
}
?>