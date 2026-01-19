<?php
// app/models/ThonXom.php

class ThonXom {
    private $db;
    private $table = 'thon_xom';

    public function __construct($db) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            throw new InvalidArgumentException('Invalid database connection provided to ThonXom model.');
        }
    }

    public function getAll($searchTerm = '', $limit = 15, $offset = 0) {
        try {
            $sqlData = "SELECT * FROM {$this->table}";
            $sqlCount = "SELECT COUNT(id) FROM {$this->table}";

            $whereClauses = [];
            $params = [];

            if (!empty($searchTerm)) {
                $whereClauses[] = "(ten_thon LIKE :term OR ma_thon LIKE :term)";
                $params[':term'] = '%' . $searchTerm . '%';
            }

            if (!empty($whereClauses)) {
                $sqlData .= " WHERE " . implode(' AND ', $whereClauses);
                $sqlCount .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sqlData .= " ORDER BY ten_thon ASC LIMIT :limit OFFSET :offset";

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
            error_log("Database Error in ThonXom::getAll: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    public function findById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in ThonXom::findById: " . $e->getMessage());
            return false;
        }
    }

    public function create($data) {
        if (empty($data['ten_thon'])) {
             error_log("ThonXom creation failed: Missing ten_thon.");
             return false;
        }

        $fields = ['ten_thon', 'ma_thon', 'mo_ta'];
        $sqlParams = [];
        $placeholders = [];

        foreach($fields as $field){
            if(isset($data[$field])){
                $sqlParams[':' . $field] = ($data[$field] === '') ? null : $data[$field];
                $placeholders[] = ':' . $field;
            } else {
                 // Nếu không có trong $data, không thêm vào câu SQL, DB sẽ dùng default hoặc NULL nếu cho phép
            }
        }
        // Đảm bảo các trường được chèn vào SQL khớp với các placeholders
        $insertFields = [];
        foreach($fields as $field){
            if(isset($data[$field])){
                $insertFields[] = $field;
            }
        }


        $sql = "INSERT INTO {$this->table} (" . implode(', ', $insertFields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute($sqlParams)) {
                return $this->db->lastInsertId();
            } else {
                 error_log("ThonXom creation failed: DB execution error. Info: " . print_r($stmt->errorInfo(), true) . " SQL: " . $sql . " Params: " . print_r($sqlParams, true));
                 return false;
            }
        } catch (PDOException $e) {
            error_log("Database Error in ThonXom::create: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
            return false;
        }
    }

    public function update($id, $data) {
         $allowedFields = ['ten_thon', 'ma_thon', 'mo_ta'];
         $setParts = [];
         $sqlParams = [':id' => $id];

         foreach($allowedFields as $field){
             if(array_key_exists($field, $data)){ // Dùng array_key_exists để cho phép truyền giá trị rỗng
                 $setParts[] = $field . ' = :' . $field;
                 $sqlParams[':' . $field] = ($data[$field] === '') ? null : $data[$field];
             }
         }

         if (empty($setParts)) {
             error_log("ThonXom update failed: No fields to update for id {$id}.");
             return true; // Hoặc false tùy theo logic, true nếu không có gì update cũng coi là thành công
         }

         $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";

         try {
             $stmt = $this->db->prepare($sql);
             return $stmt->execute($sqlParams);
         } catch (PDOException $e) {
             error_log("Database Error in ThonXom::update for id {$id}: " . $e->getMessage() . " SQL: " . $sql . " Params: " . print_r($sqlParams, true));
             return false;
         }
    }

    public function delete($id) {
        try {
            // Kiểm tra xem thôn có đang được sử dụng không
            $checkSql = "SELECT COUNT(id) FROM doi_tuong WHERE thon_id = :thon_id";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->bindParam(':thon_id', $id, PDO::PARAM_INT);
            $checkStmt->execute();
            $usageCount = $checkStmt->fetchColumn();

            if ($usageCount > 0) {
                // Thông báo hoặc xử lý (ví dụ, không cho xóa nếu đang được sử dụng,
                // mặc dù FK có ON DELETE SET NULL)
                // Hoặc, nếu muốn xóa và để FK tự xử lý, thì bỏ qua phần này.
                // Ở đây, chúng ta chỉ log lại
                error_log("ThonXom ID {$id} is in use by {$usageCount} doi_tuong records. Their thon_id will be set to NULL on delete.");
            }


            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in ThonXom::delete for id {$id}: " . $e->getMessage());
            return false;
        }
    }

    public function tenThonExists($tenThon, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE ten_thon = :ten_thon";
            $sqlParams = [':ten_thon' => $tenThon];
            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $sqlParams[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($sqlParams);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database Error in ThonXom::tenThonExists: " . $e->getMessage());
            return true;
        }
    }
    
    public function maThonExists($maThon, $excludeId = null) {
        if (empty($maThon)) return false;
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE ma_thon = :ma_thon";
            $sqlParams = [':ma_thon' => $maThon];
            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $sqlParams[':exclude_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($sqlParams);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database Error in ThonXom::maThonExists: " . $e->getMessage());
            return true;
        }
    }

    public function getAllThonXomForSelect() {
         try {
            $sql = "SELECT id, ten_thon, ma_thon FROM {$this->table} ORDER BY ten_thon ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in ThonXom::getAllThonXomForSelect: " . $e->getMessage());
            return [];
        }
    }
}
?>