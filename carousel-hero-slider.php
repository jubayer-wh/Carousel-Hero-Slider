<?php
/**
 * Plugin Name: Carousel Hero Slider
 * Plugin URI: https://www.webkih.com/
 * Description: SEO-friendly WordPress carousel and hero block slider plugin to create responsive image sliders, hero carousels, and rotating banner sections using a simple shortcode.
 * Version: 1.0.0
 * Author: Jubayer Hossain
 * Author URI: https://www.webkih.com/about/
 * License: GPLv2 or later
 * Text Domain: carousel-hero-slider
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CS_VER', '1.0.0' );
define( 'CS_DIR', plugin_dir_path( __FILE__ ) );
define( 'CS_URL', plugin_dir_url( __FILE__ ) );

require_once CS_DIR . 'includes/wbk-hero-slider.php';
require_once CS_DIR . 'admin/settings-page.php';
