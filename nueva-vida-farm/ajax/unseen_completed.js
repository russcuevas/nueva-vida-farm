function updatecompletedUnread() {
    $.ajax({
        url: 'functions/unseen_completed.php',
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            var count = data.count;
            var badge = $('#completedUnread');

            if (count > 0) {
                badge.css('color', 'red');
                badge.text('(' + count + ') Unread');
            } else {
                badge.css('color', 'white');
                badge.text('');
            }

            updatecompletedUnread();
        },
        error: function () {
            console.log('Error fetching data');
            updatecompletedUnread();
        }
    });
}

updatecompletedUnread();
