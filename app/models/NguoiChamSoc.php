<?php
// app/models/NguoiChamSoc.php

class NguoiChamSoc {
    private $db;
    private $table = 'nguoi_cham_soc';

    public function __construct($db) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            throw new InvalidArgumentException('Invalid database connection provided to NguoiChamSoc model.');
        }
    }

    /**
     * Lấy danh sách người chăm sóc với tìm kiếm và phân trang
     */
    public function getAll($filters = [], $limit = 15, $offset = 0) {
        try {
            $sqlData = "SELECT * FROM {$this->table}";
            $sqlCount = "SELECT COUNT(id) FROM {$this->table}";
            
            $whereClauses = [];
            $params = [];

            if (!empty($filters['searchTerm'])) {
                $whereClauses[] = "(ho_ten LIKE :searchTerm OR ma_nguoi_cham_soc LIKE :searchTerm OR cccd LIKE :searchTerm OR so_dien_thoai LIKE :searchTerm)";
                $params[':searchTerm'] = '%' . $filters['searchTerm'] . '%';
            }

            if (!empty($whereClauses)) {
                $whereCondition = " WHERE " . implode(' AND ', $whereClauses);
                $sqlData .= $whereCondition;
                $sqlCount .= $whereCondition;
            }

            $sqlData .= " ORDER BY ho_ten ASC LIMIT :limit OFFSET :offset";

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
            error_log("Database Error in NguoiChamSoc::getAll: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * Lấy thông tin chi tiết một người chăm sóc bằng ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in NguoiChamSoc::findById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo người chăm sóc mới
     */
    public function create($data) {
        $fields = ['ma_nguoi_cham_soc', 'ho_ten', 'ngay_sinh', 'gioi_tinh', 'cccd', 'dia_chi', 'so_dien_thoai', 'quan_he_voi_doi_tuong', 'ghi_chu'];
        $placeholders = [];
        $params = [];
        foreach ($fields as $field) {
            $placeholders[] = ':' . $field;
            $params[':' . $field] = empty($data[$field]) ? null : $data[$field];
        }
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database Error in NguoiChamSoc::create: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật thông tin người chăm sóc
     */
    public function update($id, $data) {
        $fields = ['ma_nguoi_cham_soc', 'ho_ten', 'ngay_sinh', 'gioi_tinh', 'cccd', 'dia_chi', 'so_dien_thoai', 'quan_he_voi_doi_tuong', 'ghi_chu'];
        $setParts = [];
        $params = [':id' => $id];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $setParts[] = "$field = :$field";
                $params[':' . $field] = empty($data[$field]) ? null : $data[$field];
            }
        }
        
        if (empty($setParts)) return true;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database Error in NguoiChamSoc::update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa người chăm sóc
     */
    public function delete($id) {
        try {
            // Ràng buộc khóa ngoại ON DELETE SET NULL sẽ xử lý các hồ sơ liên quan.
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in NguoiChamSoc::delete: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy tất cả người chăm sóc để hiển thị trong select/dropdown
     */
    public function getAllForSelect() {
        try {
            $sql = "SELECT id, ho_ten, cccd FROM {$this->table} ORDER BY ho_ten ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in NguoiChamSoc::getAllForSelect: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Kiểm tra mã người chăm sóc tồn tại
     */
    public function maNCSExists($ma, $excludeId = null) {
        if (empty($ma)) return false;
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE ma_nguoi_cham_soc = :ma";
            $params = [':ma' => $ma];
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) { return true; }
    }

    /**
     * Kiểm tra CCCD tồn tại
     */
    public function cccdExists($cccd, $excludeId = null) {
        if (empty($cccd)) return false;
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE cccd = :cccd";
            $params = [':cccd' => $cccd];
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) { return true; }
    }
}