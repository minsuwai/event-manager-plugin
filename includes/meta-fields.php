<?php
function em_register_custom_meta()
{
    register_post_meta('event', 'event_date', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    register_post_meta('event', 'event_location', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ]);
}
add_action('init', 'em_register_custom_meta');
