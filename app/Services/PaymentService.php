<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MatchSeat;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/** Coordinates payment records and atomically activates orders and tickets. */
class PaymentService
{
    public function __construct(private TicketService $ticketService) {}

    /** @return array{success:bool,message:string,data:mixed,code:int} */
    public function createPaymentTransaction(int $orderId, string $method): array
    {
        $method = strtoupper(trim($method));
        if ($method === '' || strlen($method) > 50) return $this->result(false, 'Phương thức thanh toán không hợp lệ.', null, 400);
        try {
            $payment = DB::transaction(function () use ($orderId, $method): Payment {
                $order = Order::query()->whereKey($orderId)->lockForUpdate()->first();
                if (!$order) throw new \OutOfBoundsException('Không tìm thấy đơn hàng.');
                if ($order->status !== 'PENDING') throw new \DomainException('Đơn hàng không còn chờ thanh toán.');
                if ($order->hold_expires_at && $order->hold_expires_at->isPast()) throw new \DomainException('Thời gian giữ ghế đã hết hạn.');
                $existing = Payment::query()->where('order_id', $orderId)->where('status', 'PENDING')->lockForUpdate()->first();
                if ($existing) return $existing;
                return Payment::query()->create(['order_id' => $orderId, 'payment_method' => $method,
                    'transaction_id' => (string) Str::uuid(), 'amount' => $order->total_price, 'status' => 'PENDING']);
            }, 3);
            return $this->result(true, 'Đã tạo giao dịch thanh toán.', $payment);
        } catch (\OutOfBoundsException $exception) {
            return $this->result(false, $exception->getMessage(), null, 404);
        } catch (\DomainException $exception) {
            return $this->result(false, $exception->getMessage(), null, 409);
        } catch (Throwable $exception) {
            Log::error('Unable to create payment transaction.', ['order_id' => $orderId, 'exception' => $exception]);
            return $this->result(false, 'Không thể khởi tạo thanh toán lúc này.', null, 500);
        }
    }

    /** Call only after the gateway callback signature and amount have been verified. */
    public function processPaymentSuccess(int $orderId, string $transactionId, string $method = 'SIMULATION'): array
    {
        try {
            $order = DB::transaction(function () use ($orderId, $transactionId, $method): Order {
                $order = Order::query()->whereKey($orderId)->lockForUpdate()->first();
                if (!$order) throw new \OutOfBoundsException('Không tìm thấy đơn hàng.');
                if ($order->status === 'PAID') return $order->load('tickets'); // Idempotent webhook retry.
                if ($order->status !== 'PENDING') throw new \DomainException('Đơn hàng không còn chờ thanh toán.');
                if ($order->hold_expires_at && $order->hold_expires_at->isPast()) throw new \DomainException('Thời gian giữ ghế đã hết hạn.');

                $payment = Payment::query()->where('order_id', $orderId)->where('transaction_id', $transactionId)->lockForUpdate()->first();
                if (!$payment) throw new \DomainException('Không tìm thấy giao dịch thanh toán hợp lệ.');
                if ($payment->status === 'FAILED') throw new \DomainException('Giao dịch đã bị từ chối.');
                if (strtoupper($payment->payment_method) !== strtoupper($method)) throw new \DomainException('Phương thức thanh toán không khớp.');
                if ($this->toCents((string) $payment->amount) !== $this->toCents((string) $order->total_price)) throw new \DomainException('Số tiền thanh toán không khớp đơn hàng.');

                $tickets = Ticket::query()->where('order_id', $orderId)->lockForUpdate()->get();
                if ($tickets->isEmpty()) throw new \DomainException('Đơn hàng không có ghế đang giữ.');
                $seatIds = $tickets->pluck('match_seat_id')->all();
                $seats = MatchSeat::query()->whereIn('id', $seatIds)->orderBy('id')->lockForUpdate()->get();
                if ($seats->count() !== count($seatIds) || $seats->contains(fn (MatchSeat $seat): bool => $seat->status !== 'HELD')) {
                    throw new \DomainException('Ghế giữ không còn hợp lệ.');
                }

                $payment->status = 'SUCCESS';
                $payment->save();
                $order->status = 'PAID';
                $order->payment_status = 'PAID';
                $order->payment_method = strtoupper($method);
                $order->save();
                foreach ($seats as $seat) { $seat->status = 'BOOKED'; $seat->save(); }
                $ticketResult = $this->ticketService->generateTicketsForOrder($order);
                if (!$ticketResult['success']) throw new \RuntimeException($ticketResult['message']);
                return $order->fresh()->load('tickets');
            }, 3);
            return $this->result(true, 'Thanh toán thành công.', $order);
        } catch (\OutOfBoundsException $exception) {
            return $this->result(false, $exception->getMessage(), null, 404);
        } catch (\DomainException $exception) {
            return $this->result(false, $exception->getMessage(), null, 409);
        } catch (Throwable $exception) {
            Log::error('Payment success processing failed.', ['order_id' => $orderId, 'transaction_id' => $transactionId, 'exception' => $exception]);
            return $this->result(false, 'Không thể xác nhận thanh toán lúc này.', null, 500);
        }
    }

