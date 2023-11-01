
document.addEventListener('DOMContentLoaded', function () {
    const cancelOrderBtns = document.querySelectorAll('.cancel-order-btn');

    cancelOrderBtns.forEach(btn => {
        btn.addEventListener('click', function (event) {
            event.preventDefault();

            const orderLink = this.getAttribute('href');

            Swal.fire({
                title: 'Cancel Order',
                text: 'Are you sure you want to cancel this order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = orderLink;
                }
            });
        });
    });
});