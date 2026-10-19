# Hướng dẫn chạy và kiểm thử API Đặt Vé World Cup 2030

## 1. Khởi động môi trường

### Cách 1: Chạy qua Apache & MySQL trong XAMPP (Đang hoạt động)
1. Bật **Apache** và **MySQL** trong XAMPP Control Panel.
2. Đảm bảo database `worldcup_2030` đã được import từ `demo1.sql`.
3. URL gốc API:
   `http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/...`

### Cách 2: Chạy qua PHP Built-in Server (Tùy chọn)
Tại thư mục dự án:
```bash
php -S 127.0.0.1:8000 index.php
```
URL gốc API: `http://127.0.0.1:8000/api/...`

---

## 2. Các kịch bản kiểm thử API đặt vé (Test trực tiếp bằng cURL hoặc Postman)

### Bước 1: Xem danh sách & sơ đồ ghế của trận đấu
- **Endpoint**: `GET /api/matches/1/seats`
- **cURL**:
```bash
curl -i http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/matches/1/seats
```

---

### Bước 2: Kiểm tra các ghế muốn đặt có còn trống không
- **Endpoint**: `POST /api/matches/1/check-seats` *(hoặc `POST /api/check-seats`)*
- **Body JSON**:
```json
{
  "seat_ids": [1, 2]
}
```
- **cURL**:
```bash
curl -i -X POST \
  -H 'Content-Type: application/json' \
  -d '{"seat_ids": [1, 2]}' \
  http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/matches/1/check-seats
```
- **Kết quả mong đợi (200 OK)**:
```json
{
  "success": true,
  "message": "Các ghế đang còn trống.",
  "data": {
    "is_available": true,
    "locked_seats": [],
    "match_id": 1,
    "selected_count": 2,
    "total_price": 1000
  }
}
```

---

### Bước 3: Đặt vé trực tiếp (Tạo đơn hàng chờ thanh toán)
> **Ghi chú**: API hỗ trợ xác thực linh hoạt:
> - Có thể truyền `"user_id": 2` trực tiếp trong body (thuận tiện khi test bằng Postman/cURL mà không cần đăng nhập trước).
> - Hoặc đăng nhập qua `/api/auth/login` và dùng Session cookie.
> - Hỗ trợ cả 2 endpoint: `/api/matches/1/book-seats` và endpoint cũ `/api/matches/1/hold-seats`.

- **Endpoint**: `POST /api/matches/1/book-seats`
- **Body JSON**:
```json
{
  "user_id": 2,
  "seat_ids": [1, 2]
}
```
- **cURL**:
```bash
curl -i -X POST \
  -H 'Content-Type: application/json' \
  -d '{"user_id": 2, "seat_ids": [1, 2]}' \
  http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/matches/1/book-seats
```
- **Kết quả mong đợi (200 OK)**:
Trả về `order` kèm danh sách `tickets` gắn với các ghế đã chọn. Trạng thái ghế chuyển sang `BOOKED`.

---

### Bước 4: Kiểm tra lại ghế vừa đặt (Thử kiểm tra hoặc đặt lại ghế trùng)
- **cURL**:
```bash
curl -i -X POST \
  -H 'Content-Type: application/json' \
  -d '{"seat_ids": [1, 2]}' \
  http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/matches/1/check-seats
```
- **Kết quả mong đợi (409 Conflict)**:
Báo ghế không còn trống vì vừa được đặt:
```json
{
  "success": false,
  "is_available": false,
  "message": "Một hoặc nhiều ghế không còn trống.",
  "locked_seats": [
    {"id": 1, "seat_code": "VIP-BLK1-R1-01", "status": "BOOKED"},
    {"id": 2, "seat_code": "VIP-BLK2-R1-01", "status": "BOOKED"}
  ]
}
```

---

### Bước 5: Giả lập thanh toán cho đơn hàng (Simulate Payment)
Thay `{orderId}` bằng ID đơn hàng nhận được ở Bước 3 (ví dụ `orderId = 1`):
- **Endpoint**: `POST /api/orders/{orderId}/payment/simulate`
- **Body JSON**:
```json
{
  "user_id": 2
}
```
- **cURL**:
```bash
curl -i -X POST \
  -H 'Content-Type: application/json' \
  -d '{"user_id": 2}' \
  http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/orders/1/payment/simulate
```
- **Kết quả mong đợi (200 OK)**:
Trạng thái đơn hàng chuyển sang `PAID`, các vé trong đơn hàng được kích hoạt mã vé điện tử chính thức (`VALID`).

---

### Bước 6: Xem danh sách vé đã mua của người dùng
- **Endpoint**: `GET /api/users/2/tickets`
- **cURL**:
```bash
curl -i -H 'Content-Type: application/json' \
  http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/users/2/tickets?user_id=2
```

---

### Bước 7: Hủy đơn hàng (Nếu chưa thanh toán)
- **Endpoint**: `DELETE /api/orders/{orderId}`
- **cURL**:
```bash
curl -i -X DELETE \
  -H 'Content-Type: application/json' \
  -d '{"user_id": 2}' \
  http://localhost/PROJECT/Nhom2_QuanLy-DatVeWorldCup/api/orders/1
```
*(Ghế sẽ tự động được giải phóng về trạng thái `AVAILABLE`).*

---

## 3. Tài khoản có sẵn trong cơ sở dữ liệu (`demo1.sql`)
- Khách hàng: `user@gmail.com` / Mật khẩu: `123456` (ID = 2)
- Quản trị viên: `admin@worldcup2030.com` / Mật khẩu: `123456` (ID = 1)
