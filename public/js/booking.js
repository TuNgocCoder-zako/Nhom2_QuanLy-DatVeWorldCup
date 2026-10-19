/**
 * Booking & Payment Checkout Helper
 */
document.addEventListener('DOMContentLoaded', function () {
    // Tự động kích hoạt hiệu ứng chọn phương thức thanh toán
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('label.card').forEach(card => card.classList.remove('border-warning', 'bg-light'));
            const parentCard = this.closest('label.card');
            if (parentCard) {
                parentCard.classList.add('border-warning', 'bg-light');
            }
        });
    });
});
