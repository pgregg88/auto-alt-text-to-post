<?php
/*
Plugin Name: Auto Alt Text to Posts
Description: Automatically updates image alt text for posts based on a button trigger in the settings page.
Version: 1.0
Author: Preston Gregg
*/

// Register activation hook to set default options
function aatp_activate() {
    if (get_option('aatp_logging_enabled') === false) {
        add_option('aatp_logging_enabled', '0');
    }
    if (get_option('aatp_post_limit') === false) {
        add_option('aatp_post_limit', '');
    }
    if (get_option('aatp_post_select') === false) {
        add_option('aatp_post_select', '');
    }
    if (get_option('aatp_allowed_post_types') === false) {
        add_option('aatp_allowed_post_types', 'capabilities,consulting-services');
    }
}
register_activation_hook(__FILE__, 'aatp_activate');

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';
include_once plugin_dir_path(__FILE__) . 'includes/log-functions.php';
include_once plugin_dir_path(__FILE__) . 'includes/alt-text-functions.php';
include_once plugin_dir_path(__FILE__) . 'includes/ajax-functions.php';

// Enqueue JavaScript for the settings page
function aatp_enqueue_scripts($hook) {
    if ($hook != 'settings_page_auto-alt-text-to-posts') {
        return;
    }
    wp_enqueue_script('aatp_log_script', plugin_dir_url(__FILE__) . 'js/aatp_log.js', array('jquery'), '1.0', true);
    wp_localize_script('aatp_log_script', 'aatp_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('aatp_log_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'aatp_enqueue_scripts');
?>
