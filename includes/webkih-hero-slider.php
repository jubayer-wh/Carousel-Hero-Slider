<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Hero Slide custom post type.
 */
function wkhs_register_hero_slide_post_type() {
    $labels = [
        'name'               => __( 'Hero Slides', 'webkih-hero-slider' ),
        'singular_name'      => __( 'Hero Slide', 'webkih-hero-slider' ),
        'add_new'            => __( 'Add New Slide', 'webkih-hero-slider' ),
        'add_new_item'       => __( 'Add New Hero Slide', 'webkih-hero-slider' ),
        'edit_item'          => __( 'Edit Hero Slide', 'webkih-hero-slider' ),
        'new_item'           => __( 'New Hero Slide', 'webkih-hero-slider' ),
        'view_item'          => __( 'View Hero Slide', 'webkih-hero-slider' ),
        'search_items'       => __( 'Search Hero Slides', 'webkih-hero-slider' ),
        'not_found'          => __( 'No hero slides found.', 'webkih-hero-slider' ),
        'not_found_in_trash' => __( 'No hero slides found in Trash.', 'webkih-hero-slider' ),
        'menu_name'          => __( 'Hero Slides', 'webkih-hero-slider' ),
    ];

    register_post_type(
        'wkhs_hero_slide',
        [
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
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
add_action( 'init', 'wkhs_register_hero_slide_post_type' );

/**
 * Register hero slider shortcode.
 */
function wkhs_register_webkih_hero_slider_shortcode() {
    add_shortcode( 'webkih_hero_slider', 'wkhs_render_webkih_hero_slider_shortcode' );
}
add_action( 'init', 'wkhs_register_webkih_hero_slider_shortcode' );

/**
 * Render hero slider output.
 *
 * @param array<string, string> $atts Shortcode attributes.
 */
function wkhs_render_webkih_hero_slider_shortcode( $atts ) {
    $defaults = wkhs_get_webkih_hero_slider_settings();
    $animation_styles = wkhs_get_webkih_hero_slider_animation_styles();

    $atts = shortcode_atts(
        [
            'height' => (string) $defaults['height'],
            'timer'  => (string) $defaults['timer'],
            'speed'  => '',
        ],
        $atts,
        'webkih_hero_slider'
    );

    $height      = max( 220, absint( $atts['height'] ) );
    $timer_value = '' !== $atts['timer'] ? $atts['timer'] : $atts['speed'];
    $timer       = max( 2000, absint( $timer_value ) );

    $show_title    = ! empty( $defaults['show_title'] );
    $show_caption  = ! empty( $defaults['show_caption'] );
    $show_button    = ! empty( $defaults['show_button'] );
    $center_content = ! empty( $defaults['center_content'] );
    $enable_arrows  = ! empty( $defaults['enable_arrows'] );
    $text_color     = sanitize_hex_color( (string) ( $defaults['text_color'] ?? '' ) );
    $button_bg_color = sanitize_hex_color( (string) ( $defaults['button_bg_color'] ?? '' ) );

    if ( ! $text_color ) {
        $text_color = '#ffffff';
    }

    if ( ! $button_bg_color ) {
        $button_bg_color = '#ffffff';
    }
    $animation     = isset( $animation_styles[ $defaults['animation_style'] ] ) ? $defaults['animation_style'] : 'animation_12';
    $arrow_style   = in_array( $defaults['arrow_style'], [ 'side', 'bottom_right', 'bottom_rounded' ], true ) ? $defaults['arrow_style'] : 'bottom_rounded';
    $mobile_image_behavior = in_array( $defaults['mobile_image_behavior'], [ 'cover', 'contain', 'no-repeat', 'no_repeat' ], true ) ? $defaults['mobile_image_behavior'] : 'cover';

    if ( 'no_repeat' === $mobile_image_behavior ) {
        $mobile_image_behavior = 'no-repeat';
    }
    $slides        = wkhs_get_hero_slides();

    if ( empty( $slides ) ) {
        return '<p><strong>Carousel Slider:</strong> Create at least one Hero Slide in the admin panel.</p>';
    }

    wp_enqueue_style( 'cs-webkih-hero-slider-css', WKHS_URL . 'assets/css/webkih-hero-slider.css', [], WKHS_VER );
    wp_enqueue_script( 'cs-webkih-hero-slider-js', WKHS_URL . 'assets/js/webkih-hero-slider.js', [], WKHS_VER, true );

    ob_start();
    ?>
    <div class="cs-webkih-hero-slider cs-animation-<?php echo esc_attr( $animation ); ?> cs-nav-style-<?php echo esc_attr( $arrow_style ); ?> cs-mobile-image-<?php echo esc_attr( $mobile_image_behavior ); ?><?php echo $center_content ? ' cs-content-align-center' : ''; ?>" data-timer="<?php echo esc_attr( $timer ); ?>" data-animation="<?php echo esc_attr( $animation ); ?>" style="--cs-slider-height:<?php echo esc_attr( $height ); ?>px;--cs-slider-text-color:<?php echo esc_attr( $text_color ); ?>;--cs-slider-button-bg:<?php echo esc_attr( $button_bg_color ); ?>;">
        <?php if ( $enable_arrows && count( $slides ) > 1 ) : ?>
            <div class="cs-webkih-hero-slider-nav" aria-label="<?php esc_attr_e( 'Slide navigation', 'webkih-hero-slider' ); ?>">
                <button type="button" class="cs-webkih-hero-slider-arrow cs-webkih-hero-slider-arrow-prev" data-direction="prev" aria-label="<?php esc_attr_e( 'Previous slide', 'webkih-hero-slider' ); ?>">
                    <span aria-hidden="true">&#10094;</span>
                </button>
                <button type="button" class="cs-webkih-hero-slider-arrow cs-webkih-hero-slider-arrow-next" data-direction="next" aria-label="<?php esc_attr_e( 'Next slide', 'webkih-hero-slider' ); ?>">
                    <span aria-hidden="true">&#10095;</span>
                </button>
            </div>
        <?php endif; ?>
        <?php foreach ( $slides as $index => $slide ) : ?>
            <article class="cs-webkih-hero-slide<?php echo 0 === $index ? ' is-active' : ''; ?>" aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>">
                <div class="cs-webkih-hero-slide-bg" style="background-image:url('<?php echo esc_url( $slide['image'] ); ?>');"></div>
                <div class="cs-webkih-hero-slide-overlay">
                    <?php if ( $show_title && ! empty( $slide['title'] ) ) : ?>
                        <h2 class="cs-webkih-hero-slide-title"><?php echo esc_html( $slide['title'] ); ?></h2>
                    <?php endif; ?>

                    <?php if ( $show_caption && ! empty( $slide['caption'] ) ) : ?>
                        <p class="cs-webkih-hero-slide-caption"><?php echo esc_html( $slide['caption'] ); ?></p>
                    <?php endif; ?>

                    <?php if ( $show_button && ! empty( $slide['button_label'] ) && ! empty( $slide['button_link'] ) ) : ?>
                        <a class="cs-webkih-hero-slide-button" href="<?php echo esc_url( $slide['button_link'] ); ?>">
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
 * Get available animation styles.
 *
 * @return array<string, array<string, string>>
 */
function wkhs_get_webkih_hero_slider_animation_styles() {
    $styles = [
        'default'     => [
            'label'       => __( 'Default', 'webkih-hero-slider' ),
            'description' => __( 'Standard smooth slide transition.', 'webkih-hero-slider' ),
        ],
        'animation_1' => [
            'label'       => __( 'Animation 1 (Bounce)', 'webkih-hero-slider' ),
            'description' => __( 'Slide content appears with a light bouncing motion.', 'webkih-hero-slider' ),
        ],
        'animation_2' => [
            'label'       => __( 'Animation 2 (Extend)', 'webkih-hero-slider' ),
            'description' => __( 'Elements smoothly expand or stretch into view.', 'webkih-hero-slider' ),
        ],
        'animation_3' => [
            'label'       => __( 'Animation 3 (Fade)', 'webkih-hero-slider' ),
            'description' => __( 'Content gently fades in for a clean and elegant effect.', 'webkih-hero-slider' ),
        ],
        'animation_4' => [
            'label'       => __( 'Animation 4 (Split Enter)', 'webkih-hero-slider' ),
            'description' => __( 'Text slides in from the left while the image moves in from the right.', 'webkih-hero-slider' ),
        ],
        'animation_5' => [
            'label'       => __( 'Animation 5 (Cross Motion)', 'webkih-hero-slider' ),
            'description' => __( 'Text moves up into view while the image glides down for a crossed entry effect.', 'webkih-hero-slider' ),
        ],
        'animation_6' => [
            'label'       => __( 'Animation 6 (Reveal Mask)', 'webkih-hero-slider' ),
            'description' => __( 'Content is smoothly revealed as a sliding mask uncovers the text while the image fades and scales into place.', 'webkih-hero-slider' ),
        ],
        'animation_7' => [
            'label'       => __( 'Animation 7 (Cinematic Parallax)', 'webkih-hero-slider' ),
            'description' => __( 'Slide elements move at different speeds to create a subtle layered parallax entrance.', 'webkih-hero-slider' ),
        ],
        'animation_8' => [
            'label'       => __( 'Animation 8 (Zoom Focus)', 'webkih-hero-slider' ),
            'description' => __( 'Image gently zooms into focus while text fades and lifts into view.', 'webkih-hero-slider' ),
        ],
        'animation_9' => [
            'label'       => __( 'Animation 9 (Sequential Reveal)', 'webkih-hero-slider' ),
            'description' => __( 'Slide elements appear one by one with a smooth staggered entrance.', 'webkih-hero-slider' ),
        ],
        'animation_10' => [
            'label'       => __( 'Animation 10 (Blur Focus)', 'webkih-hero-slider' ),
            'description' => __( 'Content fades in from a soft blur and becomes sharp as it settles into position.', 'webkih-hero-slider' ),
        ],
        'animation_11' => [
            'label'       => __( 'Animation 11 (Flip Enter)', 'webkih-hero-slider' ),
            'description' => __( 'Slide content rotates subtly in 3D before smoothly settling.', 'webkih-hero-slider' ),
        ],
        'animation_12' => [
            'label'       => __( 'Animation 12 (Curtain Reveal)', 'webkih-hero-slider' ),
            'description' => __( 'A smooth sliding overlay reveals the slide content underneath.', 'webkih-hero-slider' ),
        ],
        'animation_13' => [
            'label'       => __( 'Animation 13 (Elastic Rise)', 'webkih-hero-slider' ),
            'description' => __( 'Slide content rises with a subtle spring motion before settling.', 'webkih-hero-slider' ),
        ],
    ];

    /**
     * Filter animation style options.
     *
     * @param array<string, array<string, string>> $styles Animation style definitions.
     */
    return apply_filters( 'wkhs_webkih_hero_slider_animation_styles', $styles );
}

/**
 * Query slide data from custom post type.
 *
 * @return array<int, array<string, string>>
 */
function wkhs_get_hero_slides() {
    $query = new WP_Query(
        [
            'post_type'      => 'wkhs_hero_slide',
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
            'caption'      => (string) get_post_meta( $post->ID, '_wkhs_slide_caption', true ),
            'button_label' => (string) get_post_meta( $post->ID, '_wkhs_slide_button_label', true ),
            'button_link'  => (string) get_post_meta( $post->ID, '_wkhs_slide_button_link', true ),
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
function wkhs_get_webkih_hero_slider_settings() {
    $defaults = [
        'height'          => 460,
        'timer'           => 4500,
        'animation_style' => 'animation_12',
        'show_title'      => 1,
        'show_caption'    => 1,
        'show_button'     => 1,
        'center_content'  => 0,
        'enable_arrows'   => 1,
        'arrow_style'     => 'bottom_rounded',
        'text_color'      => '#ffffff',
        'button_bg_color' => '#ffffff',
        'mobile_image_behavior' => 'cover',
    ];

    $settings = get_option( 'wkhs_webkih_hero_slider_settings', [] );

    if ( ! is_array( $settings ) ) {
        return $defaults;
    }

    $settings = wp_parse_args( $settings, $defaults );

    if ( empty( $settings['timer'] ) && ! empty( $settings['speed'] ) ) {
        $settings['timer'] = absint( $settings['speed'] );
    }

    return $settings;
}
