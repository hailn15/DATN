<?php
// app/models/LoaiHinhHoTroKhanCap.php

class LoaiHinhHoTroKhanCap {
    private $db;
    private $table = 'loai_hinh_ho_tro_khan_cap';
    private $table_van_ban = 'van_ban_chinh_sach';

    public function __construct($db) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            throw new InvalidArgumentException('Invalid database connection provided to LoaiHinhHoTroKhanCap model.');
        }
    }

    public function getAll($searchTerm = '', $trangThaiApDung = '', $limit = 15, $offset = 0) {
        try {
            $sqlData = "SELECT lhkc.*, vb.so_hieu AS so_hieu_van_ban, vb.ten_van_ban
                        FROM {$this->table} lhkc
                        LEFT JOIN {$this->table_van_ban} vb ON lhkc.van_ban_chinh_sach_id = vb.id";
            $sqlCount = "SELECT COUNT(lhkc.id) FROM {$this->table} lhkc";

            $whereClauses = [];
            $params = [];

            if (!empty($searchTerm)) {
                $whereClauses[] = "(lhkc.ma_loai_hinh LIKE :term OR lhkc.ten_loai_hinh LIKE :term)";
                $params[':term'] = '%' . $searchTerm . '%';
            }

            if (!empty($trangThaiApDung)) {
                $whereClauses[] = "lhkc.trang_thai_ap_dung = :trang_thai_ap_dung";
                $params[':trang_thai_ap_dung'] = $trangThaiApDung;
            }

            if (!empty($whereClauses)) {
                $sqlData .= " WHERE " . implode(' AND ', $whereClauses);
                $sqlCount .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sqlData .= " ORDER BY lhkc.ma_loai_hinh ASC, lhkc.ngay_tao DESC LIMIT :limit OFFSET :offset";

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
            error_log("Database Error in LoaiHinhHoTroKhanCap::getAll: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    public function findById($id) {
        try {
            $sql = "SELECT lhkc.*, vb.so_hieu AS so_hieu_van_ban, vb.ten_van_ban
                    FROM {$this->table} lhkc
                    LEFT JOIN {$this->table_van_ban} vb ON lhkc.van_ban_chinh_sach_id = vb.id
                    WHERE lhkc.id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in LoaiHinhHoTroKhanCap::findById: " . $e->getMessage());
            return false;
        }
    }

    public function create($data) {
        $fields = [
            'ma_loai_hinh', 'ten_loai_hinh', 'mo_ta',
            'muc_chuan', 'he_so', 'gia_tri_ho_tro_dinh_muc', // Thêm muc_chuan, he_so
            'don_vi_tinh_gt', 'mo_ta_hien_vat_dinh_muc',
            'van_ban_chinh_sach_id', 'ghi_chu_them_vb', 'trang_thai_ap_dung'
        ];
        
        $params = [];
        $placeholders = [];

        foreach ($fields as $field) {
            $placeholders[] = ':' . $field;
        }
        
        $params[':ma_loai_hinh'] = (isset($data['ma_loai_hinh']) && $data['ma_loai_hinh'] !== '') ? $data['ma_loai_hinh'] : null;
        $params[':ten_loai_hinh'] = (isset($data['ten_loai_hinh']) && $data['ten_loai_hinh'] !== '') ? $data['ten_loai_hinh'] : null;
        $params[':mo_ta'] = (isset($data['mo_ta']) && $data['mo_ta'] !== '') ? $data['mo_ta'] : null;

        $muc_chuan_val = (isset($data['muc_chuan']) && is_numeric($data['muc_chuan']) && $data['muc_chuan'] !== '') ? (float)$data['muc_chuan'] : null;
        $he_so_val = (isset($data['he_so']) && is_numeric($data['he_so']) && $data['he_so'] !== '') ? (float)$data['he_so'] : null;
        
        $params[':muc_chuan'] = $muc_chuan_val;
        $params[':he_so'] = $he_so_val;

        if ($muc_chuan_val !== null && $muc_chuan_val > 0 && $he_so_val !== null && $he_so_val > 0) {
            $params[':gia_tri_ho_tro_dinh_muc'] = round($muc_chuan_val * $he_so_val);
        } else {
            $params[':gia_tri_ho_tro_dinh_muc'] = (isset($data['gia_tri_ho_tro_dinh_muc']) && is_numeric($data['gia_tri_ho_tro_dinh_muc']) && $data['gia_tri_ho_tro_dinh_muc'] !== '') ? (float)$data['gia_tri_ho_tro_dinh_muc'] : null; // Cho phép null
        }
        
        $params[':don_vi_tinh_gt'] = (isset($data['don_vi_tinh_gt']) && $data['don_vi_tinh_gt'] !== '') ? $data['don_vi_tinh_gt'] : 'VNĐ';
        $params[':mo_ta_hien_vat_dinh_muc'] = (isset($data['mo_ta_hien_vat_dinh_muc']) && $data['mo_ta_hien_vat_dinh_muc'] !== '') ? $data['mo_ta_hien_vat_dinh_muc'] : null;
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
                 error_log("LoaiHinhHoTroKhanCap creation failed: DB execution error. Info: " . print_r($stmt->errorInfo(), true) . " SQL: " . $sql . " Params: " . print_r($params, true));
                 return false;
            }
        } catch (PDOException $e) {
            error_log("Database Error in LoaiHinhHoTroKhanCap::create: " . $e->getMessage() . " (Code: " . $e->getCode() . ") SQL: " . $sql . " Params: " . print_r($params, true));
            return false;
        }
    }

    public function update($id, $data) {
         $allowedFields = [
            'ma_loai_hinh', 'ten_loai_hinh', 'mo_ta', 
            'muc_chuan', 'he_so', 'gia_tri_ho_tro_dinh_muc',
            'don_vi_tinh_gt', 'mo_ta_hien_vat_dinh_muc',
            'van_ban_chinh_sach_id', 'ghi_chu_them_vb', 'trang_thai_ap_dung'
         ];
         
         $setParts = [];
         $params = [':id' => $id];
         $processedData = [];

        $muc_chuan_from_form = (array_key_exists('muc_chuan', $data) && $data['muc_chuan'] !== '' && is_numeric($data['muc_chuan'])) ? (float)$data['muc_chuan'] : null;
        if (array_key_exists('muc_chuan', $data)){
            $processedData['muc_chuan'] = ($data['muc_chuan'] === '' || !is_numeric($data['muc_chuan'])) ? null : (float)$data['muc_chuan'];
        } else {
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

        if ( ($muc_chuan_from_form !== null && $muc_chuan_from_form > 0 && $he_so_from_form !== null && $he_so_from_form > 0) &&
             (array_key_exists('muc_chuan', $data) || array_key_exists('he_so', $data))
           ) {
            $processedData['gia_tri_ho_tro_dinh_muc'] = round($muc_chuan_from_form * $he_so_from_form);
        } elseif (array_key_exists('gia_tri_ho_tro_dinh_muc', $data)) {
             $processedData['gia_tri_ho_tro_dinh_muc'] = ($data['gia_tri_ho_tro_dinh_muc'] === '' || !is_numeric($data['gia_tri_ho_tro_dinh_muc'])) ? null : (float)$data['gia_tri_ho_tro_dinh_muc'];
        }

        foreach($allowedFields as $field){
            if ($field === 'gia_tri_ho_tro_dinh_muc' || $field === 'muc_chuan' || $field === 'he_so') {
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
             error_log("Database Error in LoaiHinhHoTroKhanCap::update for id {$id}: " . $e->getMessage() . " SQL: " . $sql . " Params: " . print_r($params, true));
             return false;
         }
    }

    public function delete($id) {
        try {
            $checkSql = "SELECT COUNT(id) FROM ho_tro_khan_cap WHERE loai_hinh_ho_tro_kc_id = :lh_id";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->bindParam(':lh_id', $id, PDO::PARAM_INT);
            $checkStmt->execute();
            if ($checkStmt->fetchColumn() > 0) {
                error_log("Cannot delete LoaiHinhHoTroKhanCap {$id} as it is currently in use.");
                return false; 
            }

            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                error_log("Cannot delete LoaiHinhHoTroKhanCap {$id} due to foreign key constraint: " . $e->getMessage());
            } else {
                error_log("Database Error in LoaiHinhHoTroKhanCap::delete for id {$id}: " . $e->getMessage());
            }
            return false;
        }
    }

    public function maLoaiHinhExists($maLoaiHinh, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE ma_loai_hinh = :ma_loai_hinh";
            $params = [':ma_loai_hinh' => $maLoaiHinh];
            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database Error in LoaiHinhHoTroKhanCap::maLoaiHinhExists: " . $e->getMessage());
            return true; 
        }
    }
    
    public function getAllLoaiHinhHoTroKCForSelect() {
        try {
            $sql = "SELECT id, ten_loai_hinh, ma_loai_hinh, gia_tri_ho_tro_dinh_muc, muc_chuan, he_so, mo_ta_hien_vat_dinh_muc 
                    FROM {$this->table} 
                    WHERE trang_thai_ap_dung = 'dang_ap_dung' 
                    ORDER BY ten_loai_hinh ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in LoaiHinhHoTroKhanCap::getAllLoaiHinhHoTroKCForSelect: " . $e->getMessage());
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