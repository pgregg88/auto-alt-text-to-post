<?php
// Add a settings page to manage the post limit and logging
function aatp_settings_page() {
    add_options_page(
        'Auto Alt Text to Posts Settings',
        'Auto Alt Text to Posts',
        'manage_options',
        'auto-alt-text-to-posts',
        'aatp_settings_page_html'
    );
}
add_action('admin_menu', 'aatp_settings_page');

// Settings page HTML
function aatp_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['aatp_logging_enabled']) || isset($_POST['aatp_post_limit']) || isset($_POST['aatp_post_select']) || isset($_POST['aatp_allowed_post_types']) || isset($_POST['clear_log']) || isset($_POST['download_log']) || isset($_POST['update_alt_text'])) {
        check_admin_referer('aatp_settings');
        if (isset($_POST['aatp_logging_enabled'])) {
            update_option('aatp_logging_enabled', isset($_POST['aatp_logging_enabled']) ? '1' : '0');
            echo '<div class="updated"><p>Logging settings saved.</p></div>';
        }
        if (isset($_POST['aatp_post_limit'])) {
            update_option('aatp_post_limit', sanitize_text_field($_POST['aatp_post_limit']));
            echo '<div class="updated"><p>Post limit saved.</p></div>';
        }
        if (isset($_POST['aatp_post_select'])) {
            update_option('aatp_post_select', sanitize_text_field($_POST['aatp_post_select']));
            echo '<div class="updated"><p>Post select saved.</p></div>';
        }
        if (isset($_POST['aatp_allowed_post_types'])) {
            update_option('aatp_allowed_post_types', sanitize_text_field($_POST['aatp_allowed_post_types']));
            echo '<div class="updated"><p>Allowed post types saved.</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Auto Alt Text to Posts Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('aatp_settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Logging</th>
                    <td>
                        <input type="checkbox" name="aatp_logging_enabled" value="1" <?php checked(1, get_option('aatp_logging_enabled'), true); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Post Limit (comma-separated IDs)</th>
                    <td>
                        <input type="text" name="aatp_post_limit" value="<?php echo esc_attr(get_option('aatp_post_limit')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Select Specific Post</th>
                    <td>
                        <input type="text" name="aatp_post_select" value="<?php echo esc_attr(get_option('aatp_post_select')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Allowed Post Types (comma-separated)</th>
                    <td>
                        <input type="text" name="aatp_allowed_post_types" value="<?php echo esc_attr(get_option('aatp_allowed_post_types', 'capabilities,consulting-services')); ?>" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <form method="post" action="">
            <?php wp_nonce_field('aatp_settings'); ?>
            <input type="hidden" name="clear_log" value="1" />
            <?php submit_button('Clear Log'); ?>
        </form>
        <form method="post" action="">
            <?php wp_nonce_field('aatp_settings'); ?>
            <input type="hidden" name="download_log" value="1" />
            <?php submit_button('Download Log'); ?>
        </form>
        <form method="post" action="">
            <?php wp_nonce_field('aatp_settings'); ?>
            <input type="hidden" name="update_alt_text" value="1" />
            <?php submit_button('Update Alt Text'); ?>
        </form>
        <h2>Log Content</h2>
        <div id="aatp-log-content" style="background-color: #f5f5f5; padding: 10px; border: 1px solid #ccc; max-height: 300px; overflow-y: scroll;">
            <p>Loading log content...</p>
        </div>
    </div>
    <script>
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
                setInterval(fetchLogContent, 15000); // Update log content every 15 seconds
            });
        })(jQuery);
    </script>
    <?php
}
?>
