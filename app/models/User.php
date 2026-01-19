<?php
// app/models/User.php

class User {
    private $db;
    private $table = 'nguoi_dung';

    /**
     * Constructor - Nhận kết nối CSDL
     * @param PDO $db Đối tượng kết nối PDO
     */
    public function __construct($db) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
             // Xử lý lỗi nếu $db không phải là đối tượng PDO hợp lệ
             throw new InvalidArgumentException('Invalid database connection provided to User model.');
        }
    }

    /**
     * Tìm người dùng bằng tên đăng nhập (QUAN TRỌNG - ĐÃ BỊ THIẾU)
     * @param string $username Tên đăng nhập cần tìm
     * @return array|false Trả về mảng thông tin user hoặc false nếu không tìm thấy/lỗi
     */
    public function findByUsername($username) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE ten_dang_nhap = :username LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            // fetch() trả về false nếu không có dòng nào khớp
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Ghi log lỗi chi tiết để debug
            error_log("Database Error in findByUsername: " . $e->getMessage());
            return false; // Trả về false khi có lỗi CSDL
        }
    }

    /**
     * Tìm người dùng bằng ID
     * @param int $id ID người dùng
     * @return array|false
     */
    public function findById($id) {
         try {
            // Chỉ chọn các cột cần thiết, không nên lấy mật khẩu trừ khi thực sự cần
            $sql = "SELECT id, ten_dang_nhap, ho_ten, quyen, ngay_tao FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in findById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách tất cả người dùng (có thể thêm phân trang, tìm kiếm sau)
     * @return array|false Mảng các user hoặc false nếu có lỗi
     */
    public function getAllUsers() {
         try {
            // Không lấy cột mật khẩu trong danh sách chung
            $sql = "SELECT id, ten_dang_nhap, ho_ten, quyen, ngay_tao FROM {$this->table} ORDER BY ngay_tao DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in getAllUsers: " . $e->getMessage());
            return false;
        }
    }

     /**
     * Tạo người dùng mới (Lưu mật khẩu dạng plain text - KHÔNG AN TOÀN!)
     * @param array $data Mảng chứa dữ liệu ['ten_dang_nhap', 'mat_khau_plain', 'ho_ten', 'quyen']
     * @return int|false ID của người dùng mới tạo hoặc false nếu lỗi
     */
    public function create($data) {
         if (empty($data['ten_dang_nhap']) || empty($data['mat_khau_plain']) || empty($data['ho_ten'])) {
             error_log("User creation failed: Missing required data.");
            return false;
        }

        // CẢNH BÁO: Lưu mật khẩu không mã hóa!
        $plainPassword = $data['mat_khau_plain'];

        try {
            $sql = "INSERT INTO {$this->table} (ten_dang_nhap, mat_khau, ho_ten, quyen) VALUES (:username, :password, :fullname, :role)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':username', $data['ten_dang_nhap']);
            $stmt->bindParam(':password', $plainPassword, PDO::PARAM_STR); // Lưu plain text
            $stmt->bindParam(':fullname', $data['ho_ten']);
            $role = $data['quyen'] ?? 'canbo';
            $stmt->bindParam(':role', $role);

            if ($stmt->execute()) {
                 return $this->db->lastInsertId();
            } else {
                 error_log("User creation failed: DB execution error. Info: " . print_r($stmt->errorInfo(), true));
                 return false;
            }

        } catch (PDOException $e) {
            error_log("Database Error in create user: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
            return false;
        }
    }

    /**
     * Cập nhật thông tin người dùng (KHÔNG hash pass nếu được cung cấp)
     * @param int $id ID người dùng
     * @param array $data Dữ liệu cập nhật (có thể có 'mat_khau_plain')
     * @return bool Thành công hay không
     */
     public function update($id, $data) {
         $fields = [];
         $params = [':id' => $id];

         if (isset($data['ho_ten'])) { $fields[] = 'ho_ten = :fullname'; $params[':fullname'] = $data['ho_ten']; }
         if (isset($data['quyen'])) { $fields[] = 'quyen = :role'; $params[':role'] = $data['quyen']; }
         // Cập nhật mật khẩu (KHÔNG HASH) nếu được cung cấp và không rỗng
         if (isset($data['mat_khau_plain']) && $data['mat_khau_plain'] !== '') {
              $fields[] = 'mat_khau = :password';
              $params[':password'] = $data['mat_khau_plain']; // Lưu plain text
         }

         if (empty($fields)) {
             // Không có trường nào để cập nhật
             return true; // Hoặc false tùy logic mong muốn, true có vẻ hợp lý hơn
         }

         $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";

         try {
             $stmt = $this->db->prepare($sql);
             return $stmt->execute($params);
         } catch (PDOException $e) {
             error_log("Database Error updating user {$id}: " . $e->getMessage());
             return false;
         }
     }

     /**
     * Xóa người dùng
     * @param int $id
     * @return bool
     */
     public function delete($id) {
          try {
            // Có thể thêm kiểm tra xem có nên cho xóa admin cuối cùng không, v.v.
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Xử lý trường hợp không xóa được do khóa ngoại nếu cần
            error_log("Database Error deleting user {$id}: " . $e->getMessage());
            return false;
        }
     }

     /**
     * Lấy danh sách người dùng với tìm kiếm và phân trang
     * @param array $filters Mảng các điều kiện lọc (searchTerm, quyen)
     * @param int $limit Số lượng bản ghi mỗi trang
     * @param int $offset Vị trí bắt đầu lấy
     * @return array Mảng chứa 'data' (danh sách) và 'total' (tổng số bản ghi khớp)
     */
    public function getAll($filters = [], $limit = 15, $offset = 0) {
        try {
            // Không lấy cột mật khẩu
            $sqlData = "SELECT id, ten_dang_nhap, ho_ten, email, so_dien_thoai, quyen, trang_thai, ngay_tao FROM {$this->table}";
            $sqlCount = "SELECT COUNT(id) FROM {$this->table}";

            $whereClauses = [];
            $params = [];

            if (!empty($filters['searchTerm'])) {
                $whereClauses[] = "(ten_dang_nhap LIKE :searchTerm OR ho_ten LIKE :searchTerm OR email LIKE :searchTerm)";
                $params[':searchTerm'] = '%' . $filters['searchTerm'] . '%';
            }
            if (!empty($filters['quyen'])) {
                $whereClauses[] = "quyen = :quyen_filter";
                $params[':quyen_filter'] = $filters['quyen'];
            }

            if (!empty($whereClauses)) {
                $sqlData .= " WHERE " . implode(' AND ', $whereClauses);
                $sqlCount .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sqlData .= " ORDER BY ngay_tao DESC LIMIT :limit OFFSET :offset";

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
            error_log("Database Error in User::getAll: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * Thay đổi trạng thái người dùng (chặn/mở chặn)
     * @param int $id ID người dùng
     * @return bool
     */
    public function toggleStatus($id) {
        // Câu lệnh SQL `1 - trang_thai` sẽ đảo ngược giá trị 0 thành 1 và 1 thành 0
        $sql = "UPDATE {$this->table} SET trang_thai = 1 - trang_thai WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error toggling status for user {$id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm số lượng admin đang hoạt động
     * @return int
     */
    public function countActiveAdmins() {
        try {
            $sql = "SELECT COUNT(id) FROM {$this->table} WHERE quyen = 'admin' AND trang_thai = 1";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Error in countActiveAdmins: " . $e->getMessage());
            return 0;
        }
    }

}
?>