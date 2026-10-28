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
        return $this->subject('Vé điện tử FIFA World Cup 2030 – Mã đơn #' . ($this->order->order_code ?? $this->order->id))
                    ->view('emails.ticket-confirmation');
    }
}
