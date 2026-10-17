# Hướng dẫn chạy API và Backend

## Cách 1: Chạy nhanh bằng PHP Built-in Server (Khuyên dùng khi dev/test)

1. Mở MySQL trong XAMPP (hoặc chạy mysqld).
2. Tại thư mục dự án, chạy lệnh:
   ```bash
   php -S 127.0.0.1:8000 index.php
   ```
3. Truy cập API qua: `http://127.0.0.1:8000/api/...`

Ví dụ kiểm tra ghế:
```bash
curl -i http://127.0.0.1:8000/api/matches/1/seats
```

## Cách 2: Chạy qua Apache trong XAMPP

1. Khởi động Apache và MySQL trong XAMPP.
2. Import `demo1.sql` vào database `worldcup_2030`.
3. Kiểm tra `.env`. XAMPP mặc định thường dùng `DB_USERNAME=root` và `DB_PASSWORD=` (rỗng).
4. Truy cập API qua:
   `http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/...`

Apache cần bật `mod_rewrite` để `.htaccess` chuyển các URL API về `index.php`.

## Đăng nhập để kiểm tra endpoint cần auth

```bash
curl -i -c cookies.txt -X POST \
  -H 'Content-Type: application/json' \
  -d '{"email":"user@gmail.com","password":"123456"}' \
  http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/auth/login
```

Sau đó dùng cookie đã lưu để gọi API, ví dụ:

```bash
curl -i -b cookies.txt -X POST \
  -H 'Content-Type: application/json' \
  -d '{"user_id":2,"seat_ids":[1,2],"hold_duration_minutes":10}' \
  http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/matches/1/hold-seats
```

Tài khoản seed trong `demo1.sql`: `user@gmail.com` / `123456` và `admin@worldcup2030.com` / `123456`.
