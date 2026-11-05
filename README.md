# FIFA World Cup 2030 - Hệ Thống Đặt Vé Trực Tuyến

Dự án đồ án môn học: Hệ thống quản lý và đặt vé xem bóng đá FIFA World Cup 2030 (Laravel 11, PHP 8.2+, MySQL).

---

## 🚀 Hướng Dẫn Cài Đặt & Khởi Chạy Dự Án

---

### PHƯƠNG PHÁP 1: Quy trình chuẩn trên Windows (sử dụng XAMPP & PowerShell)

Quy trình hoàn chỉnh từ lúc clone dự án từ GitHub đến khi khởi chạy thành công:

#### Bước 1: Chuẩn bị môi trường PHP trong XAMPP
Mặc định XAMPP tắt một số extension bắt buộc của Composer và Laravel:
1. Mở file `C:\xampp\php\php.ini` (hoặc vào **XAMPP Control Panel** -> dòng Apache -> **Config** -> **PHP (php.ini)**).
2. Tìm và kích hoạt 2 extension bằng cách xóa dấu chấm phẩy `;` ở đầu dòng:
   ```ini
   extension=openssl
   extension=zip
   ```
3. Lưu file (`Ctrl + S`) và khởi động lại Apache trong XAMPP.
* **Kiểm tra:** Mở PowerShell/Terminal gõ:
  ```powershell
  php -m | findstr -i "zip openssl"
  ```
  *(Màn hình in ra cả 2 tên extension là thành công)*.

---

#### Bước 2: Cài đặt Composer
1. Tải **Composer-Setup.exe** từ trang chủ [getcomposer.org](https://getcomposer.org/download/).
2. Trong quá trình cài đặt, chọn đường dẫn `php.exe` đến: `C:\xampp\php\php.exe`.
3. Tắt hoàn toàn VS Code / Terminal đang mở và mở lại để hệ thống nhận diện biến môi trường `PATH`.
* **Kiểm tra:** Gõ `composer -v` hiện ra thông tin phiên bản Composer.

---

#### Bước 3: Cài đặt thư viện dự án (Composer Install)
Mở PowerShell tại thư mục dự án và tải thư mục vendor:
```powershell
cd C:\xampp\htdocs\Nhom2_QuanLy-DatVeWorldCup
composer install
```
* **Kiểm tra:** Thư mục `vendor/` được tạo đầy đủ trong dự án và không có thông báo lỗi giải nén gói.

---

#### Bước 4: Khởi tạo các thư mục lưu cache & phân quyền
Dự án clone từ Git thường bị thiếu thư mục lưu cache tạm do `.gitignore`:
```powershell
New-Item -ItemType Directory -Force -Path "storage\framework\views", "storage\framework\cache\data", "storage\framework\sessions", "storage\logs", "bootstrap\cache"
```
* **Kiểm tra:** Trong `storage/framework/` có đầy đủ các thư mục `views`, `cache`, `sessions`.

---

#### Bước 5: Cấu hình file `.env`
1. Tạo file `.env` bằng cách sao chép từ `.env.example`:
   ```powershell
   Copy-Item .env.example .env
   ```
2. Mở file `.env` kiểm tra thông số kết nối cơ sở dữ liệu và bộ nhớ đệm:
   ```env
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=worldcup_2030
   DB_USERNAME=root
   DB_PASSWORD=

   CACHE_STORE=file
   CACHE_DRIVER=file
   SESSION_DRIVER=file
   ```
* **Kiểm tra:** `DB_PASSWORD=` để trống hoàn toàn (chuẩn mặc định của tài khoản `root` trên XAMPP MySQL).

---

#### Bước 6: Tạo Database trên MySQL
1. Mở trình duyệt truy cập: `http://localhost/phpmyadmin/`.
2. Tạo một Database mới có tên: `worldcup_2030` với bảng mã `utf8mb4_unicode_ci`.
* **Kiểm tra:** Database `worldcup_2030` hiển thị trên danh sách bên trái của phpMyAdmin.

---

#### Bước 7: Khởi tạo bảng, làm mới App Key và xóa cache
Chạy lần lượt các lệnh sau trong terminal:

1. Sinh mã khóa bảo mật ứng dụng:
   ```powershell
   php artisan key:generate
   ```

2. Chạy migration nạp dữ liệu và cấu trúc bảng:
   ```powershell
   php artisan migrate
   ```
   *(Migration đầu tiên sẽ tự động nạp cấu trúc từ `demo1.sql`, các migration tiếp theo tự động sinh 104 trận đấu và toàn bộ sơ đồ ghế)*.

3. Xóa các cache cấu hình cũ còn lưu tạm:
   ```powershell
   Remove-Item -Path "bootstrap\cache\config.php" -ErrorAction SilentlyContinue
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```
* **Kiểm tra:** Lệnh migrate báo `DONE` cho tất cả các bảng và các lệnh clear đều báo `successfully`.

---

#### Bước 8: Khởi động Server
Chạy lệnh khởi chạy máy chủ PHP nội bộ:
```powershell
php artisan serve
```
* **Kiểm tra:** Mở trình duyệt truy cập **http://127.0.0.1:8000/** (hoặc **http://localhost:8000**), giao diện trang chủ hiển thị mượt mà không phát sinh lỗi 500 hay lỗi kết nối database.

---

### PHƯƠNG PHÁP 2: Khởi chạy nhanh trên Linux / macOS / Terminal chuẩn

Dành cho môi trường phát triển đã có sẵn PHP 8.2+ và Composer:

```bash
# 1. Clone dự án và truy cập thư mục
git clone https://github.com/TuNgocCoder-zako/Nhom2_QuanLy-DatVeWorldCup.git
cd Nhom2_QuanLy-DatVeWorldCup

# 2. Cài đặt thư viện dependencies
composer install

# 3. Tạo file cấu hình và sinh khóa ứng dụng
cp .env.example .env
php artisan key:generate

# 4. Khởi tạo thư mục storage & phân quyền (nếu cần)
mkdir -p storage/framework/{sessions,views,cache/data} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 5. Tạo database và chạy migration
# (Tạo database worldcup_2030 trong MySQL trước)
php artisan migrate

# 6. Khởi chạy server
php artisan serve
```

---

## 📧 Cấu Hình Gửi Email Vé Điện Tử

Khi khách hàng thanh toán vé thành công, hệ thống tự động gửi email xác nhận vé (kèm mã QR soát vé) về địa chỉ email của người đặt.

Có 2 cách linh hoạt để cấu hình:

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
Nếu chỉ muốn kiểm tra mã nguồn gửi mail mà không cần cấu hình Gmail thật, chỉnh trong `.env`:
```env
MAIL_MAILER=log
```
Khi đặt vé thành công, toàn bộ nội dung thư HTML và chi tiết vé sẽ được tự động ghi vào tệp: `storage/logs/laravel.log`.
