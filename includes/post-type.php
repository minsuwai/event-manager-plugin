<?php
function em_register_event_post_type()
{
    register_post_type('event', [
        'labels' => [
            'name' => 'Events',
            'singular_name' => 'Event',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'custom-fields'],
    ]);
}
add_action('init', 'em_register_event_post_type');
