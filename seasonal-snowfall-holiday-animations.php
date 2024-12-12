<?php
/*
Plugin Name: Seasonal Snowfall & Holiday Animations
Plugin URI:  https://uyimoses.com/seasonal-snowfall-holiday-animations
Description: Adds festive snowfall and holiday animations to your WordPress site with configurable settings.
Version:     1.3.1
Author:      Uyi Moses
Author URI:  https://uyimoses.com
License:     GPL2
Text Domain: seasonal-snowfall-holiday-animations
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('SSHA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SSHA_PLUGIN_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, 'ssha_activate_plugin');
function ssha_activate_plugin() {
    $default = array(
        'enable_snow'        => 1,
        'animation_type'     => 'snowfall',
        'snowflake_count'    => 100,
        'snowflake_speed'    => 1,
        'enable_cursor'      => 0
    );
    update_option('ssha_settings', $default);
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ssha_action_links');
function ssha_action_links($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=ssha_settings') . '">' . esc_html__('Settings', 'seasonal-snowfall-holiday-animations') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

add_action('admin_menu', 'ssha_add_admin_menu');
function ssha_add_admin_menu() {
    add_options_page(
        esc_html__('Seasonal Snowfall & Holiday Animations', 'seasonal-snowfall-holiday-animations'),
        esc_html__('Snowfall & Holiday', 'seasonal-snowfall-holiday-animations'),
        'manage_options',
        'ssha_settings',
        'ssha_options_page'
    );
}

add_action('admin_init', 'ssha_settings_init');
function ssha_settings_init() {
    register_setting('ssha_settings_group', 'ssha_settings');
    add_settings_section('ssha_section', esc_html__('Animation Settings', 'seasonal-snowfall-holiday-animations'), 'ssha_section_cb', 'ssha_settings_group');

    add_settings_field('ssha_enable_snow', esc_html__('Enable Holiday Animations', 'seasonal-snowfall-holiday-animations'), 'ssha_enable_snow_render', 'ssha_settings_group', 'ssha_section');
    add_settings_field('ssha_animation_type', esc_html__('Animation Type', 'seasonal-snowfall-holiday-animations'), 'ssha_animation_type_render', 'ssha_settings_group', 'ssha_section');
    add_settings_field('ssha_snowflake_count', esc_html__('Snowflake/Light Count', 'seasonal-snowfall-holiday-animations'), 'ssha_snowflake_count_render', 'ssha_settings_group', 'ssha_section');
    add_settings_field('ssha_snowflake_speed', esc_html__('Animation Speed', 'seasonal-snowfall-holiday-animations'), 'ssha_snowflake_speed_render', 'ssha_settings_group', 'ssha_section');
    add_settings_field('ssha_enable_cursor', esc_html__('Festive Cursor', 'seasonal-snowfall-holiday-animations'), 'ssha_enable_cursor_render', 'ssha_settings_group', 'ssha_section');
}

function ssha_section_cb() {
    echo '<p>' . esc_html__('Configure your holiday animations and effects.', 'seasonal-snowfall-holiday-animations') . '</p>';
}

function ssha_enable_snow_render() {
    $options = get_option('ssha_settings');
    echo '<input type="checkbox" name="ssha_settings[enable_snow]" ' . checked(1, $options['enable_snow'], false) . ' value="1">';
    echo '<p class="description">' . esc_html__('Enable or disable the holiday animations.', 'seasonal-snowfall-holiday-animations') . '</p>';
}

function ssha_animation_type_render() {
    $options = get_option('ssha_settings');
    $types = array('snowfall' => 'Snowfall', 'twinkle' => 'Twinkle Lights');
    echo '<select name="ssha_settings[animation_type]">';
    foreach ($types as $key => $label) {
        echo '<option value="' . esc_attr($key) . '" ' . selected($options['animation_type'], $key, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
    echo '<p class="description">' . esc_html__('Select the type of holiday animation.', 'seasonal-snowfall-holiday-animations') . '</p>';
}

function ssha_snowflake_count_render() {
    $options = get_option('ssha_settings');
    echo '<input type="number" name="ssha_settings[snowflake_count]" value="' . esc_attr($options['snowflake_count']) . '" min="1" max="1000">';
    echo '<p class="description">' . esc_html__('Number of snowflakes or twinkle lights. Default is 100.', 'seasonal-snowfall-holiday-animations') . '</p>';
}

function ssha_snowflake_speed_render() {
    $options = get_option('ssha_settings');
    echo '<input type="number" step="0.1" name="ssha_settings[snowflake_speed]" value="' . esc_attr($options['snowflake_speed']) . '" min="0.1" max="10">';
    echo '<p class="description">' . esc_html__('Adjust how fast the animation elements move.', 'seasonal-snowfall-holiday-animations') . '</p>';
}

function ssha_enable_cursor_render() {
    $options = get_option('ssha_settings');
    echo '<input type="checkbox" name="ssha_settings[enable_cursor]" ' . checked(1, $options['enable_cursor'], false) . ' value="1">';
    echo '<p class="description">' . esc_html__('Check to use a festive holiday cursor.', 'seasonal-snowfall-holiday-animations') . '</p>';
}

function ssha_options_page() {
    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Seasonal Snowfall & Holiday Animations Settings', 'seasonal-snowfall-holiday-animations') . '</h1>';
    echo '<form action="options.php" method="post">';
    settings_fields('ssha_settings_group');
    do_settings_sections('ssha_settings_group');
    submit_button();
    echo '</form>';
    echo '</div>';
}

add_action('wp_enqueue_scripts', 'ssha_enqueue_scripts');
function ssha_enqueue_scripts() {
    $options = get_option('ssha_settings');
    if (!empty($options['enable_snow']) && $options['enable_snow'] == 1) {
        wp_enqueue_script('ssha_effects', SSHA_PLUGIN_URL . 'js/effects.js', array(), '1.0.0', true);
        wp_localize_script('ssha_effects', 'sshaSettings', array(
            'animationType' => $options['animation_type'],
            'count'         => intval($options['snowflake_count']),
            'speed'         => floatval($options['snowflake_speed'])
        ));
        wp_enqueue_style('ssha_styles', SSHA_PLUGIN_URL . 'css/style.css', array(), '1.0.0');
        if (!empty($options['enable_cursor']) && $options['enable_cursor'] == 1) {
            add_filter('body_class', 'ssha_add_cursor_class');
        }
    }
}

function ssha_add_cursor_class($classes) {
    $classes[] = 'ssha-festive-cursor';
    return $classes;
}
