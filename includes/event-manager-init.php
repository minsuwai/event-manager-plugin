<?php
// Register the Events custom post type
function em_register_event_post_type()
{
    $labels = [
        'name' => 'Events',
        'singular_name' => 'Event',
        'add_new' => 'Add New Event',
        'edit_item' => 'Edit Event',
        'new_item' => 'New Event',
        'view_item' => 'View Event',
        'all_items' => 'All Events',
        'menu_name' => 'Events',
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'events'],
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-calendar-alt',
    ];

    register_post_type('event', $args);
}
add_action('init', 'em_register_event_post_type');

require_once plugin_dir_path(__FILE__) . 'event-shortcode.php';
