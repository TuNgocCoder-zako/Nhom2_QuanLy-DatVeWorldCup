# FIFA World Cup 2030 - Hệ Thống Đặt Vé Trực Tuyến

Dự án đồ án môn học: Hệ thống quản lý và đặt vé xem bóng đá FIFA World Cup 2030 (Laravel 11, PHP 8.2+, MySQL).

---

## 🚀 Hướng Dẫn Cài Đặt & Chạy Dự Án

### 1. Yêu cầu môi trường
- **PHP**: >= 8.2 (hỗ trợ các extension cơ bản: `pdo_mysql`, `mbstring`, `gd`, `openssl`)
- **Composer**: Phiên bản 2.x
- **MySQL / MariaDB**: (XAMPP, WampServer hoặc Docker)

---

### 2. Các bước cài đặt chi tiết

**Bước 1: Clone mã nguồn về máy**
```bash
git clone https://github.com/TuNgocCoder-zako/Nhom2_QuanLy-DatVeWorldCup.git
cd Nhom2_QuanLy-DatVeWorldCup
```

**Bước 2: Cài đặt thư viện dependencies**
```bash
composer install
```

**Bước 3: Tạo file cấu hình `.env`**
```bash
cp .env.example .env
```

**Bước 4: Tạo mã khóa ứng dụng Laravel**
```bash
php artisan key:generate
```

**Bước 5: Thiết lập cơ sở dữ liệu**
1. Mở phpMyAdmin hoặc MySQL Client, tạo mới cơ sở dữ liệu:
   ```sql
   CREATE DATABASE worldcup_2030 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Mở file `.env` kiểm tra kết nối DB:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=worldcup_2030
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Lưu ý: Dùng XAMPP để trống `DB_PASSWORD=`. Dùng Docker hoặc MySQL có đặt pass thì điền mật khẩu tương ứng)*.

3. Chạy lệnh nạp dữ liệu:
   ```bash
   php artisan migrate
   ```
   *(Migration sẽ tự động nạp dữ liệu từ `demo1.sql`, khởi tạo 104 trận đấu, toàn bộ khán đài, ghế ngồi và giá vé)*.

**Bước 6: Khởi chạy website**
```bash
php artisan serve
```
Truy cập hệ thống tại: **http://localhost:8000**

---

## 📧 Cấu Hình Gửi Email Vé Điện Tử

Khi khách hàng thanh toán vé thành công, hệ thống tự động gửi email xác nhận vé (kèm mã QR soát vé) về địa chỉ email của người đặt.

Có 2 cách linh hoạt để kiểm tra tính năng này:

### Cách 1: Gửi qua Gmail thật (Khuyên dùng khi Demo)
Mở file `.env` và điền tài khoản Gmail cùng **Mật khẩu ứng dụng (App Password)** 16 ký tự:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_16_char_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="FIFA World Cup 2030"
```
*(Chi tiết các bước tạo App Password xem hướng dẫn bên trong file `.env.example`)*.

### Cách 2: Test nhanh không cần Gmail (Chế độ Log)
Nếu chỉ muốn kiểm tra code gửi mail mà không cần cấu hình Gmail thật, chỉnh trong `.env`:
```env
MAIL_MAILER=log
```
Khi đặt vé thành công, toàn bộ nội dung thư HTML và chi tiết vé sẽ được tự động ghi vào tệp: `storage/logs/laravel.log`.
