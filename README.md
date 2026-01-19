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
## 📸 Hình ảnh Demo
![Giao diện đăng nhập]
<img width="454" height="184" alt="image" src="https://github.com/user-attachments/assets/10d6c8c3-800e-4cd3-a089-344efb84b76f" />

![Giao diện trang chủ]
<img width="430" height="201" alt="image" src="https://github.com/user-attachments/assets/e25ecfee-1b53-41a6-8669-ff5cdb5b67c8" />

![Giao diện quản lý hồ sơ đổi tượng]
<img width="454" height="210" alt="image" src="https://github.com/user-attachments/assets/bdd87967-f942-41bd-a06c-4d3eeccb28fd" />

![Giao diện quản lý hỗ trợ thường xuyên]
<img width="454" height="213" alt="image" src="https://github.com/user-attachments/assets/dd932057-9d66-4a43-ac55-d58ecded3435" />

![Giao diện hỗ trợ khẩn cấp]
<img width="2239" height="1199" alt="image" src="https://github.com/user-attachments/assets/30fd0625-37a2-4f67-8137-e85c96fbd6cf" />

![Giao diện chăm sóc tại cồng đồng]
<img width="2239" height="1198" alt="image" src="https://github.com/user-attachments/assets/eeacf69e-1e18-4c14-ba09-fdc08eaa01bb" />

![Giao diện quản lý chíng sách]
<img width="2237" height="1189" alt="image" src="https://github.com/user-attachments/assets/e68ed541-c665-4480-b3fb-056e1ac7adde" />


![Giao diện quản lý địa phương]
<img width="2239" height="1199" alt="image" src="https://github.com/user-attachments/assets/9373009a-9ad6-4583-ae18-19916499a5b5" />

![Giao diện quản lý người dùng]
<img width="2239" height="1195" alt="image" src="https://github.com/user-attachments/assets/16309fa3-6784-444b-819b-290eebbccaae" />

---
