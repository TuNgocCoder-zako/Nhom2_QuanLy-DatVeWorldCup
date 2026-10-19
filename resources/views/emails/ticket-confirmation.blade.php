<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác Nhận Đặt Vé FIFA World Cup 2030</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; color: #333333; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e9ecef; }
        .header { background: #0b132b; color: #ffffff; padding: 24px; text-align: center; border-bottom: 3px solid #ffb703; }
        .content { padding: 24px; }
        .ticket-item { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 12px; margin-bottom: 12px; }
        .footer { background: #f1f3f5; padding: 16px; text-align: center; font-size: 12px; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #ffb703; margin: 0;">FIFA WORLD CUP 2030</h2>
            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">XÁC NHẬN ĐẶT VÉ THÀNH CÔNG</p>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $order->user->name ?? 'Quý khách' }}</strong>,</p>
            <p>Cảm ơn bạn đã mua vé xem các trận cầu đỉnh cao tại FIFA World Cup 2030. Dưới đây là thông tin đơn hàng của bạn:</p>
            
            <p><strong>Mã đơn hàng:</strong> #{{ $order->order_code ?? $order->id }}<br>
            <strong>Tổng số tiền:</strong> {{ number_format((float)($order->total_amount ?? $order->total_price), 0, ',', '.') }} VNĐ<br>
            <strong>Trạng thái:</strong> ĐÃ THANH TOÁN (PAID)</p>

            <h4 style="border-bottom: 2px solid #ffb703; padding-bottom: 6px;">Danh sách vé của bạn:</h4>
            @foreach($order->tickets as $ticket)
            <div class="ticket-item">
                <strong>Trận:</strong> {{ $ticket->match->homeTeam->name ?? 'Team A' }} vs {{ $ticket->match->awayTeam->name ?? 'Team B' }}<br>
                <strong>Vị trí ghế:</strong> {{ $ticket->matchSeat->seat_code ?? 'A1' }} (Khu vực: {{ $ticket->matchSeat->seatZone->name ?? 'STANDARD' }})<br>
                <strong>Mã vé:</strong> <code>{{ $ticket->ticket_code }}</code>
            </div>
            @endforeach

            <p style="margin-top: 20px;">Bạn có thể đăng nhập vào hệ thống để tải file vé điện tử PDF có mã QR soát vé cổng.</p>
        </div>
        <div class="footer">
            &copy; 2030 FIFA World Cup Organising Committee. Mọi quyền được bảo lưu.
        </div>
    </div>
</body>
</html>
