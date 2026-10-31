/**
 * Interactive Seat Map Manager for FIFA World Cup 2030
 */
document.addEventListener('DOMContentLoaded', function () {
    const seatMapContainer = document.getElementById('seatMapContainer');
    if (!seatMapContainer) return;

    const matchId = seatMapContainer.getAttribute('data-match-id');
    const loadingEl = document.getElementById('seatMapLoading');
    const zonesContainer = document.getElementById('zonesContainer');
    const selectedCountEl = document.getElementById('selectedCount');
    const selectedSeatsListEl = document.getElementById('selectedSeatsList');
    const totalAmountEl = document.getElementById('totalAmount');
    const btnProceedBooking = document.getElementById('btnProceedBooking');
    const bookingForm = document.getElementById('bookingForm');
    const hiddenSeatIdsContainer = document.getElementById('hiddenSeatIds');
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
    const seatsApiUrl = seatMapContainer.getAttribute('data-seats-url') || `${baseUrl}/api/matches/${matchId}/seats`;
    const checkSeatsApiUrl = seatMapContainer.getAttribute('data-check-seats-url') || `${baseUrl}/api/matches/${matchId}/check-seats`;

    let selectedSeats = [];
    const MAX_SEATS = 8;

    // 1. Tải sơ đồ ghế từ API GET /api/matches/{id}/seats
    fetch(seatsApiUrl)
        .then(response => {
            if (!response.ok) throw new Error('Không thể tải dữ liệu sơ đồ ghế.');
            return response.json();
        })
        .then(res => {
            if (loadingEl) loadingEl.style.display = 'none';
            if (res.success && res.data) {
                renderSeatMap(res.data);
            } else {
                zonesContainer.innerHTML = `<div class="alert alert-warning text-center">Không có dữ liệu ghế cho trận đấu này.</div>`;
            }
        })
        .catch(err => {
            if (loadingEl) loadingEl.style.display = 'none';
            zonesContainer.innerHTML = `<div class="alert alert-danger text-center"><i class="fa-solid fa-triangle-exclamation me-2"></i> ${err.message}</div>`;
        });

    // 2. Render các khu vực và danh sách ghế
    function renderSeatMap(data) {
        zonesContainer.innerHTML = '';
        const zones = data.zones || [];
        const allSeats = data.seats || [];

        if (zones.length === 0) {
            zonesContainer.innerHTML = `<div class="alert alert-info text-center">Chưa có khu vực khán đài nào được thiết lập.</div>`;
            return;
        }

        // Đảm bảo mỗi zone luôn có mảng ghế tương ứng kể cả khi API trả về mảng phẳng
        zones.forEach(zone => {
            if (!zone.seats || !Array.isArray(zone.seats) || zone.seats.length === 0) {
                zone.seats = allSeats.filter(s => parseInt(s.seat_zone_id, 10) === parseInt(zone.id, 10));
            }
        });

        const vipZones = zones.filter(zone => String(zone.type).toUpperCase() === 'VIP');
        const standardZones = zones.filter(zone => String(zone.type).toUpperCase() !== 'VIP');
        const seatOffsets = new Map();
        let vipSeatOffset = 0;

        vipZones.forEach(zone => {
            seatOffsets.set(zone.id, vipSeatOffset);
            vipSeatOffset += (zone.seats || []).length;
        });

        zones.forEach(zone => {
            const zoneCard = document.createElement('div');
            zoneCard.className = 'zone-card';

            const zoneName = zone.name || zone.zone_name || 'Khán đài';
            const price = parseFloat(zone.base_price || (zone.seats[0] ? zone.seats[0].price : 0));
            const availableSeatsCount = (zone.seats || []).filter(s => s.status === 'AVAILABLE').length;
            const escapedZoneName = escapeHtml(zoneName);
            const escapedZoneType = escapeHtml(zone.type || 'STANDARD');

            zoneCard.innerHTML = `
                <div class="zone-header">
                    <div>
                        <strong class="text-warning"><i class="fa-solid fa-users-rectangle me-1"></i> ${escapedZoneName}</strong>
                        <span class="badge ${zone.type === 'VIP' ? 'bg-danger' : 'bg-primary'} ms-2">${escapedZoneType}</span>
                    </div>
                    <div class="text-end">
                        <span class="text-white fw-bold me-2">${formatCurrency(price)} VNĐ</span>
                        <small class="text-secondary">(Còn trống: <strong class="text-success">${availableSeatsCount}</strong>/${(zone.seats || []).length})</small>
                    </div>
                </div>
                <div class="seat-grid" id="zone-seats-${zone.id}"></div>
            `;

            zonesContainer.appendChild(zoneCard);

            const seatGrid = zoneCard.querySelector(`#zone-seats-${zone.id}`);
            const isVip = String(zone.type).toUpperCase() === 'VIP';
            const typedZones = isVip ? vipZones : standardZones;
            const typeZoneIndex = typedZones.findIndex(item => String(item.id) === String(zone.id));
            const seats = [...(zone.seats || [])].sort((left, right) => Number(left.id) - Number(right.id));

            seats.forEach((seat, index) => {
                const display = getSeatDisplayInfo(index, isVip, typeZoneIndex, seatOffsets.get(zone.id) || 0);
                const seatBtn = document.createElement('button');
                seatBtn.type = 'button';
                seatBtn.className = `seat ${seat.status.toLowerCase()}`;
                seatBtn.innerHTML = `<span class="seat-block">${escapeHtml(display.block)}</span><span class="seat-label">${escapeHtml(display.label)}</span>`;
                seatBtn.dataset.id = seat.id;
                seatBtn.dataset.code = seat.seat_code;
                seatBtn.dataset.price = seat.price;
                seatBtn.dataset.zoneName = zoneName;
                seatBtn.dataset.status = seat.status;
                seatBtn.dataset.block = display.block;
                seatBtn.dataset.label = display.label;
                updateSeatInfo(seatBtn, seat.status);

                if (seat.status === 'AVAILABLE') {
                    seatBtn.addEventListener('click', () => toggleSeatSelection(seatBtn));
                } else {
                    seatBtn.disabled = true;
                }

                seatGrid.appendChild(seatBtn);
            });
        });
    }

    // 3. Xử lý click chọn / bỏ chọn ghế
    function toggleSeatSelection(seatBtn) {
        const id = parseInt(seatBtn.dataset.id, 10);
        const code = seatBtn.dataset.code;
        const price = parseFloat(seatBtn.dataset.price);
        const zoneName = seatBtn.dataset.zoneName;
        const block = seatBtn.dataset.block;
        const label = seatBtn.dataset.label;
        const status = 'Đang chọn';

        const index = selectedSeats.findIndex(s => s.id === id);

        if (index > -1) {
            // Bỏ chọn
            selectedSeats.splice(index, 1);
            seatBtn.classList.remove('selected');
            updateSeatInfo(seatBtn, seatBtn.dataset.status);
        } else {
            // Chọn mới (giới hạn tối đa 8 ghế)
            if (selectedSeats.length >= MAX_SEATS) {
                alert(`Bạn chỉ được chọn tối đa ${MAX_SEATS} ghế cho một lần đặt vé.`);
                return;
            }
            selectedSeats.push({ id, code, price, zoneName, block, label, status });
            seatBtn.classList.add('selected');
            updateSeatInfo(seatBtn, 'SELECTED');
        }

        updateSummary();
    }

    // 4. Cập nhật thanh giỏ vé và tổng tiền
    function updateSummary() {
        const total = selectedSeats.reduce((sum, s) => sum + s.price, 0);

        if (selectedCountEl) selectedCountEl.textContent = selectedSeats.length;
        if (totalAmountEl) totalAmountEl.textContent = formatCurrency(total) + ' VNĐ';

        if (selectedSeatsListEl) {
            if (selectedSeats.length === 0) {
                selectedSeatsListEl.innerHTML = `<span class="text-muted small">Chưa có ghế nào được chọn. Hãy nhấp vào các ô ghế màu xanh trên sơ đồ.</span>`;
            } else {
                selectedSeatsListEl.innerHTML = selectedSeats.map(s => `
                    <span class="badge bg-warning text-dark me-1 mb-1 p-2">
                        <strong>${escapeHtml(s.zoneName)}</strong>
                        <span class="d-block fw-semibold">${escapeHtml(s.block)} - ${escapeHtml(s.label)}</span>
                        <span class="d-block fw-normal">${formatCurrency(s.price)} VNĐ · ${escapeHtml(s.status)}</span>
                    </span>
                `).join('');
            }
        }

        if (btnProceedBooking) {
            btnProceedBooking.disabled = selectedSeats.length === 0;
        }

        // Cập nhật các input ẩn cho form đặt vé
        if (hiddenSeatIdsContainer) {
            hiddenSeatIdsContainer.innerHTML = selectedSeats.map(s => `
                <input type="hidden" name="seat_ids[]" value="${s.id}">
            `).join('');
        }
    }

    // 5. Kiểm tra tính khả dụng của ghế trước khi gửi form chuyển sang checkout
    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (selectedSeats.length === 0) {
                alert('Vui lòng chọn ít nhất một ghế.');
                return;
            }

            btnProceedBooking.disabled = true;
            btnProceedBooking.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i> Đang kiểm tra ghế...`;

            // Gọi API kiểm tra ghế trước thanh toán: POST /api/matches/{id}/check-seats
            fetch(checkSeatsApiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    seat_ids: selectedSeats.map(s => s.id)
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.is_available) {
                    // Ghế hợp lệ và sẵn sàng -> submit form sang /booking/checkout
                    bookingForm.submit();
                } else {
                    alert(res.message || 'Một số ghế bạn chọn vừa có người khác đặt. Vui lòng chọn ghế khác!');
                    location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                // Nếu kiểm tra API gặp sự cố mạng, vẫn cho submit form để backend kiểm tra transaction
                bookingForm.submit();
            });
        });
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }

    function formatSeatStatus(status) {
        const labels = {
            AVAILABLE: 'Còn trống',
            BOOKED: 'Đã đặt',
            HELD: 'Đang giữ chỗ',
            RESERVED: 'Đã giữ chỗ',
            SELECTED: 'Đang chọn'
        };

        return labels[status] || status;
    }

    function updateSeatInfo(seatBtn, status) {
        seatBtn.title = `Ghế: ${seatBtn.dataset.block} ${seatBtn.dataset.label} (${seatBtn.dataset.code}) | Khu vực: ${seatBtn.dataset.zoneName} | Giá: ${formatCurrency(seatBtn.dataset.price)} VNĐ | Trạng thái: ${formatSeatStatus(status)}`;
        seatBtn.setAttribute('aria-label', seatBtn.title);
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[character]);
    }

    function getSeatDisplayInfo(index, isVip, typeZoneIndex, vipSeatOffset) {
        if (isVip) {
            const seatIndex = vipSeatOffset + index;
            const seatInBlock = seatIndex % 30;
            const row = Math.floor(seatInBlock / 15) + 1;
            const seatNumber = String((seatInBlock % 15) + 1).padStart(2, '0');

            return {
                block: `VIP-${Math.floor(seatIndex / 30) + 1}`,
                label: `R${row}-${seatNumber}`
            };
        }

        const section = typeZoneIndex < 26
            ? String.fromCharCode('A'.charCodeAt(0) + Math.max(0, typeZoneIndex))
            : `S${typeZoneIndex + 1}`;
        const seatNumber = String((index % 24) + 1).padStart(2, '0');

        return {
            block: `${section}-${101 + Math.floor(index / 24)}`,
            label: `#${seatNumber}`
        };
    }
});
