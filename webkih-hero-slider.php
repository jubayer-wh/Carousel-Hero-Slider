<?php
/**
 * Plugin Name: WebKih Hero Slider
 * Plugin URI: https://github.com/jubayer-wh/Carousel-Hero-Slider/
 * Description: SEO-friendly WordPress carousel and hero block slider plugin to create responsive image sliders, hero carousels, and rotating banner sections using a simple shortcode.
 * Version: 1.0.0
 * Author: Jubayer Hossain
 * Author URI: https://www.webkih.com/about/
 * License: GPLv2 or later
 * Text Domain: webkih-hero-slider
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WKHS_VER', '1.0.0' );
define( 'WKHS_DIR', plugin_dir_path( __FILE__ ) );
define( 'WKHS_URL', plugin_dir_url( __FILE__ ) );

require_once WKHS_DIR . 'includes/webkih-hero-slider.php';
require_once WKHS_DIR . 'admin/settings-page.php';
