<?php

function em_handle_event_submission()
{
    $title = sanitize_text_field($_POST['title']);
    $desc = sanitize_textarea_field($_POST['description']);
    $date = sanitize_text_field($_POST['date']);
    $location = sanitize_text_field($_POST['location']);

    $event_id = wp_insert_post([
        'post_type' => 'event',
        'post_title' => $title,
        'post_content' => $desc,
        'post_status' => 'publish',
    ]);

    if ($event_id) {
        update_post_meta($event_id, 'event_date', $date);
        update_post_meta($event_id, 'event_location', $location);
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_em_submit_event', 'em_handle_event_submission');
add_action('wp_ajax_nopriv_em_submit_event', 'em_handle_event_submission');
