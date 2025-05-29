<?php

/**
 * Plugin Name: Event Manager
 * Description: A custom plugin to manage and display events.
 * Version: 1.0
 * Author: Minsuwai
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Register custom post type
require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';

// Register shortcode
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';

// Register custom fields
require_once plugin_dir_path(__FILE__) . 'includes/meta-fields.php';

require_once plugin_dir_path(__FILE__) . 'includes/event-form.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handler.php';
require_once plugin_dir_path(__FILE__) . 'event-manager-admin.php';

// Enqueue Tailwind CSS and Alpine.js
function em_enqueue_assets()
{
    wp_enqueue_style('tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
    wp_enqueue_script('alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', [], null, true);

    // Include jQuery dependency if not already added
    wp_enqueue_script('jquery');

    // Custom AJAX form script
    wp_enqueue_script('em-event-form', plugin_dir_url(__FILE__) . 'assets/js/event-form.js', ['jquery'], null, true);

    wp_localize_script('em-event-form', 'em_ajax_obj', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}
add_action('wp_enqueue_scripts', 'em_enqueue_assets');
