-- ============================================================================
-- ĐỒ ÁN MÔN PHÁT TRIỂN MÃ NGUỒN MỞ (PHP & MYSQL)
-- HỆ THỐNG ĐẶT VÉ FIFA WORLD CUP 2030 (QUY MÔ 100,000 CHỖ NGOÌ/SÂN)
-- Cơ cấu: 10,000 Ghế VIP | 90,000 Ghế Phổ Thông
-- ============================================================================

DROP DATABASE IF EXISTS worldcup_2030;
CREATE DATABASE worldcup_2030 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE worldcup_2030;

SET FOREIGN_KEY_CHECKS = 0;

-- ==========================================
-- 1. BẢNG NGƯỜI DÙNG & PHÂN QUYỀN
-- ==========================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('USER', 'ADMIN') DEFAULT 'USER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- 2. BẢNG QUỐC GIA ĐĂNG CAI (3 QUỐC GIA)
-- ==========================================
CREATE TABLE host_countries (
    country_id INT AUTO_INCREMENT PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL UNIQUE,
    country_code CHAR(3) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ==========================================
-- 3. BẢNG THÀNH PHỐ TỔ CHỨC (16 THÀNH PHỐ)
-- ==========================================
CREATE TABLE cities (
    city_id INT AUTO_INCREMENT PRIMARY KEY,
    country_id INT NOT NULL,
    city_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (country_id) REFERENCES host_countries(country_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 4. BẢNG SÂN VẬN ĐỘNG (SỨC CHỨA 100,000 CHỖ)
-- ==========================================
CREATE TABLE stadiums (
    stadium_id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NOT NULL,
    stadium_name VARCHAR(150) NOT NULL,
    capacity INT NOT NULL DEFAULT 100000,
    FOREIGN KEY (city_id) REFERENCES cities(city_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 5. BẢNG ĐỘI TUYỂN (48 ĐỘI, 12 BẢNG A-L)
-- ==========================================
CREATE TABLE teams (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100) NOT NULL UNIQUE,
    code CHAR(3) NOT NULL UNIQUE,
    flag_url VARCHAR(255),
    group_name ENUM('A','B','C','D','E','F','G','H','I','J','K','L') NULL
) ENGINE=InnoDB;

-- ==========================================
-- 6. BẢNG TRẬN ĐẤU (104 TRẬN)
-- ==========================================
CREATE TABLE matches (
    match_id INT AUTO_INCREMENT PRIMARY KEY,
    match_number INT NOT NULL UNIQUE,
    stage ENUM(
        'GROUP',         -- 72 trận
        'ROUND_32',      -- 16 trận
        'ROUND_16',      -- 8 trận
        'QUARTER_FINAL', -- 4 trận
        'SEMI_FINAL',    -- 2 trận
        'THIRD_PLACE',   -- 1 trận
        'FINAL'          -- 1 trận
    ) NOT NULL,
    stadium_id INT NOT NULL,
    home_team_id INT NULL,
    away_team_id INT NULL,
    match_time DATETIME NOT NULL,
    home_score INT DEFAULT 0,
    away_score INT DEFAULT 0,
    match_status ENUM('UPCOMING', 'LIVE', 'FINISHED', 'CANCELLED') DEFAULT 'UPCOMING',
    FOREIGN KEY (stadium_id) REFERENCES stadiums(stadium_id),
    FOREIGN KEY (home_team_id) REFERENCES teams(team_id),
    FOREIGN KEY (away_team_id) REFERENCES teams(team_id)
) ENGINE=InnoDB;

-- ==========================================
-- 7. BẢNG LOẠI VÉ (VIP & PHỔ THÔNG)
-- ==========================================
CREATE TABLE ticket_types (
    ticket_type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_name ENUM('VIP', 'STANDARD') NOT NULL,
    description VARCHAR(255)
) ENGINE=InnoDB;

-- ==========================================
-- 8. BẢNG GIÁ & KHO VÉ THEO TRẬN
-- ==========================================
CREATE TABLE match_tickets (
    match_ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    ticket_type_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total_quantity INT NOT NULL,
    available_quantity INT NOT NULL,
    FOREIGN KEY (match_id) REFERENCES matches(match_id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_type_id) REFERENCES ticket_types(ticket_type_id),
    UNIQUE KEY idx_match_type (match_id, ticket_type_id)
) ENGINE=InnoDB;

-- ==========================================
-- 9. BẢNG GHẾ NGOÌ CHI TIẾT
-- ==========================================
CREATE TABLE seats (
    seat_id INT AUTO_INCREMENT PRIMARY KEY,
    stadium_id INT NOT NULL,
    seat_code VARCHAR(30) NOT NULL,
    ticket_type_id INT NOT NULL,
    FOREIGN KEY (stadium_id) REFERENCES stadiums(stadium_id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_type_id) REFERENCES ticket_types(ticket_type_id)
) ENGINE=InnoDB;

-- Chỉ mục tối ưu tốc độ tìm kiếm ghế trên CSDL lớn (Index cho 100,000+ dòng)
CREATE INDEX idx_stadium_type ON seats(stadium_id, ticket_type_id);
CREATE INDEX idx_seat_stadium_code ON seats(stadium_id, seat_code);

-- ==========================================
-- 10. BẢNG ĐƠN HÀNG
-- ==========================================
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    payment_status ENUM('PENDING', 'PAID', 'CANCELLED') DEFAULT 'PENDING',
    payment_method VARCHAR(50) DEFAULT 'SIMULATION',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 11. BẢNG VÉ ĐÃ ĐẶT (CHỐNG BÁN TRÙNG GHẾ)
-- ==========================================
CREATE TABLE tickets (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    match_id INT NOT NULL,
    seat_id INT NOT NULL,
    ticket_code VARCHAR(64) NOT NULL UNIQUE,
    status ENUM('BOOKED', 'CANCELLED') DEFAULT 'BOOKED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matches(match_id),
    FOREIGN KEY (seat_id) REFERENCES seats(seat_id),
    UNIQUE KEY idx_match_seat_unique (match_id, seat_id)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- NẠP DỮ LIỆU MẪU (SEED DATA)
-- ============================================================================

-- 1. LOẠI VÉ
INSERT INTO ticket_types (type_name, description) VALUES
('VIP', 'Vé khán đài VIP, lối đi ưu tiên, kèm đồ ăn nhẹ & đồ uống cao cấp'),
('STANDARD', 'Vé phổ thông khán đài A/B/C/D');

-- 2. TÀI KHOẢN MẪU (Mật khẩu mặc định: 123456)
INSERT INTO users (full_name, email, password_hash, phone, role) VALUES
('System Admin', 'admin@worldcup2030.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe118a./2i9F14G/M7z26p20R/fP.Wd42', '0901234567', 'ADMIN'),
('Nguyễn Văn A', 'user@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe118a./2i9F14G/M7z26p20R/fP.Wd42', '0987654321', 'USER'),
('Trần Thị B', 'user_b@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe118a./2i9F14G/M7z26p20R/fP.Wd42', '0912345678', 'USER');

-- 3. 3 QUỐC GIA ĐĂNG CAI
INSERT INTO host_countries (country_id, country_name, country_code) VALUES
(1, 'Tây Ban Nha', 'ESP'),
(2, 'Bồ Đào Nha', 'POR'),
(3, 'Ma-rốc', 'MAR');

-- 4. 16 THÀNH PHỐ TỔ CHỨC THỰC TẾ
INSERT INTO cities (city_id, country_id, city_name) VALUES
(1, 1, 'Madrid'), (2, 1, 'Barcelona'), (3, 1, 'Seville'), (4, 1, 'Bilbao'),
(5, 1, 'San Sebastian'), (6, 1, 'A Coruna'), (7, 1, 'Zaragoza'), (8, 1, 'Malaga'), (9, 1, 'Las Palmas'),
(10, 2, 'Lisbon'), (11, 2, 'Porto'), (12, 2, 'Faro'),
(13, 3, 'Casablanca'), (14, 3, 'Rabat'), (15, 3, 'Tangier'), (16, 3, 'Marrakech');

-- 5. 16 SÂN VẬN ĐỘNG QUY MÔ 100,000 CHỖ NGOÌ
INSERT INTO stadiums (stadium_id, city_id, stadium_name, capacity) VALUES
(1, 1, 'Santiago Bernabeu', 100000),
(2, 2, 'Spotify Camp Nou', 100000),
(3, 3, 'Estadio La Cartuja', 100000),
(4, 4, 'San Mames', 100000),
(5, 5, 'Reale Arena', 100000),
(6, 6, 'Estadio Abanca-Riazor', 100000),
(7, 7, 'Estadio La Romareda', 100000),
(8, 8, 'Estadio La Rosaleda', 100000),
(9, 9, 'Estadio Gran Canaria', 100000),
(10, 10, 'Estadio da Luz', 100000),
(11, 11, 'Estadio do Dragao', 100000),
(12, 12, 'Estadio Algarve', 100000),
(13, 13, 'Grand Stade de Casablanca', 100000),
(14, 14, 'Stade Prince Moulay Abdellah', 100000),
(15, 15, 'Ibn Batouta Stadium', 100000),
(16, 16, 'Grand Stade de Marrakech', 100000);

-- 6. DỮ LIỆU MẪU 48 ĐỘI TUYỂN CHIA ĐỀU VÀO 12 BẢNG (A - L)
INSERT INTO teams (team_id, team_name, code, group_name) VALUES
-- Bảng A
(1, 'Tây Ban Nha', 'ESP', 'A'), (2, 'Bồ Đào Nha', 'POR', 'A'), (3, 'Ma-rốc', 'MAR', 'A'), (4, 'Việt Nam', 'VIE', 'A'),
-- Bảng B
(5, 'Nhật Bản', 'JPN', 'B'), (6, 'Hàn Quốc', 'KOR', 'B'), (7, 'Úc', 'AUS', 'B'), (8, 'Ả Rập Xê Út', 'KSA', 'B'),
-- Bảng C
(9, 'Pháp', 'FRA', 'C'), (10, 'Đức', 'GER', 'C'), (11, 'Ý', 'ITA', 'C'), (12, 'Hà Lan', 'NED', 'C'),
-- Bảng D
(13, 'Anh', 'ENG', 'D'), (14, 'Bỉ', 'BEL', 'D'), (15, 'Kroatia', 'CRO', 'D'), (16, 'Thụy Sĩ', 'SUI', 'D'),
-- Bảng E
(17, 'Brasil', 'BRA', 'E'), (18, 'Argentina', 'ARG', 'E'), (19, 'Uruguay', 'URU', 'E'), (20, 'Colombia', 'COL', 'E'),
-- Bảng F
(21, 'Mỹ', 'USA', 'F'), (22, 'Mexico', 'MEX', 'F'), (23, 'Canada', 'CAN', 'F'), (24, 'Costa Rica', 'CRC', 'F'),
-- Bảng G
(25, 'Senegal', 'SEN', 'G'), (26, 'Nigeria', 'NGA', 'G'), (27, 'Ai Cập', 'EGY', 'G'), (28, 'Ghana', 'GHA', 'G'),
-- Bảng H
(29, 'Đan Mạch', 'DEN', 'H'), (30, 'Áo', 'AUT', 'H'), (31, 'Ba Lan', 'POL', 'H'), (32, 'Thụy Điển', 'SWE', 'H'),
-- Bảng I
(33, 'Ecuador', 'ECU', 'I'), (34, 'Chile', 'CHI', 'I'), (35, 'Peru', 'PER', 'I'), (36, 'Paraguay', 'PAR', 'I'),
-- Bảng J
(37, 'Iran', 'IRN', 'J'), (38, 'I-rắc', 'IRQ', 'J'), (39, 'Uzbekistan', 'UZB', 'J'), (40, 'Qatar', 'QAT', 'J'),
-- Bảng K
(41, 'Cameroon', 'CMR', 'K'), (42, 'Mali', 'MLI', 'K'), (43, 'Bờ Biển Ngà', 'CIV', 'K'), (44, 'Tunisia', 'TUN', 'K'),
-- Bảng L
(45, 'Algeria', 'ALG', 'L'), (46, 'Thổ Nhĩ Kỳ', 'TUR', 'L'), (47, 'Ukraina', 'UKR', 'L'), (48, 'Serbia', 'SRB', 'L');

-- 7. CÁC TRẬN ĐẤU ĐẠI DIỆN CHO TẤT CẢ CÁC BẢNG ĐẤU VÀ VÒNG KNOCKOUT
INSERT INTO matches (match_id, match_number, stage, stadium_id, home_team_id, away_team_id, match_time) VALUES
-- Vòng bảng A - L (17 Trận đại diện)
(1, 1, 'GROUP', 1, 1, 4, '2030-06-13 20:00:00'),   -- Khai mạc Bảng A: Tây Ban Nha vs Việt Nam
(2, 2, 'GROUP', 10, 2, 3, '2030-06-14 18:00:00'),  -- Bảng A: Bồ Đào Nha vs Ma-rốc
(3, 3, 'GROUP', 5, 5, 6, '2030-06-14 21:00:00'),   -- Bảng B: Nhật Bản vs Hàn Quốc
(4, 4, 'GROUP', 12, 7, 8, '2030-06-15 15:00:00'),  -- Bảng B: Úc vs Ả Rập Xê Út
(5, 5, 'GROUP', 13, 9, 10, '2030-06-15 18:00:00'), -- Bảng C: Pháp vs Đức
(6, 6, 'GROUP', 2, 11, 12, '2030-06-15 21:00:00'), -- Bảng C: Ý vs Hà Lan
(7, 7, 'GROUP', 4, 13, 14, '2030-06-16 18:00:00'), -- Bảng D: Anh vs Bỉ
(8, 8, 'GROUP', 3, 15, 16, '2030-06-16 21:00:00'), -- Bảng D: Kroatia vs Thụy Sĩ
(9, 9, 'GROUP', 1, 17, 18, '2030-06-17 18:00:00'), -- Bảng E: Brasil vs Argentina
(10, 10, 'GROUP', 11, 19, 20, '2030-06-17 21:00:00'),-- Bảng E: Uruguay vs Colombia
(11, 11, 'GROUP', 15, 21, 22, '2030-06-18 18:00:00'),-- Bảng F: Mỹ vs Mexico
(12, 12, 'GROUP', 14, 25, 26, '2030-06-18 21:00:00'),-- Bảng G: Senegal vs Nigeria
(13, 13, 'GROUP', 8, 29, 30, '2030-06-19 18:00:00'), -- Bảng H: Đan Mạch vs Áo
(14, 14, 'GROUP', 6, 33, 34, '2030-06-19 21:00:00'), -- Bảng I: Ecuador vs Chile
(15, 15, 'GROUP', 16, 37, 38, '2030-06-20 18:00:00'),-- Bảng J: Iran vs I-rắc
(16, 16, 'GROUP', 7, 41, 42, '2030-06-20 21:00:00'), -- Bảng K: Cameroon vs Mali
(17, 17, 'GROUP', 9, 45, 46, '2030-06-21 20:00:00'), -- Bảng L: Algeria vs Thổ Nhĩ Kỳ
-- Vòng Knockout
(18, 73, 'ROUND_32', 2, NULL, NULL, '2030-07-01 18:00:00'),
(19, 89, 'ROUND_16', 1, NULL, NULL, '2030-07-06 20:00:00'),
(20, 97, 'QUARTER_FINAL', 13, NULL, NULL, '2030-07-10 21:00:00'),
(21, 101, 'SEMI_FINAL', 2, NULL, NULL, '2030-07-14 21:00:00'),
(22, 103, 'THIRD_PLACE', 10, NULL, NULL, '2030-07-18 18:00:00'),
(23, 104, 'FINAL', 13, NULL, NULL, '2030-07-21 20:00:00');

-- 8. CẤU HÌNH KHO VÉ 100,000 CHỖ THEO TRẬN
INSERT INTO match_tickets (match_id, ticket_type_id, price, total_quantity, available_quantity) VALUES
-- Match 1 (Tây Ban Nha vs Việt Nam): 10,000 vé VIP & 90,000 vé Phổ Thông
(1, 1, 500.00, 10000, 10000), (1, 2, 150.00, 90000, 90000),
-- Match 2: 10,000 vé VIP & 90,000 vé Phổ Thông
(2, 1, 450.00, 10000, 10000), (2, 2, 130.00, 90000, 90000),
-- Match 23 (Trận Chung kết): 10,000 vé VIP & 90,000 vé Phổ Thông
(23, 1, 2500.00, 10000, 10000), (23, 2, 800.00, 90000, 90000);

-- ============================================================================
-- 9. STORED PROCEDURE SINH ĐỦ 100,000 GHẾ (10,000 VIP + 90,000 PHỔ THÔNG)
-- ============================================================================

DELIMITER //

DROP PROCEDURE IF EXISTS generate_100k_stadium_seats //

CREATE PROCEDURE generate_100k_stadium_seats(IN target_stadium_id INT)
BEGIN
    -- Tạo bảng phụ dãy số 1-100 để CROSS JOIN cực nhanh
    CREATE TEMPORARY TABLE IF NOT EXISTS seq_100 (n INT PRIMARY KEY);
    
    IF (SELECT COUNT(*) FROM seq_100) = 0 THEN
        INSERT INTO seq_100 (n) VALUES
        (1),(2),(3),(4),(5),(6),(7),(8),(9),(10),
        (11),(12),(13),(14),(15),(16),(17),(18),(19),(20),
        (21),(22),(23),(24),(25),(26),(27),(28),(29),(30),
        (31),(32),(33),(34),(35),(36),(37),(38),(39),(40),
        (41),(42),(43),(44),(45),(46),(47),(48),(49),(50);
    END IF;

    -- A. Sinh đúng 10,000 Ghế VIP (10 Block x 20 Hàng x 50 Ghế)
    INSERT INTO seats (stadium_id, seat_code, ticket_type_id)
    SELECT target_stadium_id, CONCAT('VIP-BLK', b.n, '-R', r.n, '-', LPAD(s.seat_num, 2, '0')), 1
    FROM seq_100 b
    JOIN seq_100 r ON r.n <= 20
    JOIN (
        SELECT 1 AS seat_num UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
        UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20
        UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL SELECT 30
        UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL SELECT 40
        UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL SELECT 45 UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL SELECT 50
    ) s
    WHERE b.n <= 10;

    -- B. Sinh đúng 90,000 Ghế Phổ thông (4 Khán đài A,B,C,D x 15 Block x 30 Hàng x 50 Ghế)
    INSERT INTO seats (stadium_id, seat_code, ticket_type_id)
    SELECT target_stadium_id, CONCAT('STD-', sec.sec_code, '-BLK', (100 + b.n), '-R', r.n, '-', LPAD(s.seat_num, 2, '0')), 2
    FROM (SELECT 'A' AS sec_code UNION ALL SELECT 'B' UNION ALL SELECT 'C' UNION ALL SELECT 'D') sec
    JOIN seq_100 b ON b.n <= 15
    JOIN seq_100 r ON r.n <= 30
    JOIN (
        SELECT 1 AS seat_num UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
        UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20
        UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL SELECT 30
        UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL SELECT 40
        UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL SELECT 45 UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL SELECT 50
    ) s;

    DROP TEMPORARY TABLE IF EXISTS seq_100;
END //

DELIMITER ;

-- Khởi tạo đủ 100,000 ghế cho Sân Santiago Bernabeu (Sân_id = 1)
CALL generate_100k_stadium_seats(1);

-- Khởi tạo đủ 100,000 ghế cho Sân Spotify Camp Nou (Sân_id = 2)
CALL generate_100k_stadium_seats(2);

-- Dọn dẹp Stored Procedure
DROP PROCEDURE IF EXISTS generate_100k_stadium_seats;