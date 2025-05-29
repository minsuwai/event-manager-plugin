<?php
function em_event_submission_form()
{
    if (!is_user_logged_in()) {
        return '<p class="text-red-500">You must be logged in to submit or edit an event.</p>';
    }

    $current_user_id = get_current_user_id();
    $edit_event_id = isset($_GET['edit_event_id']) ? intval($_GET['edit_event_id']) : 0;

    // Initialize empty/default values
    $title = '';
    $content = '';
    $event_date = '';
    $event_location = '';

    // If editing, load existing event and check permission
    if ($edit_event_id) {
        $post = get_post($edit_event_id);

        if (!$post || $post->post_type !== 'event' || $post->post_author != $current_user_id) {
            return '<p class="text-red-500">You do not have permission to edit this event.</p>';
        }

        // Prefill values
        $title = $post->post_title;
        $content = $post->post_content;
        $event_date = get_post_meta($edit_event_id, 'event_date', true);
        $event_location = get_post_meta($edit_event_id, 'event_location', true);
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['em_event_nonce']) && wp_verify_nonce($_POST['em_event_nonce'], 'em_event_submit_action')) {

        // Sanitize inputs
        $new_title = sanitize_text_field($_POST['event_title'] ?? '');
        $new_content = sanitize_textarea_field($_POST['event_content'] ?? '');
        $new_date = sanitize_text_field($_POST['event_date'] ?? '');
        $new_location = sanitize_text_field($_POST['event_location'] ?? '');

        if (empty($new_title)) {
            echo '<p class="text-red-500">Title is required.</p>';
        } else {
            if ($edit_event_id) {
                // Update existing post
                $updated_post = [
                    'ID'           => $edit_event_id,
                    'post_title'   => $new_title,
                    'post_content' => $new_content,
                    'post_status'  => 'pending',
                ];
                wp_update_post($updated_post);

                // Update meta
                update_post_meta($edit_event_id, 'event_date', $new_date);
                update_post_meta($edit_event_id, 'event_location', $new_location);

                echo '<p class="text-green-600">Event updated successfully.</p>';
            } else {
                // Create new post
                $new_post = [
                    'post_title'   => $new_title,
                    'post_content' => $new_content,
                    'post_type'    => 'event',
                    'post_status'  => 'pending',
                    'post_author'  => $current_user_id,
                ];
                $post_id = wp_insert_post($new_post);

                if ($post_id) {
                    update_post_meta($post_id, 'event_date', $new_date);
                    update_post_meta($post_id, 'event_location', $new_location);
                    echo '<p class="text-green-600">Event submitted successfully.</p>';
                } else {
                    echo '<p class="text-red-500">There was an error submitting your event.</p>';
                }
            }
        }
    }

    ob_start();
?>
    <form method="post" class="max-w-lg mx-auto p-6 bg-white rounded shadow-md space-y-4 mb-5">
        <?php wp_nonce_field('em_event_submit_action', 'em_event_nonce'); ?>
        <label class="block">
            <span class="font-semibold">Event Title:</span>
            <input type="text" name="event_title" value="<?php echo esc_attr($title); ?>" required
                class="w-full border rounded px-3 py-2" />
        </label>
        <label class="block">
            <span class="font-semibold">Event Description:</span>
            <textarea name="event_content" rows="4" class="w-full border rounded px-3 py-2"><?php echo esc_textarea($content); ?></textarea>
        </label>
        <label class="block">
            <span class="font-semibold">Event Date:</span>
            <input type="date" name="event_date" value="<?php echo esc_attr($event_date); ?>"
                class="w-full border rounded px-3 py-2" />
        </label>
        <label class="block">
            <span class="font-semibold">Event Location:</span>
            <input type="text" name="event_location" value="<?php echo esc_attr($event_location); ?>"
                class="w-full border rounded px-3 py-2" />
        </label>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
            <?php echo $edit_event_id ? 'Update Event' : 'Submit Event'; ?>
        </button>
    </form>
<?php
    return ob_get_clean();
}
add_shortcode('event_form', 'em_event_submission_form');

// Handle event deletion
function em_handle_event_deletion()
{
    if (
        isset($_GET['delete_event_id'], $_GET['delete_nonce']) &&
        is_user_logged_in()
    ) {
        $event_id = intval($_GET['delete_event_id']);
        $nonce = sanitize_text_field($_GET['delete_nonce']);

        if (!wp_verify_nonce($nonce, 'delete_event_' . $event_id)) {
            echo '<p class="text-red-600">Invalid request. Nonce failed.</p>';
            return;
        }

        $post = get_post($event_id);
        if (!$post || $post->post_type !== 'event') {
            echo '<p class="text-red-600">Event not found.</p>';
            return;
        }

        if ((int)$post->post_author !== get_current_user_id()) {
            echo '<p class="text-red-600">You are not allowed to delete this event.</p>';
            return;
        }

        wp_delete_post($event_id, true); // true = force delete
        // echo '<p class="text-green-600">Event deleted successfully.</p>';
        wp_redirect(add_query_arg([
            'em_message' => urlencode('Event deleted successfully.'),
            'em_type'    => 'success'
        ], remove_query_arg(['delete_event_id', 'delete_nonce'])));
        exit;
    }
}
add_action('template_redirect', 'em_handle_event_deletion');
