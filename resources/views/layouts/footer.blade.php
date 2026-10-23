<footer class="wc-footer">
    <div class="container">
        <!-- Main Footer Row -->
        <div class="row g-4 mb-5">
            <!-- Brand & Host Countries -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="wc-brand-trophy" aria-hidden="true">
                        <i class="fa-solid fa-trophy"></i>
                    </span>
                    <div>
                        <h5 class="fw-bold text-white mb-0 font-display">FIFA WORLD CUP 2030</h5>
                        <small class="text-gold text-uppercase fw-semibold" style="letter-spacing: 0.1em; font-size: 0.72rem; color: #f59e0b;">
                            Centenary 1930 &bull; 2030
                        </small>
                    </div>
                </div>
                <p class="small text-secondary mb-3">
                    Hệ thống đặt vé và quản lý trực tuyến giải vô địch bóng đá thế giới FIFA World Cup 2030. Lần đầu tiên trong lịch sử ngày hội bóng đá lớn nhất hành tinh được tổ chức tại 3 châu lục với 6 quốc gia tham gia khai mạc.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-dark border border-secondary border-opacity-50 text-light px-2 py-1">
                        <img src="https://flagcdn.com/w20/es.png" alt="ES" style="width: 14px; height: 10px; margin-right: 4px;"> Tây Ban Nha
                    </span>
                    <span class="badge bg-dark border border-secondary border-opacity-50 text-light px-2 py-1">
                        <img src="https://flagcdn.com/w20/pt.png" alt="PT" style="width: 14px; height: 10px; margin-right: 4px;"> Bồ Đào Nha
                    </span>
                    <span class="badge bg-dark border border-secondary border-opacity-50 text-light px-2 py-1">
                        <img src="https://flagcdn.com/w20/ma.png" alt="MA" style="width: 14px; height: 10px; margin-right: 4px;"> Ma-rốc
                    </span>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 col-sm-6">
                <h6 class="wc-footer-title">Khám Phá</h6>
                <div class="wc-footer-links">
                    <a href="{{ url('/') }}"><i class="fa-solid fa-angle-right me-1 text-warning small"></i> Trang chủ</a>
                    <a href="{{ url('/matches') }}"><i class="fa-solid fa-angle-right me-1 text-warning small"></i> Lịch thi đấu</a>
                    <a href="{{ url('/matches') }}"><i class="fa-solid fa-angle-right me-1 text-warning small"></i> Đặt vé trực tuyến</a>
                    <a href="{{ url('/#groups-section') }}"><i class="fa-solid fa-angle-right me-1 text-warning small"></i> Bảng đấu A - L</a>
                    <a href="{{ url('/my-tickets') }}"><i class="fa-solid fa-angle-right me-1 text-warning small"></i> Tra cứu vé đã đặt</a>
                </div>
            </div>

            <!-- Host Stadiums & Cities -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <h6 class="wc-footer-title">Thánh Địa Đăng Cai</h6>
                <div class="wc-footer-links">
                    <span class="d-block text-secondary small mb-2"><i class="fa-solid fa-location-dot text-danger me-1"></i> Santiago Bernabéu (Madrid)</span>
                    <span class="d-block text-secondary small mb-2"><i class="fa-solid fa-location-dot text-danger me-1"></i> Spotify Camp Nou (Barcelona)</span>
                    <span class="d-block text-secondary small mb-2"><i class="fa-solid fa-location-dot text-danger me-1"></i> Estádio da Luz (Lisbon)</span>
                    <span class="d-block text-secondary small mb-2"><i class="fa-solid fa-location-dot text-danger me-1"></i> Grand Stade de Casablanca</span>
                    <span class="d-block text-secondary small"><i class="fa-solid fa-location-dot text-danger me-1"></i> San Mamés (Bilbao)</span>
                </div>
            </div>

            <!-- Support & Contact -->
            <div class="col-lg-3 col-md-6">
                <h6 class="wc-footer-title">Hỗ Trợ Khách Hàng</h6>
                <div class="wc-footer-links">
                    <span class="d-block text-secondary small mb-2"><i class="fa-solid fa-envelope text-warning me-2"></i> support@worldcup2030.com</span>
                    <span class="d-block text-secondary small mb-2"><i class="fa-solid fa-phone text-warning me-2"></i> Hotline: 1900 2030 (8:00 - 22:00)</span>
                    <span class="d-block text-secondary small"><i class="fa-solid fa-clock text-warning me-2"></i> Hỗ trợ kỹ thuật trực tuyến 24/7</span>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <hr class="border-secondary border-opacity-25 my-4">

        <!-- Bottom Copyright & Legal Links -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 small text-secondary pt-2">
            <div>
                &copy; 2030 FIFA World Cup&trade; &bull; Bản quyền thuộc về FIFA. Phát triển bởi <strong>Nhóm 2</strong>.
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <a href="{{ url('/') }}" class="text-secondary text-decoration-none hover-gold">Chính sách bảo mật</a>
                <span class="opacity-50">&bull;</span>
                <a href="{{ url('/') }}" class="text-secondary text-decoration-none hover-gold">Điều khoản dịch vụ</a>
                <span class="opacity-50">&bull;</span>
                <a href="{{ url('/') }}" class="text-secondary text-decoration-none hover-gold">Quy định vé FIFA</a>
                <span class="opacity-50">&bull;</span>
                <a href="{{ url('/') }}" class="text-secondary text-decoration-none hover-gold">Cài đặt Cookie</a>
            </div>
        </div>
    </div>
</footer>
