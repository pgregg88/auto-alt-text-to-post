(function($) {
    function fetchLogContent() {
        $.ajax({
            url: aatp_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'aatp_fetch_log',
                security: aatp_ajax.nonce
            },
            success: function(response) {
                $('#aatp-log-content').html(response);
            }
        });
    }

    $(document).ready(function() {
        fetchLogContent();
        setInterval(fetchLogContent, 5000); // Update log content every 5 seconds
    });
})(jQuery);
