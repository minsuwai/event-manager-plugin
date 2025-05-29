<?php
// Add custom meta box to Event post type
function em_add_event_meta_boxes()
{
    add_meta_box(
        'em_event_details',
        'Event Details',
        'em_render_event_meta_box',
        'event',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'em_add_event_meta_boxes');

// Render the meta box
function em_render_event_meta_box($post)
{
    $event_date = get_post_meta($post->ID, 'event_date', true);
    $event_location = get_post_meta($post->ID, 'event_location', true);
    wp_nonce_field('em_save_event_meta', 'em_event_nonce');
?>
    <p>
        <label for="event_date"><strong>Date:</strong></label><br />
        <input type="date" id="event_date" name="event_date" value="<?php echo esc_attr($event_date); ?>" />
    </p>
    <p>
        <label for="event_location"><strong>Location:</strong></label><br />
        <input type="text" id="event_location" name="event_location" value="<?php echo esc_attr($event_location); ?>" />
    </p>
<?php
}

// Save the data
function em_save_event_meta($post_id)
{
    if (!isset($_POST['em_event_nonce']) || !wp_verify_nonce($_POST['em_event_nonce'], 'em_save_event_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if ('event' !== $_POST['post_type']) return;

    if (isset($_POST['event_date'])) {
        update_post_meta($post_id, 'event_date', sanitize_text_field($_POST['event_date']));
    }
    if (isset($_POST['event_location'])) {
        update_post_meta($post_id, 'event_location', sanitize_text_field($_POST['event_location']));
    }
}
add_action('save_post', 'em_save_event_meta');
