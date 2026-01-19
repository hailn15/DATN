# XÂY DỰNG HỆ THỐNG QUẢN LÝ ĐỐI TƯỢNG BẢO TRỢ XÃ HỘI

## Giới thiệu

Dự án xây dựng một hệ thống web giúp quản lý thông tin các đối tượng thuộc diện chính sách xã hội, bao gồm việc lưu trữ, cập nhật, thống kê và tra cứu dữ liệu nhanh chóng, chính xác.

## 💻 Công nghệ sử dụng

- **Ngôn ngữ lập trình:** PHP (>= 8.0)
- **Cơ sở dữ liệu:** MySQL (file `qlchinhsachdoituong.sql`)
- **Máy chủ chạy thử:** Apache hoặc PHP built-in server
- **IDE khuyến nghị:** Visual Studio Code
- **Cấu trúc dự án:**
  ```
  /app
  /config
  /core
  /public
  /src
  .htaccess
  ```

---

## Yêu cầu môi trường

Trước khi chạy, cần đảm bảo:

- Đã cài **XAMPP** hoặc **PHP CLI (>= 8.0)** trên máy.
- Đã import cơ sở dữ liệu `qlchinhsachdoituong.sql` vào **phpMyAdmin** (MySQL).
- Đã cấu hình đúng thông tin kết nối CSDL trong file `config/database.php`.

## Hướng dẫn chạy chương trình

### 🔹 Cách 1 – Chạy bằng XAMPP

1. Giải nén thư mục dự án vào:  
   `C:\xampp\htdocs\qlchinhsachdoituong`
2. Mở XAMPP → Start **Apache** và **MySQL**.
3. Truy cập trình duyệt:  
   👉 [http://localhost/qlchinhsachdoituong/public](http://localhost/qlchinhsachdoituong/public)

### 🔹 Cách 2 – Chạy bằng VS Code (PHP built-in server)

1. Mở thư mục dự án trong VS Code.
2. Mở Terminal (`Ctrl + ~`) và nhập lệnh:
   ```
   php -S localhost:8000 -t public
   ```
3. Mở trình duyệt → truy cập  
   👉 [http://localhost:8000](http://localhost:8000)

---

## Tài khoản mặc định

Tài khoản: admin
Mật khẩu: 123456

---

## Cấu trúc thư mục chính

```
├── app/               → Chứa controllers, models, views
├── config/            → Cấu hình chung và database
├── core/              → Thư viện lõi, định nghĩa hệ thống MVC
├── public/            → File index.php, CSS, JS, assets
├── src/               → Các file phụ trợ (nếu có)
├── .htaccess          → Cấu hình rewrite URL
└── README.md          → File hướng dẫn
```

---
