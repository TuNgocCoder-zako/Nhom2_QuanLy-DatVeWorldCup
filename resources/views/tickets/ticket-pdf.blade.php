<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Vé Điện Tử - FIFA World Cup 2030</title>
    <style>
        @page {
            margin: 0;
            size: A5 landscape;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0b132b;
            color: #ffffff;
            font-size: 12px;
        }
        .ticket-container {
            width: 100%;
            height: 100%;
            display: table;
            table-layout: fixed;
            box-sizing: border-box;
        }
        .ticket-left {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            padding: 24px;
            background: linear-gradient(135deg, #0b132b 0%, #1c2541 100%);
            border-right: 2px dashed #48cae4;
        }
        .ticket-right {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
            text-align: center;
            padding: 20px;
            background-color: #ffffff;
            color: #0b132b;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #f77f00;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 11px;
            color: #90e0ef;
            margin-bottom: 15px;
        }
        .match-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .match-teams {
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 6px;
        }
        .match-info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .match-info-table td {
            padding: 3px 0;
            font-size: 11px;
            color: #e0e1dd;
        }
        .seat-badge {
            display: inline-block;
            background: #f77f00;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 13px;
            margin-top: 8px;
        }
        .qr-wrapper {
            margin: 0 auto;
            display: inline-block;
        }
        .qr-wrapper svg {
            width: 130px;
            height: 130px;
        }
        .ticket-code-label {
            font-size: 10px;
            font-weight: bold;
            color: #333333;
            margin-top: 8px;
            word-break: break-all;
        }
        .gate-info {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #dddddd;
            font-size: 11px;
            font-weight: bold;
            color: #0b132b;
        }
        .footer-note {
            font-size: 9px;
            color: #8d99ae;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <!-- Phần thân vé bên trái -->
        <div class="ticket-left">
            <div class="header-title">FIFA WORLD CUP 2030 &trade;</div>
            <div class="subtitle">SPAIN &bull; PORTUGAL &bull; MOROCCO &bull; OFFICIAL E-TICKET</div>

            <div class="match-card">
                <div class="match-teams">
                    {{ $ticket->match->homeTeam->name ?? 'Home Team' }} 
                    <span style="color: #f77f00; font-size: 14px;">VS</span> 
                    {{ $ticket->match->awayTeam->name ?? 'Away Team' }}
                </div>
                <table class="match-info-table">
                    <tr>
                        <td width="35%"><strong>Giai đoạn:</strong></td>
                        <td>{{ $ticket->match->stage ?? 'Vòng bảng' }} (Trận #{{ $ticket->match->match_number ?? $ticket->match->id }})</td>
                    </tr>
                    <tr>
                        <td><strong>Thời gian:</strong></td>
                        <td>{{ $ticket->match->match_time ? \Carbon\Carbon::parse($ticket->match->match_time)->format('H:i - d/m/Y') : 'Chưa cập nhật' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Sân vận động:</strong></td>
                        <td>{{ $ticket->match->stadium->stadium_name ?? ($ticket->match->stadium->name ?? 'Sân vận động chính') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Địa điểm:</strong></td>
                        <td>{{ $ticket->match->stadium->location ?? 'Tây Ban Nha' }}</td>
                    </tr>
                </table>
            </div>

            <table style="width: 100%;">
                <tr>
                    <td>
                        <span class="seat-badge">
                            KHU: {{ $ticket->matchSeat->seatZone->name ?? ($ticket->matchSeat->seatZone->zone_name ?? 'Khán đài') }} | GHẾ: {{ $ticket->matchSeat->seat_code ?? 'A1' }}
                        </span>
                    </td>
                    <td style="text-align: right; color: #48cae4; font-weight: bold; font-size: 14px;">
                        {{ number_format((float) ($ticket->price ?? 0), 0, ',', '.') }} VNĐ
                    </td>
                </tr>
            </table>

            <div class="footer-note">
                Người sở hữu: <strong>{{ $ticket->order->user->name ?? 'Khán giả' }}</strong> | Mã đơn: #{{ $ticket->order->order_code ?? $ticket->order_id }}<br>
                Vé điện tử có giá trị 01 lần quét vào cổng. Vui lòng xuất trình mã QR tại cửa soát vé.
            </div>
        </div>

        <!-- Phần cuống vé bên phải chứa mã QR -->
        <div class="ticket-right">
            <div style="font-weight: bold; font-size: 12px; margin-bottom: 8px; color: #0b132b;">MÃ SOÁT VÉ CỔNG</div>
            <div class="qr-wrapper">
                {!! $qrCodeSvg !!}
            </div>
            <div class="ticket-code-label">
                {{ $ticket->ticket_code }}
            </div>
            <div class="gate-info">
                KHU: {{ $ticket->matchSeat->seatZone->name ?? 'STANDARD' }}<br>
                CỔNG VÀO: GATE {{ substr($ticket->matchSeat->seat_code ?? 'A', 0, 1) }}
            </div>
            <div style="font-size: 8px; color: #888888; margin-top: 6px;">
                TRẠNG THÁI: {{ $ticket->status === 'VALID' ? 'HỢP LỆ' : $ticket->status }}
            </div>
        </div>
    </div>
</body>
</html>
