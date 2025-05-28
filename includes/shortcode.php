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
