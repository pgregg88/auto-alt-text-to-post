<?php
// Handle form submission for updating alt text
add_action('admin_post_aatp_update_alt_text', 'aatp_update_alt_text');

function aatp_update_alt_text() {
    check_admin_referer('aatp_settings');
    $post_limit = get_option('aatp_post_limit');
    $selected_post_id = get_option('aatp_post_select');
    $allowed_post_types = get_option('aatp_allowed_post_types', 'capabilities,consulting-services');
    $allowed_post_types_array = explode(',', $allowed_post_types);

    $args = [
        'post_type' => $allowed_post_types_array,
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ];

    if (!empty($post_limit)) {
        $args['post__in'] = explode(',', $post_limit);
    }

    if (!empty($selected_post_id)) {
        $args['post__in'] = [$selected_post_id];
    }

    $offset = 0;
    $batch_size = 100; // Adjust batch size as needed

    do {
        $args['posts_per_page'] = $batch_size;
        $args['offset'] = $offset;
        $posts = get_posts($args);

        foreach ($posts as $post) {
            $post_id = $post->ID;

            // Update alt text for each image in the post content
            if (has_post_thumbnail($post_id)) {
                $thumbnail_id = get_post_thumbnail_id($post_id);
                $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                if (empty($alt_text)) {
                    $new_alt_text = aatp_generate_alt_text($post); // Use custom function to generate alt text
                    update_post_meta($thumbnail_id, '_wp_attachment_image_alt', $new_alt_text);
                    aatp_log("Updated alt text for thumbnail of post {$post_id}");
                }
            }

            // Process images in the post content
            $content = $post->post_content;
            preg_match_all('/<img[^>]+>/i', $content, $matches);
            foreach ($matches[0] as $img_tag) {
                preg_match('/wp-image-([0-9]+)/i', $img_tag, $img_id_match);
                if (isset($img_id_match[1])) {
                    $img_id = $img_id_match[1];
                    $alt_text = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                    if (empty($alt_text)) {
                        $new_alt_text = aatp_generate_alt_text($post); // Use custom function to generate alt text
                        update_post_meta($img_id, '_wp_attachment_image_alt', $new_alt_text);
                        aatp_log("Updated alt text for image {$img_id} in post {$post_id}");
                    }
                }
            }
        }

        $offset += $batch_size;
    } while (count($posts) > 0);

    wp_redirect(admin_url('options-general.php?page=auto-alt-text-to-posts'));
    exit;
}

// Function to generate alt text based on post title or content
function aatp_generate_alt_text($post) {
    if (!empty($post->post_title)) {
        return esc_attr($post->post_title);
    }
    return 'Default alt text';
}
?>
