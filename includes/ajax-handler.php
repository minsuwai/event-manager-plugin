<?php
add_action('wp_ajax_submit_event', 'em_handle_event_submission');
add_action('wp_ajax_nopriv_submit_event', 'em_handle_event_submission');

function em_handle_event_submission()
{
    $title = sanitize_text_field($_POST['event_title']);
    $content = sanitize_textarea_field($_POST['event_content']);
    $event_date = sanitize_text_field($_POST['event_date']);
    $event_location = sanitize_text_field($_POST['event_location']);

    $post_id = wp_insert_post([
        'post_type' => 'event',
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'pending',
    ]);

    if ($post_id) {
        update_post_meta($post_id, 'event_date', $event_date);
        update_post_meta($post_id, 'event_location', $event_location);
        wp_send_json_success('Event submitted and awaiting approval.');
    } else {
        wp_send_json_error('Failed to submit event.');
    }

    wp_die();
}
