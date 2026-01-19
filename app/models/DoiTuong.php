<?php
// app/models/DoiTuong.php

class DoiTuong {
    private $db;
    private $table = 'doi_tuong';
    private $table_loai_dt = 'loai_doi_tuong';

    public function __construct($db) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            throw new InvalidArgumentException('Invalid database connection provided to DoiTuong model.');
        }
    }

    /**
     * Lấy danh sách đối tượng với tìm kiếm và phân trang
     * // <<< THAY ĐỔI: Chuyển từ $searchTerm sang mảng $filters
     * @param array $filters Mảng các điều kiện lọc
     * @param int $limit Số lượng bản ghi mỗi trang
     * @param int $offset Vị trí bắt đầu lấy
     * @return array Mảng chứa 'data' và 'total'
     */
    public function getAll($filters = [], $limit = 15, $offset = 0) {
        try {
            // Câu SQL gốc vẫn giữ nguyên cấu trúc JOINs và subqueries
            $sqlData = "SELECT 
                            dt.*, 
                            ldt.ten_loai, 
                            nd.ho_ten as ten_nguoi_tiep_nhan_dt,
                            (SELECT COUNT(id) FROM ho_so_tro_cap WHERE doi_tuong_id = dt.id) as ho_so_tro_cap_count,
                            (SELECT COUNT(id) FROM ho_so_cham_soc_cong_dong WHERE doi_tuong_id = dt.id) as ho_so_cham_soc_count,
                            (SELECT COUNT(id) FROM ho_tro_khan_cap WHERE doi_tuong_id = dt.id) as ho_so_khan_cap_count
                        FROM {$this->table} dt
                        LEFT JOIN {$this->table_loai_dt} ldt ON dt.loai_doi_tuong_id = ldt.id
                        LEFT JOIN nguoi_dung nd ON dt.nguoi_tiep_nhan_dt_id = nd.id";
            
            $sqlCount = "SELECT COUNT(dt.id) 
                         FROM {$this->table} dt
                         LEFT JOIN {$this->table_loai_dt} ldt ON dt.loai_doi_tuong_id = ldt.id";

            $whereClauses = [];
            $params = [];

            // <<< THAY ĐỔI: Xây dựng điều kiện WHERE từ mảng $filters
            if (!empty($filters['searchTerm'])) {
                $whereClauses[] = "(dt.ho_ten LIKE :searchTerm OR dt.ma_doi_tuong LIKE :searchTerm OR dt.cccd LIKE :searchTerm)";
                $params[':searchTerm'] = '%' . $filters['searchTerm'] . '%';
            }
            if (!empty($filters['loai_doi_tuong_id']) && is_numeric($filters['loai_doi_tuong_id'])) {
                $whereClauses[] = "dt.loai_doi_tuong_id = :loai_doi_tuong_id_filter";
                $params[':loai_doi_tuong_id_filter'] = $filters['loai_doi_tuong_id'];
            }
            if (!empty($filters['trang_thai_doi_tuong'])) {
                $whereClauses[] = "dt.trang_thai_doi_tuong = :trang_thai_dt_filter";
                $params[':trang_thai_dt_filter'] = $filters['trang_thai_doi_tuong'];
            }
            if (!empty($filters['trang_thai_ho_so_dt'])) {
                $whereClauses[] = "dt.trang_thai_ho_so_dt = :trang_thai_filter";
                $params[':trang_thai_filter'] = $filters['trang_thai_ho_so_dt'];
            }

            if (!empty($whereClauses)) {
                $sqlData .= " WHERE " . implode(' AND ', $whereClauses);
                $sqlCount .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sqlData .= " ORDER BY dt.ngay_tao DESC LIMIT :limit OFFSET :offset";

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
            error_log("Database Error in DoiTuong::getAll: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }
    /**
     * Lấy thông tin chi tiết một đối tượng bằng ID
     * @param int $id ID đối tượng
     * @return array|false Mảng thông tin hoặc false nếu không tìm thấy/lỗi
     */
    public function findById($id) {
        try {
            $sql = "SELECT dt.*, ldt.ten_loai, nd.ho_ten as ten_nguoi_tiep_nhan_dt
                    FROM {$this->table} dt
                    LEFT JOIN {$this->table_loai_dt} ldt ON dt.loai_doi_tuong_id = ldt.id
                    LEFT JOIN nguoi_dung nd ON dt.nguoi_tiep_nhan_dt_id = nd.id
                    WHERE dt.id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in DoiTuong::findById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo đối tượng mới
     * @param array $data Dữ liệu đối tượng từ form
     * @return int|false ID đối tượng mới hoặc false nếu lỗi
     */
    public function create($data) {
        // Danh sách các cột có trong bảng `doi_tuong`
        $tableColumns = [
             'ma_doi_tuong', 'ho_ten', 'ngay_sinh', 'gioi_tinh', 'cccd',
             'dia_chi_thuong_tru', 'dia_chi_tam_tru', 'so_dien_thoai', 'thon_id',
             'loai_doi_tuong_id','trang_thai_doi_tuong',  'trang_thai_ho_so_dt', 
             'ngay_tiep_nhan_dt', 'nguoi_tiep_nhan_dt_id', 'ghi_chu', 'anh_dai_dien_path'
        ];

        $fields = [];
        $placeholders = [];
        $params = [];
        
        // Duyệt qua các cột của bảng để xây dựng câu lệnh SQL
        foreach ($tableColumns as $column) {
            if (array_key_exists($column, $data)) {
                $fields[] = $column;
                $placeholders[] = ':' . $column;
                // Chuẩn hóa giá trị: chuỗi rỗng thành null
                $params[':' . $column] = ($data[$column] === '' || $data[$column] === null) ? null : $data[$column];
            }
        }
        
        // Xử lý các trường hợp đặc biệt hoặc giá trị mặc định nếu cần
        // Ví dụ: nếu trang_thai_ho_so_dt không được gửi, set mặc định là 'moi_tao'
        if (!in_array('trang_thai_ho_so_dt', $fields)) {
            $fields[] = 'trang_thai_ho_so_dt';
            $placeholders[] = ':trang_thai_ho_so_dt';
            $params[':trang_thai_ho_so_dt'] = 'moi_tao';
        }
        
        // Kiểm tra nếu không có trường nào để insert thì báo lỗi
        if (empty($fields)) {
            error_log("DoiTuong creation failed: No valid fields to insert.");
            return false;
        }

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";

        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute($params)) {
                return $this->db->lastInsertId();
            } else {
                 $errorInfo = $stmt->errorInfo();
                 error_log("DoiTuong creation failed: DB execution error. Info: " . print_r($errorInfo, true) . " SQL: " . $sql . " Params: " . print_r($params, true));
                 return false;
            }
        } catch (PDOException $e) {
            error_log("Database Error in DoiTuong::create: " . $e->getMessage() . " (Code: " . $e->getCode() . ") SQL: $sql Params: " . print_r($params, true));
            return false;
        }
    }


    /**
     * Cập nhật thông tin đối tượng
     * @param int $id ID đối tượng
     * @param array $data Dữ liệu cập nhật từ form
     * @return bool True nếu thành công, False nếu lỗi
     */
    public function update($id, $data) {
         $allowedFields = [
             'ma_doi_tuong', 'ho_ten', 'ngay_sinh', 'gioi_tinh', 'cccd',
             'dia_chi_thuong_tru', 'dia_chi_tam_tru', 'so_dien_thoai', 'thon_id',
             'loai_doi_tuong_id',  'trang_thai_ho_so_dt', 
             'ngay_tiep_nhan_dt', 'ghi_chu', 'minh_chung_path'
         ];
         $setParts = [];
         $params = [':id' => $id];

         foreach($allowedFields as $field){
             if(array_key_exists($field, $data)){
                 $setParts[] = $field . ' = :' . $field;
                 $params[':' . $field] = ($data[$field] === '' || $data[$field] === null) ? null : $data[$field];
             }
         }
        
         if (empty($setParts)) {
             error_log("DoiTuong update failed: No fields to update for id {$id}.");
             return true; // Không có gì để cập nhật, coi như thành công
         }

         $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";

         try {
             $stmt = $this->db->prepare($sql);
             return $stmt->execute($params);
         } catch (PDOException $e) {
             error_log("Database Error in DoiTuong::update for id {$id}: " . $e->getMessage());
             return false;
         }
    }

    /**
     * Xóa đối tượng
     * @param int $id ID đối tượng
     * @return bool True nếu thành công, False nếu lỗi
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            if($e->getCode() == '23000'){
                 error_log("Cannot delete DoiTuong {$id} due to foreign key constraint: " . $e->getMessage());
            } else {
                 error_log("Database Error in DoiTuong::delete for id {$id}: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Lấy danh sách tất cả loại đối tượng (để làm dropdown)
     * @return array Mảng các loại đối tượng hoặc mảng rỗng nếu lỗi
     */
    public function getAllLoaiDoiTuong() {
         try {
            $sql = "SELECT id, ten_loai FROM {$this->table_loai_dt} ORDER BY ten_loai ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in DoiTuong::getAllLoaiDoiTuong: " . $e->getMessage());
            return [];
        }
    }

     /**
     * Kiểm tra xem mã đối tượng đã tồn tại chưa (trừ chính nó khi update)
     * @param string $maDoiTuong Mã cần kiểm tra
     * @param int|null $excludeId ID của đối tượng hiện tại (khi update) để loại trừ
     * @return bool True nếu tồn tại, False nếu không
     */
    public function maDoiTuongExists($maDoiTuong, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE ma_doi_tuong = :ma_doi_tuong";
            $params = [':ma_doi_tuong' => $maDoiTuong];
            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database Error in maDoiTuongExists: " . $e->getMessage());
            return true;
        }
    }

     /**
     * Kiểm tra xem CCCD đã tồn tại chưa (trừ chính nó khi update)
     * @param string $cccd CCCD cần kiểm tra
     * @param int|null $excludeId ID của đối tượng hiện tại (khi update) để loại trừ
     * @return bool True nếu tồn tại, False nếu không
     */
    public function cccdExists($cccd, $excludeId = null) {
         if (empty($cccd)) {
             return false;
         }
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE cccd = :cccd";
             $params = [':cccd' => $cccd];
            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
             error_log("Database Error in cccdExists: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Đếm tổng số đối tượng
     * @return int
     */
    public function countAll() {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table}";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Error in DoiTuong::countAll: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đếm số đối tượng theo loại (loai_doi_tuong_id)
     * @return array Mảng với ten_loai làm key và count làm value
     */
    public function countByLoaiDoiTuong() {
        try {
            $sql = "SELECT ldt.ten_loai, COUNT(dt.id) as count
                    FROM {$this->table} dt
                    LEFT JOIN {$this->table_loai_dt} ldt ON dt.loai_doi_tuong_id = ldt.id
                    WHERE dt.loai_doi_tuong_id IS NOT NULL
                    GROUP BY dt.loai_doi_tuong_id, ldt.ten_loai
                    ORDER BY ldt.ten_loai";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in DoiTuong::countByLoaiDoiTuong: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm số đối tượng có trạng thái hồ sơ cụ thể (trên bảng doi_tuong)
     * @param string $trangThaiHoSoDt
     * @return int
     */
    public function countByTrangThaiHoSoDt($trangThaiHoSoDt) {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE trang_thai_ho_so_dt = :trang_thai";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':trang_thai', $trangThaiHoSoDt);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Error in DoiTuong::countByTrangThaiHoSoDt: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tìm kiếm đối tượng để chọn (sử dụng cho AJAX/JSON)
     * @param string $term Từ khóa tìm kiếm
     * @param int $limit Giới hạn số lượng kết quả trả về
     * @return array Mảng các đối tượng tìm thấy
     */
    public function searchForSelection($term, $limit = 10) {
        try {
            $sql = "SELECT id, ho_ten, ma_doi_tuong, ngay_sinh
                    FROM {$this->table}
                    WHERE (ho_ten LIKE :term OR ma_doi_tuong LIKE :term OR cccd LIKE :term)
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $searchTerm = '%' . $term . '%';
            $stmt->bindParam(':term', $searchTerm, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Database Error in DoiTuong::searchForSelection: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách các trạng thái hồ sơ của đối tượng
     */
    public function getTrangThaiHoSoDtOptions() {
        return [
            'moi_tao' => 'Mới tạo', 
            'dang_xu_ly_thong_tin' => 'Đang xử lý thông tin', 
            'da_xac_minh' => 'Đã xác minh',
            'cho_duyet_ho_so' => 'Chờ duyệt hồ sơ', 
            'da_duyet_thong_tin' => 'Đã duyệt thông tin', 
            'bi_tu_choi_thong_tin' => 'Bị từ chối thông tin',
        ];
    }
   

    /**
     * Tìm kiếm đối tượng đủ điều kiện để tạo Hồ sơ Trợ cấp mới.
     * Loại trừ những đối tượng đã có hồ sơ đang hoạt động hoặc đang chờ.
     * @param string $term Từ khóa tìm kiếm
     * @param int $limit Giới hạn số lượng kết quả trả về
     * @return array Mảng các đối tượng tìm thấy
     */
    public function searchForSelectionEligibleForHstc($term, $limit = 10) {
        try {
            // Câu SQL này sử dụng NOT EXISTS để loại trừ các đối tượng đã có hồ sơ trợ cấp
            // ở các trạng thái 'cho_xem_xet', 'cho_duyet', hoặc 'da_phe_duyet_dang_huong'.
            $sql = "SELECT dt.id, dt.ho_ten, dt.ma_doi_tuong, dt.ngay_sinh
                    FROM {$this->table} dt
                    WHERE 
                        dt.trang_thai_ho_so_dt = 'da_duyet_thong_tin' -- <<< THÊM ĐIỀU KIỆN NÀY
                        AND (dt.ho_ten LIKE :term OR dt.ma_doi_tuong LIKE :term OR dt.cccd LIKE :term)
                        AND NOT EXISTS (
                            SELECT 1 
                            FROM ho_so_tro_cap hstc 
                            WHERE hstc.doi_tuong_id = dt.id 
                            AND hstc.trang_thai IN ('cho_xem_xet', 'cho_duyet', 'da_phe_duyet_dang_huong')
                        )
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $searchTerm = '%' . $term . '%';
            $stmt->bindParam(':term', $searchTerm, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Database Error in DoiTuong::searchForSelectionEligibleForHstc: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm đối tượng đủ điều kiện để tạo Hồ sơ Chăm sóc Cộng đồng mới.
     * Loại trừ những đối tượng đã có hồ sơ đang hoạt động hoặc đang chờ.
     * @param string $term Từ khóa tìm kiếm
     * @param int $limit Giới hạn số lượng kết quả trả về
     * @return array Mảng các đối tượng tìm thấy
     */
    public function searchForSelectionEligibleForHscc($term, $limit = 10) {
        try {
            // Tương tự hàm cho HSTC, nhưng kiểm tra trong bảng ho_so_cham_soc_cong_dong
            // và các trạng thái tương ứng ('cho_xem_xet', 'cho_duyet', 'da_phe_duyet')
            $sql = "SELECT dt.id, dt.ho_ten, dt.ma_doi_tuong, dt.ngay_sinh
                    FROM {$this->table} dt
                    WHERE 
                        dt.trang_thai_ho_so_dt = 'da_duyet_thong_tin' -- <<< THÊM ĐIỀU KIỆN NÀY
                        AND (dt.ho_ten LIKE :term OR dt.ma_doi_tuong LIKE :term OR dt.cccd LIKE :term)
                        AND NOT EXISTS (
                            SELECT 1 
                            FROM ho_so_cham_soc_cong_dong hscc 
                            WHERE hscc.doi_tuong_id = dt.id 
                            AND hscc.trang_thai_hs_cs IN ('cho_xem_xet', 'cho_duyet', 'da_phe_duyet')
                        )
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $searchTerm = '%' . $term . '%';
            $stmt->bindParam(':term', $searchTerm, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Database Error in DoiTuong::searchForSelectionEligibleForHscc: " . $e->getMessage());
            return [];
        }
    }

    
}