    /** @return array{success:bool,message:string,data:mixed,code:int} */
    public function processPaymentFailure(int $orderId, string $reason): array
    {
        try {
            $order = DB::transaction(function () use ($orderId, $reason): Order {
                $order = Order::query()->whereKey($orderId)->lockForUpdate()->first();
                if (!$order) throw new \OutOfBoundsException('Không tìm thấy đơn hàng.');
                if ($order->status !== 'PENDING') throw new \DomainException('Đơn hàng không còn chờ thanh toán.');
                Payment::query()->where('order_id', $orderId)->where('status', 'PENDING')->lockForUpdate()->get()->each(function (Payment $payment): void {
                    $payment->status = 'FAILED'; $payment->save();
                });
                $tickets = Ticket::query()->where('order_id', $orderId)->get(['id', 'match_seat_id']);
                MatchSeat::query()->whereIn('id', $tickets->pluck('match_seat_id'))->where('status', 'HELD')->lockForUpdate()->update(['status' => 'AVAILABLE']);
                Ticket::query()->where('order_id', $orderId)->delete();
                $order->status = 'CANCELLED'; $order->payment_status = 'CANCELLED'; $order->save();
                return $order;
            }, 3);
            Log::info('Payment failed; order cancelled and held seats released.', ['order_id' => $orderId, 'reason' => $reason]);
            return $this->result(true, 'Thanh toán thất bại; đơn hàng đã hủy và ghế được giải phóng.', $order);
        } catch (\OutOfBoundsException $exception) {
            return $this->result(false, $exception->getMessage(), null, 404);
        } catch (\DomainException $exception) {
            return $this->result(false, $exception->getMessage(), null, 409);
        } catch (Throwable $exception) {
            Log::error('Payment failure processing failed.', ['order_id' => $orderId, 'exception' => $exception]);
            return $this->result(false, 'Không thể cập nhật kết quả thanh toán.', null, 500);
        }
    }

    /** Demo helper. Real gateways must use a signed and verified callback. */
    public function simulatePayment(int $orderId): array
    {
        $paymentResult = $this->createPaymentTransaction($orderId, 'SIMULATION');
        if (!$paymentResult['success']) return $paymentResult;
        /** @var Payment $payment */
        $payment = $paymentResult['data'];
        return $this->processPaymentSuccess($orderId, (string) $payment->transaction_id, 'SIMULATION');
    }

    /** @return array{success:bool,message:string,data:mixed,code:int} */
    private function result(bool $success, string $message, mixed $data = null, int $code = 200): array
    {
        return compact('success', 'message', 'data', 'code');
    }

    private function toCents(string $amount): int
    {
        return (int) round((float) $amount * 100);
    }
}
