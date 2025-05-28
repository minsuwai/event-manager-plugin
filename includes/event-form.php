<?php

function em_event_submission_form()
{
    if (!is_user_logged_in()) {
        return '<p class="text-red-500">You must be logged in to submit an event.</p>';
    }

    ob_start(); ?>

    <form method="post" class="space-y-4 max-w-xl mx-auto p-6 bg-white shadow-md rounded" x-data>
        <?php wp_nonce_field('em_event_submit_action', 'em_event_nonce'); ?>

        <div>
            <label class="block font-semibold mb-1">Event Title</label>
            <input type="text" name="em_title" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Description</label>
            <textarea name="em_content" class="w-full border rounded px-3 py-2" rows="4" required></textarea>
        </div>

        <div>
            <label class="block font-semibold mb-1">Event Date</label>
            <input type="date" name="em_date" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Location</label>
            <input type="text" name="em_location" class="w-full border rounded px-3 py-2" required>
        </div>

        <button type="submit" name="em_submit_event" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Submit Event
        </button>
    </form>

<?php return ob_get_clean();
}
add_shortcode('event_form', 'em_event_submission_form');
