<?php
// app/models/MucTroCapHangThang.php

class MucTroCapHangThang {
    private $db;
    private $table = 'muc_tro_cap_hang_thang';
    private $table_van_ban = 'van_ban_chinh_sach'; // Để join lấy tên văn bản

    public function __construct($db) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            throw new InvalidArgumentException('Invalid database connection provided to MucTroCapHangThang model.');
        }
    }

    public function getAll($searchTerm = '', $trangThaiApDung = '', $limit = 15, $offset = 0) {
        try {
            $sqlData = "SELECT mtc.*, vb.so_hieu AS so_hieu_van_ban, vb.ten_van_ban
                        FROM {$this->table} mtc
                        LEFT JOIN {$this->table_van_ban} vb ON mtc.van_ban_chinh_sach_id = vb.id";
            $sqlCount = "SELECT COUNT(mtc.id) FROM {$this->table} mtc";

            $whereClauses = [];
            $params = [];

            if (!empty($searchTerm)) {
                $whereClauses[] = "(mtc.ma_muc LIKE :term OR mtc.ten_muc LIKE :term)";
                $params[':term'] = '%' . $searchTerm . '%';
            }

            if (!empty($trangThaiApDung)) {
                $whereClauses[] = "mtc.trang_thai_ap_dung = :trang_thai_ap_dung";
                $params[':trang_thai_ap_dung'] = $trangThaiApDung;
            }

            if (!empty($whereClauses)) {
                $sqlData .= " WHERE " . implode(' AND ', $whereClauses);
                $sqlCount .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sqlData .= " ORDER BY mtc.ma_muc ASC, mtc.ngay_tao DESC LIMIT :limit OFFSET :offset";

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
            error_log("Database Error in MucTroCapHangThang::getAll: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    public function findById($id) {
        try {
            $sql = "SELECT mtc.*, vb.so_hieu AS so_hieu_van_ban, vb.ten_van_ban
                    FROM {$this->table} mtc
                    LEFT JOIN {$this->table_van_ban} vb ON mtc.van_ban_chinh_sach_id = vb.id
                    WHERE mtc.id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in MucTroCapHangThang::findById: " . $e->getMessage());
            return false;
        }
    }

    public function create($data) {
        $fields = [
            'ma_muc', 'ten_muc', 'mo_ta', 
            'muc_chuan', 'he_so', 'so_tien_ap_dung', // Thêm muc_chuan, he_so
            'don_vi_tinh', 'van_ban_chinh_sach_id', 'ghi_chu_them_vb', 'trang_thai_ap_dung'
        ];
        
        $params = [];
        $placeholders = [];

        // Khởi tạo params và placeholders
        foreach ($fields as $field) {
            $placeholders[] = ':' . $field;
        }
        
        // Xử lý giá trị đầu vào và tính toán
        $params[':ma_muc'] = (isset($data['ma_muc']) && $data['ma_muc'] !== '') ? $data['ma_muc'] : null;
        $params[':ten_muc'] = (isset($data['ten_muc']) && $data['ten_muc'] !== '') ? $data['ten_muc'] : null;
        $params[':mo_ta'] = (isset($data['mo_ta']) && $data['mo_ta'] !== '') ? $data['mo_ta'] : null;
        
        $muc_chuan_val = (isset($data['muc_chuan']) && is_numeric($data['muc_chuan']) && $data['muc_chuan'] !== '') ? (float)$data['muc_chuan'] : null;
        $he_so_val = (isset($data['he_so']) && is_numeric($data['he_so']) && $data['he_so'] !== '') ? (float)$data['he_so'] : null;

        $params[':muc_chuan'] = $muc_chuan_val;
        $params[':he_so'] = $he_so_val;

        if ($muc_chuan_val !== null && $muc_chuan_val > 0 && $he_so_val !== null && $he_so_val > 0) {
            $params[':so_tien_ap_dung'] = round($muc_chuan_val * $he_so_val);
        } else {
            $params[':so_tien_ap_dung'] = (isset($data['so_tien_ap_dung']) && is_numeric($data['so_tien_ap_dung']) && $data['so_tien_ap_dung'] !== '') ? (float)$data['so_tien_ap_dung'] : 0.00;
        }
        
        $params[':don_vi_tinh'] = (isset($data['don_vi_tinh']) && $data['don_vi_tinh'] !== '') ? $data['don_vi_tinh'] : 'VNĐ/tháng';
        $params[':van_ban_chinh_sach_id'] = (isset($data['van_ban_chinh_sach_id']) && is_numeric($data['van_ban_chinh_sach_id']) && $data['van_ban_chinh_sach_id'] !== '') ? $data['van_ban_chinh_sach_id'] : null;
        $params[':ghi_chu_them_vb'] = (isset($data['ghi_chu_them_vb']) && $data['ghi_chu_them_vb'] !== '') ? $data['ghi_chu_them_vb'] : null;
        $params[':trang_thai_ap_dung'] = $data['trang_thai_ap_dung'] ?? 'dang_ap_dung';

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute($params)) {
                return $this->db->lastInsertId();
            } else {
                 error_log("MucTroCapHangThang creation failed: DB execution error. Info: " . print_r($stmt->errorInfo(), true) . " SQL: " . $sql . " Params: " . print_r($params, true));
                 return false;
            }
        } catch (PDOException $e) {
            error_log("Database Error in MucTroCapHangThang::create: " . $e->getMessage() . " (Code: " . $e->getCode() . ")" . " SQL: " . $sql . " Params: " . print_r($params, true));
            return false;
        }
    }

    public function update($id, $data) {
         $allowedFields = [
            'ma_muc', 'ten_muc', 'mo_ta', 
            'muc_chuan', 'he_so', 'so_tien_ap_dung',
            'don_vi_tinh', 'van_ban_chinh_sach_id', 'ghi_chu_them_vb', 'trang_thai_ap_dung'
         ];
         
         $setParts = [];
         $params = [':id' => $id];
         $processedData = [];

        // Xử lý muc_chuan, he_so và tính so_tien_ap_dung
        $muc_chuan_from_form = (array_key_exists('muc_chuan', $data) && $data['muc_chuan'] !== '' && is_numeric($data['muc_chuan'])) ? (float)$data['muc_chuan'] : null;
        // Nếu muc_chuan gửi lên là rỗng, thì set là null. Nếu không có trong $data, thì không cập nhật.
        if (array_key_exists('muc_chuan', $data)){
            $processedData['muc_chuan'] = ($data['muc_chuan'] === '' || !is_numeric($data['muc_chuan'])) ? null : (float)$data['muc_chuan'];
        } else {
            // Nếu không có trong $data, lấy giá trị cũ để tính toán nếu cần
            $current = $this->findById($id);
            $muc_chuan_from_form = $current['muc_chuan'] !== null ? (float)$current['muc_chuan'] : null;
        }

        $he_so_from_form = (array_key_exists('he_so', $data) && $data['he_so'] !== '' && is_numeric($data['he_so'])) ? (float)$data['he_so'] : null;
        if (array_key_exists('he_so', $data)){
            $processedData['he_so'] = ($data['he_so'] === '' || !is_numeric($data['he_so'])) ? null : (float)$data['he_so'];
        } else {
            if (!isset($current)) $current = $this->findById($id);
            $he_so_from_form = $current['he_so'] !== null ? (float)$current['he_so'] : null;
        }

        // Chỉ tính toán và cập nhật so_tien_ap_dung nếu muc_chuan hoặc he_so có thay đổi và dẫn đến tính toán mới
        // Hoặc nếu so_tien_ap_dung được gửi trực tiếp từ form
        if ( ($muc_chuan_from_form !== null && $muc_chuan_from_form > 0 && $he_so_from_form !== null && $he_so_from_form > 0) &&
             (array_key_exists('muc_chuan', $data) || array_key_exists('he_so', $data)) // Một trong hai phải được gửi để trigger tính toán
           ) {
            $processedData['so_tien_ap_dung'] = round($muc_chuan_from_form * $he_so_from_form);
        } elseif (array_key_exists('so_tien_ap_dung', $data)) {
             $processedData['so_tien_ap_dung'] = (is_numeric($data['so_tien_ap_dung']) && $data['so_tien_ap_dung'] !== '') ? (float)$data['so_tien_ap_dung'] : 0.00;
        }

        // Xử lý các trường còn lại
        foreach($allowedFields as $field){
            if ($field === 'so_tien_ap_dung' || $field === 'muc_chuan' || $field === 'he_so') {
                continue; 
            }
            if(array_key_exists($field, $data)){
                if ($field === 'van_ban_chinh_sach_id') {
                     $processedData[$field] = (is_numeric($data[$field]) && $data[$field] !== '') ? $data[$field] : null;
                } else {
                     $processedData[$field] = ($data[$field] === '') ? null : $data[$field];
                }
            }
        }

         if (empty($processedData)) {
             error_log("MucTroCapHangThang update failed: No valid fields to update for id {$id}.");
             return true; 
         }

         foreach ($processedData as $field => $value) {
            $setParts[] = $field . ' = :' . $field;
            $params[':' . $field] = $value;
         }

         $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";

         try {
             $stmt = $this->db->prepare($sql);
             return $stmt->execute($params);
         } catch (PDOException $e) {
             error_log("Database Error in MucTroCapHangThang::update for id {$id}: " . $e->getMessage() . " SQL: " . $sql . " Params: " . print_r($params, true));
             return false;
         }
    }

    public function delete($id) {
        try {
            $checkSql = "SELECT COUNT(id) FROM ho_so_tro_cap WHERE muc_tro_cap_id = :muc_id";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->bindParam(':muc_id', $id, PDO::PARAM_INT);
            $checkStmt->execute();
            if ($checkStmt->fetchColumn() > 0) {
                error_log("Cannot delete MucTroCapHangThang {$id} as it is currently in use by ho_so_tro_cap records.");
                return false;
            }

            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                error_log("Cannot delete MucTroCapHangThang {$id} due to foreign key constraint: " . $e->getMessage());
            } else {
                error_log("Database Error in MucTroCapHangThang::delete for id {$id}: " . $e->getMessage());
            }
            return false;
        }
    }

    public function maMucExists($maMuc, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE ma_muc = :ma_muc";
            $params = [':ma_muc' => $maMuc];
            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database Error in MucTroCapHangThang::maMucExists: " . $e->getMessage());
            return true; 
        }
    }
    
    public function getAllMucTroCapForSelect() {
        try {
            $sql = "SELECT id, ten_muc, ma_muc, so_tien_ap_dung, muc_chuan, he_so 
                    FROM {$this->table} 
                    WHERE trang_thai_ap_dung = 'dang_ap_dung' 
                    ORDER BY ten_muc ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in MucTroCapHangThang::getAllMucTroCapForSelect: " . $e->getMessage());
            return [];
        }
    }

    public function getTrangThaiApDungOptions() {
        return [
            'dang_ap_dung' => 'Đang áp dụng',
            'ngung_ap_dung' => 'Ngưng áp dụng'
        ];
    }
}
?>