<?php

function em_display_events()
{
    ob_start();

    $args = [
        'post_type' => 'event',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'ASC',
    ];
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
        while ($query->have_posts()) {
            $query->the_post();

            $event_date = get_post_meta(get_the_ID(), 'event_date', true);
            $event_location = get_post_meta(get_the_ID(), 'event_location', true);
?>
            <div class="p-6 bg-white shadow-md rounded-lg border hover:shadow-lg transition-all">
                <h2 class="text-xl font-bold mb-2"><?php the_title(); ?></h2>

                <?php if ($event_date) : ?>
                    <div class="text-sm text-gray-500 mb-1">📅 <?php echo esc_html($event_date); ?></div>
                <?php endif; ?>

                <?php if ($event_location) : ?>
                    <div class="text-sm text-gray-600 mb-3">📍 <?php echo esc_html($event_location); ?></div>
                <?php endif; ?>

                <div class="mb-4"><?php the_excerpt(); ?></div>
                <a href="<?php the_permalink(); ?>" class="button">Read More</a>
            </div>
        <?php
        }

        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p class="text-gray-500">No events found.</p>';
    }

    return ob_get_clean();
}
add_shortcode('event_manager', 'em_display_events');

function em_user_event_list()
{
    if (!is_user_logged_in()) {
        return '<p class="text-red-500">Please log in to view your submitted events.</p>';
    }

    $current_user_id = get_current_user_id();

    $args = [
        'post_type'      => 'event',
        'post_status'    => ['publish', 'pending', 'draft'],
        'posts_per_page' => -1,
        'author'         => $current_user_id,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    $query = new WP_Query($args);

    ob_start();

    if (isset($_GET['em_message']) && !empty($_GET['em_message'])) {
        $msg   = esc_js(urldecode($_GET['em_message']));
        $type  = esc_js($_GET['em_type'] ?? 'info'); // success, error, info, warning

        echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: "' . $type . '",
                    title: "' . ($type === 'success' ? 'Success' : 'Notice') . '",
                    text: "' . $msg . '",
                    confirmButtonColor: "#3085d6"
                });
            } else {
                alert("' . $msg . '");
            }
        });
    </script>';
    }


    if ($query->have_posts()) {
        echo '<div class="space-y-4">';
        while ($query->have_posts()) {
            $query->the_post();
            $event_date = get_post_meta(get_the_ID(), 'event_date', true);
            $event_location = get_post_meta(get_the_ID(), 'event_location', true);
        ?>
            <div class="p-4 bg-gray-100 border rounded shadow-sm">
                <h3 class="text-lg font-bold"><?php the_title(); ?></h3>
                <p class="text-sm text-gray-600"><?php echo esc_html($event_date); ?> | <?php echo esc_html($event_location); ?></p>
                <?php
                $status = get_post_status();
                $color_class = match ($status) {
                    'publish' => 'text-green-600',
                    'pending' => 'text-red-600',
                    'draft'   => 'text-yellow-600',
                    default   => 'text-gray-500',
                };
                ?>
                <p class="text-sm">
                    Status:
                    <span class="text-sm <?php echo esc_attr($color_class); ?> mb-2">
                        <?php echo esc_html(ucfirst($status)); ?>
                    </span>
                </p>

                <div class="flex gap-2 mt-2">
                    <a href="<?php echo esc_url(add_query_arg('edit_event_id', get_the_ID())); ?>" class="px-4 py-2 bg-blue-600 text-white rounded hover:underline">Edit</a>
                    <?php
                    $event_id = get_the_ID(); // ✅ Fix here
                    $delete_nonce = wp_create_nonce('delete_event_' . $event_id);
                    $delete_url = add_query_arg([
                        'delete_event_id' => $event_id,
                        'delete_nonce' => $delete_nonce,
                    ]);
                    ?>
                    <a href="<?php echo esc_url($delete_url); ?>" class="px-4 py-2 bg-red-600 text-white rounded hover:underline" onclick="return confirm('Are you sure you want to delete this event?')">
                        Delete
                    </a>
                </div>

            </div>
<?php
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p class="text-gray-500">You have not submitted any events yet.</p>';
    }

    return ob_get_clean();
}
add_shortcode('my_events', 'em_user_event_list');
