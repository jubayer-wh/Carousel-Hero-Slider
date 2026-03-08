<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Hero Slide custom post type.
 */
function cs_register_hero_slide_post_type() {
    $labels = [
        'name'               => __( 'Hero Slides', 'carousel-hero-slider' ),
        'singular_name'      => __( 'Hero Slide', 'carousel-hero-slider' ),
        'add_new'            => __( 'Add New Slide', 'carousel-hero-slider' ),
        'add_new_item'       => __( 'Add New Hero Slide', 'carousel-hero-slider' ),
        'edit_item'          => __( 'Edit Hero Slide', 'carousel-hero-slider' ),
        'new_item'           => __( 'New Hero Slide', 'carousel-hero-slider' ),
        'view_item'          => __( 'View Hero Slide', 'carousel-hero-slider' ),
        'search_items'       => __( 'Search Hero Slides', 'carousel-hero-slider' ),
        'not_found'          => __( 'No hero slides found.', 'carousel-hero-slider' ),
        'not_found_in_trash' => __( 'No hero slides found in Trash.', 'carousel-hero-slider' ),
        'menu_name'          => __( 'Hero Slides', 'carousel-hero-slider' ),
    ];

    register_post_type(
        'cs_hero_slide',
        [
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => 'carousel-hero-slider',
            'menu_position'       => null,
            'supports'            => [ 'title', 'thumbnail', 'page-attributes' ],
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'exclude_from_search' => true,
            'rewrite'             => false,
            'query_var'           => false,
        ]
    );
}
add_action( 'init', 'cs_register_hero_slide_post_type' );

/**
 * Register hero slider shortcode.
 */
function cs_register_wbk_hero_slider_shortcode() {
    add_shortcode( 'wbk_hero_slider', 'cs_render_wbk_hero_slider_shortcode' );
}
add_action( 'init', 'cs_register_wbk_hero_slider_shortcode' );

/**
 * Render hero slider output.
 *
 * @param array<string, string> $atts Shortcode attributes.
 */
function cs_render_wbk_hero_slider_shortcode( $atts ) {
    $defaults = cs_get_wbk_hero_slider_settings();

    $atts = shortcode_atts(
        [
            'height' => (string) $defaults['height'],
            'speed'  => (string) $defaults['speed'],
        ],
        $atts,
        'wbk_hero_slider'
    );

    $height = max( 220, absint( $atts['height'] ) );
    $speed  = max( 2000, absint( $atts['speed'] ) );
    $slides = cs_get_hero_slides();

    if ( empty( $slides ) ) {
        return '<p><strong>Carousel Slider:</strong> Create at least one Hero Slide in the admin panel.</p>';
    }

    wp_enqueue_style( 'cs-wbk-hero-slider-css', CS_URL . 'assets/css/wbk-hero-slider.css', [], CS_VER );
    wp_enqueue_script( 'cs-wbk-hero-slider-js', CS_URL . 'assets/js/wbk-hero-slider.js', [], CS_VER, true );

    ob_start();
    ?>
    <div class="cs-wbk-hero-slider" data-speed="<?php echo esc_attr( $speed ); ?>" style="--cs-slider-height:<?php echo esc_attr( $height ); ?>px;">
        <?php foreach ( $slides as $index => $slide ) : ?>
            <article class="cs-wbk-hero-slide<?php echo 0 === $index ? ' is-active' : ''; ?>" aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>">
                <div class="cs-wbk-hero-slide-bg" style="background-image:url('<?php echo esc_url( $slide['image'] ); ?>');"></div>
                <div class="cs-wbk-hero-slide-overlay">
                    <h2 class="cs-wbk-hero-slide-title"><?php echo esc_html( $slide['title'] ); ?></h2>
                    <?php if ( ! empty( $slide['caption'] ) ) : ?>
                        <p class="cs-wbk-hero-slide-caption"><?php echo esc_html( $slide['caption'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( ! empty( $slide['button_label'] ) && ! empty( $slide['button_link'] ) ) : ?>
                        <a class="cs-wbk-hero-slide-button" href="<?php echo esc_url( $slide['button_link'] ); ?>">
                            <?php echo esc_html( $slide['button_label'] ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php

    return ob_get_clean();
}

/**
 * Query slide data from custom post type.
 *
 * @return array<int, array<string, string>>
 */
function cs_get_hero_slides() {
    $query = new WP_Query(
        [
            'post_type'      => 'cs_hero_slide',
            'posts_per_page' => -1,
            'orderby'        => [
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ],
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ]
    );

    if ( ! $query->have_posts() ) {
        return [];
    }

    $slides = [];

    foreach ( $query->posts as $post ) {
        $image = get_the_post_thumbnail_url( $post->ID, 'full' );

        if ( empty( $image ) ) {
            continue;
        }

        $slides[] = [
            'title'        => get_the_title( $post->ID ),
            'caption'      => (string) get_post_meta( $post->ID, '_cs_slide_caption', true ),
            'button_label' => (string) get_post_meta( $post->ID, '_cs_slide_button_label', true ),
            'button_link'  => (string) get_post_meta( $post->ID, '_cs_slide_button_link', true ),
            'image'        => $image,
        ];
    }

    wp_reset_postdata();

    return $slides;
}

/**
 * Read hero slider settings.
 *
 * @return array<string, mixed>
 */
function cs_get_wbk_hero_slider_settings() {
    $defaults = [
        'height' => 460,
        'speed'  => 4500,
    ];

    $settings = get_option( 'cs_wbk_hero_slider_settings', [] );

    if ( ! is_array( $settings ) ) {
        return $defaults;
    }

    return wp_parse_args( $settings, $defaults );
}
