-- ============================================================================
-- HỆ THỐNG ĐẶT VÉ FIFA WORLD CUP 2030 (QUY MÔ 100,000 CHỖ NGOÌ/SÂN)
-- Cơ sở dữ liệu đồng bộ 100% với Codebase Laravel (Nhom2_QuanLy-DatVeWorldCup)
-- ============================================================================

DROP DATABASE IF EXISTS worldcup_2030;
CREATE DATABASE worldcup_2030 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE worldcup_2030;

SET FOREIGN_KEY_CHECKS = 0;

-- ==========================================
-- 1. BẢNG NGƯỜI DÙNG (Tương thích User.php)
-- ==========================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    role ENUM('USER', 'ADMIN') DEFAULT 'USER',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- 2. BẢNG QUỐC GIA ĐĂNG CAI (3 QUỐC GIA)
-- ==========================================
CREATE TABLE host_countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL UNIQUE,
    country_code CHAR(3) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- 3. BẢNG THÀNH PHỐ TỔ CHỨC (16 THÀNH PHỐ)
-- ==========================================
CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_id INT NOT NULL,
    city_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES host_countries(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 4. BẢNG SÂN VẬN ĐỘNG (Tương thích Stadium.php)
-- ==========================================
CREATE TABLE stadiums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NULL,
    name VARCHAR(150) NOT NULL,
    stadium_name VARCHAR(150) NULL,
    location VARCHAR(255) NULL,
    capacity INT NOT NULL DEFAULT 100000,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 5. BẢNG LOẠI VÉ (TICKET_TYPES)
-- ==========================================
CREATE TABLE ticket_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- 6. BẢNG ĐỘI TUYỂN (Tương thích Team.php)
-- ==========================================
CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    team_name VARCHAR(100) NULL,
    code CHAR(3) NOT NULL UNIQUE,
    flag_url VARCHAR(255) NULL,
    group_name ENUM('A','B','C','D','E','F','G','H','I','J','K','L') NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- 7. BẢNG KHU VỰC GHẾ (Tương thích SeatZone.php)
-- ==========================================
CREATE TABLE seat_zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stadium_id INT NOT NULL,
    ticket_type_id INT NULL,
    zone_name VARCHAR(50) NOT NULL,
    name VARCHAR(50) NULL,
    type ENUM('VIP', 'STANDARD') NOT NULL DEFAULT 'STANDARD',
    total_seats INT NOT NULL DEFAULT 0,
    base_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (stadium_id) REFERENCES stadiums(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_type_id) REFERENCES ticket_types(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 8. BẢNG TRẬN ĐẤU (Tương thích MatchGame.php)
-- ==========================================
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_number INT NOT NULL UNIQUE,
    stage ENUM('GROUP', 'ROUND_32', 'ROUND_16', 'QUARTER_FINAL', 'SEMI_FINAL', 'THIRD_PLACE', 'FINAL') NOT NULL,
    stadium_id INT NOT NULL,
    home_team_id INT NULL,
    away_team_id INT NULL,
    match_time DATETIME NOT NULL,
    match_date DATETIME NULL,
    home_score INT DEFAULT 0,
    away_score INT DEFAULT 0,
    status ENUM('UPCOMING', 'LIVE', 'FINISHED', 'CANCELLED') DEFAULT 'UPCOMING',
    match_status ENUM('UPCOMING', 'LIVE', 'FINISHED', 'CANCELLED') DEFAULT 'UPCOMING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (stadium_id) REFERENCES stadiums(id) ON DELETE CASCADE,
    FOREIGN KEY (home_team_id) REFERENCES teams(id) ON DELETE SET NULL,
    FOREIGN KEY (away_team_id) REFERENCES teams(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 9. BẢNG GHẾ THEO TRẬN ĐẤU (Tương thích MatchSeat.php)
-- ==========================================
CREATE TABLE match_seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    seat_zone_id INT NOT NULL,
    seat_code VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('AVAILABLE', 'BOOKED', 'RESERVED', 'HELD') DEFAULT 'AVAILABLE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_zone_id) REFERENCES seat_zones(id) ON DELETE CASCADE,
    UNIQUE KEY idx_match_seat_code (match_id, seat_code)
) ENGINE=InnoDB;

CREATE INDEX idx_match_seat_status ON match_seats(match_id, status);

-- ==========================================
-- 10. BẢNG ĐƠN HÀNG (Tương thích Order.php)
-- ==========================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_code VARCHAR(64) NULL UNIQUE,
    total_price DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NULL,
    status ENUM('PENDING', 'PAID', 'CANCELLED') DEFAULT 'PENDING',
    payment_status ENUM('PENDING', 'PAID', 'CANCELLED') DEFAULT 'PENDING',
    payment_method VARCHAR(50) DEFAULT 'SIMULATION',
    hold_expires_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_hold_expiry (status, hold_expires_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 11. BẢNG VÉ ĐÃ ĐẶT (Tương thích Ticket.php)
-- ==========================================
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    match_id INT NOT NULL,
    match_seat_id INT NOT NULL,
    ticket_code VARCHAR(64) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('BOOKED', 'CANCELLED', 'VALID') DEFAULT 'BOOKED',
    check_in_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (match_seat_id) REFERENCES match_seats(id) ON DELETE CASCADE,
    UNIQUE KEY idx_match_seat_ticket (match_id, match_seat_id),
    INDEX idx_ticket_check_in (ticket_code, status, check_in_at)
) ENGINE=InnoDB;

-- ==========================================
-- 12. BẢNG THANH TOÁN (Tương thích Payment.php & PaymentService)
-- ==========================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'SIMULATION',
    transaction_id VARCHAR(100) NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('PENDING', 'SUCCESS', 'FAILED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- NẠP DỮ LIỆU MẪU (SEED DATA WORLD CUP 2030)
-- ============================================================================

-- 1. LOẠI VÉ
INSERT INTO ticket_types (id, type_name, description) VALUES
(1, 'VIP', 'Vé khán đài VIP, lối đi ưu tiên, kèm đồ ăn nhẹ & đồ uống cao cấp'),
(2, 'STANDARD', 'Vé phổ thông khán đài A/B/C/D');

-- 2. NGƯỜI DÙNG MẪU (Mật khẩu: 123456)
INSERT INTO users (id, name, full_name, email, password, phone, role) VALUES
(1, 'System Admin', 'System Admin', 'admin@worldcup2030.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe118a./2i9F14G/M7z26p20R/fP.Wd42', '0901234567', 'ADMIN'),
(2, 'Nguyễn Văn A', 'Nguyễn Văn A', 'user@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe118a./2i9F14G/M7z26p20R/fP.Wd42', '0987654321', 'USER'),
(3, 'Trần Thị B', 'Trần Thị B', 'user_b@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe118a./2i9F14G/M7z26p20R/fP.Wd42', '0912345678', 'USER');

-- 3. QUỐC GIA ĐĂNG CAI
INSERT INTO host_countries (id, country_name, country_code) VALUES
(1, 'Tây Ban Nha', 'ESP'),
(2, 'Bồ Đào Nha', 'POR'),
(3, 'Ma-rốc', 'MAR');

-- 4. THÀNH PHỐ TỔ CHỨC
INSERT INTO cities (id, country_id, city_name) VALUES
(1, 1, 'Madrid'), (2, 1, 'Barcelona'), (3, 1, 'Seville'), (4, 1, 'Bilbao'),
(5, 1, 'San Sebastian'), (6, 1, 'A Coruna'), (7, 1, 'Zaragoza'), (8, 1, 'Malaga'), (9, 1, 'Las Palmas'),
(10, 2, 'Lisbon'), (11, 2, 'Porto'), (12, 2, 'Faro'),
(13, 3, 'Casablanca'), (14, 3, 'Rabat'), (15, 3, 'Tangier'), (16, 3, 'Marrakech');

-- 5. SÂN VẬN ĐỘNG QUY MÔ 100,000 CHỖ
INSERT INTO stadiums (id, city_id, name, stadium_name, location, capacity) VALUES
(1, 1, 'Santiago Bernabeu', 'Santiago Bernabeu', 'Madrid, Spain', 100000),
(2, 2, 'Spotify Camp Nou', 'Spotify Camp Nou', 'Barcelona, Spain', 100000),
(3, 3, 'Estadio La Cartuja', 'Estadio La Cartuja', 'Seville, Spain', 100000),
(4, 4, 'San Mames', 'San Mames', 'Bilbao, Spain', 100000),
(5, 5, 'Reale Arena', 'Reale Arena', 'San Sebastian, Spain', 100000),
(6, 6, 'Estadio Abanca-Riazor', 'Estadio Abanca-Riazor', 'A Coruna, Spain', 100000),
(7, 7, 'Estadio La Romareda', 'Estadio La Romareda', 'Zaragoza, Spain', 100000),
(8, 8, 'Estadio La Rosaleda', 'Estadio La Rosaleda', 'Malaga, Spain', 100000),
(9, 9, 'Estadio Gran Canaria', 'Estadio Gran Canaria', 'Las Palmas, Spain', 100000),
(10, 10, 'Estadio da Luz', 'Estadio da Luz', 'Lisbon, Portugal', 100000),
(11, 11, 'Estadio do Dragao', 'Estadio do Dragao', 'Porto, Portugal', 100000),
(12, 12, 'Estadio Algarve', 'Estadio Algarve', 'Faro, Portugal', 100000),
(13, 13, 'Grand Stade de Casablanca', 'Grand Stade de Casablanca', 'Casablanca, Morocco', 100000),
(14, 14, 'Stade Prince Moulay Abdellah', 'Stade Prince Moulay Abdellah', 'Rabat, Morocco', 100000),
(15, 15, 'Ibn Batouta Stadium', 'Ibn Batouta Stadium', 'Tangier, Morocco', 100000),
(16, 16, 'Grand Stade de Marrakech', 'Grand Stade de Marrakech', 'Marrakech, Morocco', 100000);

-- 6. KHU VỰC GHẾ (LIÊN KẾT TRỰC TIẾP TICKET_TYPE_ID)
INSERT INTO seat_zones (stadium_id, ticket_type_id, zone_name, name, type, total_seats, base_price)
SELECT id, 1, 'Khán đài VIP', 'Khán đài VIP', 'VIP', 10000, 500.00 FROM stadiums;

INSERT INTO seat_zones (stadium_id, ticket_type_id, zone_name, name, type, total_seats, base_price)
SELECT id, 2, 'Khán đài Phổ Thông', 'Khán đài Phổ Thông', 'STANDARD', 90000, 150.00 FROM stadiums;

-- 7. 48 ĐỘI TUYỂN 12 BẢNG (A - L)
INSERT INTO teams (id, name, team_name, code, group_name) VALUES
(1, 'Tây Ban Nha', 'Tây Ban Nha', 'ESP', 'A'), (2, 'Bồ Đào Nha', 'Bồ Đào Nha', 'POR', 'A'), (3, 'Ma-rốc', 'Ma-rốc', 'MAR', 'A'), (4, 'Việt Nam', 'Việt Nam', 'VIE', 'A'),
(5, 'Nhật Bản', 'Nhật Bản', 'JPN', 'B'), (6, 'Hàn Quốc', 'Hàn Quốc', 'KOR', 'B'), (7, 'Úc', 'Úc', 'AUS', 'B'), (8, 'Ả Rập Xê Út', 'Ả Rập Xê Út', 'KSA', 'B'),
(9, 'Pháp', 'Pháp', 'FRA', 'C'), (10, 'Đức', 'Đức', 'GER', 'C'), (11, 'Ý', 'Ý', 'ITA', 'C'), (12, 'Hà Lan', 'Hà Lan', 'NED', 'C'),
(13, 'Anh', 'Anh', 'ENG', 'D'), (14, 'Bỉ', 'Bỉ', 'BEL', 'D'), (15, 'Kroatia', 'Kroatia', 'CRO', 'D'), (16, 'Thụy Sĩ', 'Thụy Sĩ', 'SUI', 'D'),
(17, 'Brasil', 'Brasil', 'BRA', 'E'), (18, 'Argentina', 'Argentina', 'ARG', 'E'), (19, 'Uruguay', 'Uruguay', 'URU', 'E'), (20, 'Colombia', 'Colombia', 'COL', 'E'),
(21, 'Mỹ', 'Mỹ', 'USA', 'F'), (22, 'Mexico', 'Mexico', 'MEX', 'F'), (23, 'Canada', 'Canada', 'CAN', 'F'), (24, 'Costa Rica', 'Costa Rica', 'CRC', 'F'),
(25, 'Senegal', 'Senegal', 'SEN', 'G'), (26, 'Nigeria', 'Nigeria', 'NGA', 'G'), (27, 'Ai Cập', 'Ai Cập', 'EGY', 'G'), (28, 'Ghana', 'Ghana', 'GHA', 'G'),
(29, 'Đan Mạch', 'Đan Mạch', 'DEN', 'H'), (30, 'Áo', 'Áo', 'AUT', 'H'), (31, 'Ba Lan', 'Ba Lan', 'POL', 'H'), (32, 'Thụy Điển', 'Thụy Điển', 'SWE', 'H'),
(33, 'Ecuador', 'Ecuador', 'ECU', 'I'), (34, 'Chile', 'Chile', 'CHI', 'I'), (35, 'Peru', 'Peru', 'PER', 'I'), (36, 'Paraguay', 'Paraguay', 'PAR', 'I'),
(37, 'Iran', 'Iran', 'IRN', 'J'), (38, 'I-rắc', 'I-rắc', 'IRQ', 'J'), (39, 'Uzbekistan', 'Uzbekistan', 'UZB', 'J'), (40, 'Qatar', 'Qatar', 'QAT', 'J'),
(41, 'Cameroon', 'Cameroon', 'CMR', 'K'), (42, 'Mali', 'Mali', 'MLI', 'K'), (43, 'Bờ Biển Ngà', 'Bờ Biển Ngà', 'CIV', 'K'), (44, 'Tunisia', 'Tunisia', 'TUN', 'K'),
(45, 'Algeria', 'Algeria', 'ALG', 'L'), (46, 'Thổ Nhĩ Kỳ', 'Thổ Nhĩ Kỳ', 'TUR', 'L'), (47, 'Ukraina', 'Ukraina', 'UKR', 'L'), (48, 'Serbia', 'Serbia', 'SRB', 'L');

-- 8. TRẬN ĐẤU MẪU
INSERT INTO matches (id, match_number, stage, stadium_id, home_team_id, away_team_id, match_time, match_date, status, match_status) VALUES
(1, 1, 'GROUP', 1, 1, 4, '2030-06-13 20:00:00', '2030-06-13 20:00:00', 'UPCOMING', 'UPCOMING'),
(2, 2, 'GROUP', 10, 2, 3, '2030-06-14 18:00:00', '2030-06-14 18:00:00', 'UPCOMING', 'UPCOMING'),
(3, 3, 'GROUP', 5, 5, 6, '2030-06-14 21:00:00', '2030-06-14 21:00:00', 'UPCOMING', 'UPCOMING'),
(4, 4, 'GROUP', 12, 7, 8, '2030-06-15 15:00:00', '2030-06-15 15:00:00', 'UPCOMING', 'UPCOMING'),
(5, 5, 'GROUP', 13, 9, 10, '2030-06-15 18:00:00', '2030-06-15 18:00:00', 'UPCOMING', 'UPCOMING'),
(6, 6, 'GROUP', 2, 11, 12, '2030-06-15 21:00:00', '2030-06-15 21:00:00', 'UPCOMING', 'UPCOMING'),
(7, 7, 'GROUP', 4, 13, 14, '2030-06-16 18:00:00', '2030-06-16 18:00:00', 'UPCOMING', 'UPCOMING'),
(8, 8, 'GROUP', 3, 15, 16, '2030-06-16 21:00:00', '2030-06-16 21:00:00', 'UPCOMING', 'UPCOMING'),
(9, 9, 'GROUP', 1, 17, 18, '2030-06-17 18:00:00', '2030-06-17 18:00:00', 'UPCOMING', 'UPCOMING'),
(10, 10, 'GROUP', 11, 19, 20, '2030-06-17 21:00:00', '2030-06-17 21:00:00', 'UPCOMING', 'UPCOMING'),
(11, 11, 'GROUP', 15, 21, 22, '2030-06-18 18:00:00', '2030-06-18 18:00:00', 'UPCOMING', 'UPCOMING'),
(12, 12, 'GROUP', 14, 25, 26, '2030-06-18 21:00:00', '2030-06-18 21:00:00', 'UPCOMING', 'UPCOMING'),
(13, 13, 'GROUP', 8, 29, 30, '2030-06-19 18:00:00', '2030-06-19 18:00:00', 'UPCOMING', 'UPCOMING'),
(14, 14, 'GROUP', 6, 33, 34, '2030-06-19 21:00:00', '2030-06-19 21:00:00', 'UPCOMING', 'UPCOMING'),
(15, 15, 'GROUP', 16, 37, 38, '2030-06-20 18:00:00', '2030-06-20 18:00:00', 'UPCOMING', 'UPCOMING'),
(16, 16, 'GROUP', 7, 41, 42, '2030-06-20 21:00:00', '2030-06-20 21:00:00', 'UPCOMING', 'UPCOMING'),
(17, 17, 'GROUP', 9, 45, 46, '2030-06-21 20:00:00', '2030-06-21 20:00:00', 'UPCOMING', 'UPCOMING'),
(18, 73, 'ROUND_32', 2, NULL, NULL, '2030-07-01 18:00:00', '2030-07-01 18:00:00', 'UPCOMING', 'UPCOMING'),
(19, 89, 'ROUND_16', 1, NULL, NULL, '2030-07-06 20:00:00', '2030-07-06 20:00:00', 'UPCOMING', 'UPCOMING'),
(20, 97, 'QUARTER_FINAL', 13, NULL, NULL, '2030-07-10 21:00:00', '2030-07-10 21:00:00', 'UPCOMING', 'UPCOMING'),
(21, 101, 'SEMI_FINAL', 2, NULL, NULL, '2030-07-14 21:00:00', '2030-07-14 21:00:00', 'UPCOMING', 'UPCOMING'),
(22, 103, 'THIRD_PLACE', 10, NULL, NULL, '2030-07-18 18:00:00', '2030-07-18 18:00:00', 'UPCOMING', 'UPCOMING'),
(23, 104, 'FINAL', 13, NULL, NULL, '2030-07-21 20:00:00', '2030-07-21 20:00:00', 'UPCOMING', 'UPCOMING');

-- ============================================================================
-- 9. STORED PROCEDURE SINH 100,000 GHẾ BẢNG match_seats THEO TRẬN
-- ============================================================================

DELIMITER //

DROP PROCEDURE IF EXISTS generate_100k_stadium_seats //

CREATE PROCEDURE generate_100k_stadium_seats(IN target_match_id INT)
BEGIN
    DECLARE target_stadium_id INT;
    DECLARE vip_zone_id INT;
    DECLARE std_zone_id INT;

    -- Lấy id sân vận động của trận đấu
    SELECT stadium_id INTO target_stadium_id FROM matches WHERE id = target_match_id;

    -- Lấy ID khu vực ghế tương ứng
    SELECT id INTO vip_zone_id FROM seat_zones WHERE stadium_id = target_stadium_id AND type = 'VIP' LIMIT 1;
    SELECT id INTO std_zone_id FROM seat_zones WHERE stadium_id = target_stadium_id AND type = 'STANDARD' LIMIT 1;

    -- Tạo bảng phụ dãy số
    CREATE TEMPORARY TABLE IF NOT EXISTS seq_100 (n INT PRIMARY KEY);
    IF (SELECT COUNT(*) FROM seq_100) = 0 THEN
        INSERT INTO seq_100 (n) VALUES
        (1),(2),(3),(4),(5),(6),(7),(8),(9),(10),
        (11),(12),(13),(14),(15),(16),(17),(18),(19),(20),
        (21),(22),(23),(24),(25),(26),(27),(28),(29),(30),
        (31),(32),(33),(34),(35),(36),(37),(38),(39),(40),
        (41),(42),(43),(44),(45),(46),(47),(48),(49),(50);
    END IF;

    -- A. Sinh 10,000 Ghế VIP cho trận đấu
    INSERT INTO match_seats (match_id, seat_zone_id, seat_code, price, status)
    SELECT target_match_id, vip_zone_id, CONCAT('VIP-BLK', b.n, '-R', r.n, '-', LPAD(s.seat_num, 2, '0')), 500.00, 'AVAILABLE'
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

    -- B. Sinh 90,000 Ghế Phổ thông cho trận đấu
    INSERT INTO match_seats (match_id, seat_zone_id, seat_code, price, status)
    SELECT target_match_id, std_zone_id, CONCAT('STD-', sec.sec_code, '-BLK', (100 + b.n), '-R', r.n, '-', LPAD(s.seat_num, 2, '0')), 150.00, 'AVAILABLE'
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

-- Sinh ghế mẫu cho Trận 1 (Khai mạc) và Trận 23 (Chung kết)
CALL generate_100k_stadium_seats(1);
CALL generate_100k_stadium_seats(23);
