<?php
// AJAX handler to fetch log content
function aatp_fetch_log() {
    check_ajax_referer('aatp_log_nonce', 'security');

    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/aatp_log.txt';
    $log_content = '';

    if (file_exists($log_file)) {
        $file_size = filesize($log_file);
        $lines_to_read = 10; // Adjust as needed
        $file = fopen($log_file, 'r');
        $pos = $file_size - 1;
        $current_line = '';
        $line_count = 0;

        while ($pos > 0 && $line_count < $lines_to_read) {
            fseek($file, $pos);
            $char = fgetc($file);
            if ($char === "\n" && $current_line !== '') {
                $log_content = htmlspecialchars($current_line) . "<br>" . $log_content;
                $current_line = '';
                $line_count++;
            } else {
                $current_line = $char . $current_line;
            }
            $pos--;
        }
        fclose($file);
    } else {
        $log_content = '<p>No log entries found.</p>';
    }

    echo $log_content;
    wp_die();
}
add_action('wp_ajax_aatp_fetch_log', 'aatp_fetch_log');
?>
