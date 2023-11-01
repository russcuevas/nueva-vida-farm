document.addEventListener('DOMContentLoaded', function () {
    const cancelOrderBtns = document.querySelectorAll('.cancel-order-btn');

    cancelOrderBtns.forEach(btn => {
        btn.addEventListener('click', function (event) {
            event.preventDefault();
            const orderId = this.getAttribute('data-orderid');

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
                    fetch(`../components/cancel_order.php?order_id=${orderId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: data.message,
                                    toast: true,
                                    position: "top-end",
                                    showConfirmButton: false,
                                    timer: 3000,
                                }).then(function () {
                                    location.reload();
                                }, 2000);
                            } else if (data.status === 400) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Cancellation Status',
                                    title: data.message,
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'An error occurred while canceling the order',
                                icon: 'error'
                            });
                        });
                }
            });
        });
    });
});