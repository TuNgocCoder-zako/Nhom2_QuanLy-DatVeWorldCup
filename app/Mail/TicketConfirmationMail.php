<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Email containing the purchased electronic ticket codes. */
class TicketConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build(): self
    {
        $rows = $this->order->tickets->map(function ($ticket): string {
            $match = $ticket->match;
            $seat = $ticket->matchSeat;
            $home = e($match?->homeTeam?->name ?? 'TBD');
            $away = e($match?->awayTeam?->name ?? 'TBD');
            $seatCode = e($seat?->seat_code ?? '');
            $code = e($ticket->ticket_code);
            return "<li><strong>{$home} – {$away}</strong> | Ghế {$seatCode} | Mã vé: {$code}</li>";
        })->implode('');
        $orderCode = e((string) $this->order->order_code);
        $html = "<h1>Vé điện tử World Cup</h1><p>Đơn hàng: {$orderCode}</p><ul>{$rows}</ul>";

        return $this->subject('Vé điện tử của bạn – ' . $this->order->order_code)->html($html);
    }
}
