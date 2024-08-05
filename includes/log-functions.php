<?php
// Function to log events
function aatp_log($message) {
    $logging_enabled = get_option('aatp_logging_enabled', false);
    if (!$logging_enabled) {
        return;
    }

    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/aatp_log.txt';

    if (!is_writable($log_file)) {
        error_log("AATP: Log file is not writable.");
        return;
    }

    $time = current_time('Y-m-d H:i:s');
    $log_entry = "{$time} - {$message}\n";

    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Handle form submissions for log clearing and downloading
add_action('admin_post_aatp_clear_log', 'aatp_clear_log');
add_action('admin_post_aatp_download_log', 'aatp_download_log');

function aatp_clear_log() {
    check_admin_referer('aatp_settings');
    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/aatp_log.txt';
    if (file_exists($log_file)) {
        unlink($log_file);
    }
    wp_redirect(admin_url('options-general.php?page=auto-alt-text-to-posts'));
    exit;
}

function aatp_download_log() {
    check_admin_referer('aatp_settings');
    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/aatp_log.txt';
    if (file_exists($log_file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($log_file));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($log_file));
        readfile($log_file);
        exit;
    } else {
        wp_redirect(admin_url('options-general.php?page=auto-alt-text-to-posts'));
        exit;
    }
}
?>
