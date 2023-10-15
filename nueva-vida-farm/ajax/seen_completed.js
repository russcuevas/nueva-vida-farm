$("#markAsSeenLink").on("click", function (event) {
    event.preventDefault();

    $.ajax({
        url: 'functions/seen_completed.php',
        method: 'POST',
        data: { markAsSeen: true },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                window.location.href = 'completed_orders';
            } else {
                console.log('Error marking as seen');
            }
        },
        error: function () {
            console.log('Error marking as seen');
        }
    });
});
