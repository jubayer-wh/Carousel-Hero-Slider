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
    $animation_styles = cs_get_wbk_hero_slider_animation_styles();

    $atts = shortcode_atts(
        [
            'height' => (string) $defaults['height'],
            'timer'  => (string) $defaults['timer'],
            'speed'  => '',
        ],
        $atts,
        'wbk_hero_slider'
    );

    $height      = max( 220, absint( $atts['height'] ) );
    $timer_value = '' !== $atts['timer'] ? $atts['timer'] : $atts['speed'];
    $timer       = max( 2000, absint( $timer_value ) );

    $show_title    = ! empty( $defaults['show_title'] );
    $show_caption  = ! empty( $defaults['show_caption'] );
    $show_button    = ! empty( $defaults['show_button'] );
    $center_content = ! empty( $defaults['center_content'] );
    $enable_arrows  = ! empty( $defaults['enable_arrows'] );
    $animation     = isset( $animation_styles[ $defaults['animation_style'] ] ) ? $defaults['animation_style'] : 'default';
    $arrow_style   = in_array( $defaults['arrow_style'], [ 'side', 'bottom_right', 'bottom_rounded' ], true ) ? $defaults['arrow_style'] : 'bottom_rounded';
    $slides        = cs_get_hero_slides();

    if ( empty( $slides ) ) {
        return '<p><strong>Carousel Slider:</strong> Create at least one Hero Slide in the admin panel.</p>';
    }

    wp_enqueue_style( 'cs-wbk-hero-slider-css', CS_URL . 'assets/css/wbk-hero-slider.css', [], CS_VER );
    wp_enqueue_script( 'cs-wbk-hero-slider-js', CS_URL . 'assets/js/wbk-hero-slider.js', [], CS_VER, true );

    ob_start();
    ?>
    <div class="cs-wbk-hero-slider cs-animation-<?php echo esc_attr( $animation ); ?> cs-nav-style-<?php echo esc_attr( $arrow_style ); ?><?php echo $center_content ? ' cs-content-align-center' : ''; ?>" data-timer="<?php echo esc_attr( $timer ); ?>" data-animation="<?php echo esc_attr( $animation ); ?>" style="--cs-slider-height:<?php echo esc_attr( $height ); ?>px;">
        <?php if ( $enable_arrows && count( $slides ) > 1 ) : ?>
            <div class="cs-wbk-hero-slider-nav" aria-label="<?php esc_attr_e( 'Slide navigation', 'carousel-hero-slider' ); ?>">
                <button type="button" class="cs-wbk-hero-slider-arrow cs-wbk-hero-slider-arrow-prev" data-direction="prev" aria-label="<?php esc_attr_e( 'Previous slide', 'carousel-hero-slider' ); ?>">
                    <span aria-hidden="true">&#10094;</span>
                </button>
                <button type="button" class="cs-wbk-hero-slider-arrow cs-wbk-hero-slider-arrow-next" data-direction="next" aria-label="<?php esc_attr_e( 'Next slide', 'carousel-hero-slider' ); ?>">
                    <span aria-hidden="true">&#10095;</span>
                </button>
            </div>
        <?php endif; ?>
        <?php foreach ( $slides as $index => $slide ) : ?>
            <article class="cs-wbk-hero-slide<?php echo 0 === $index ? ' is-active' : ''; ?>" aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>">
                <div class="cs-wbk-hero-slide-bg" style="background-image:url('<?php echo esc_url( $slide['image'] ); ?>');"></div>
                <div class="cs-wbk-hero-slide-overlay">
                    <?php if ( $show_title && ! empty( $slide['title'] ) ) : ?>
                        <h2 class="cs-wbk-hero-slide-title"><?php echo esc_html( $slide['title'] ); ?></h2>
                    <?php endif; ?>

                    <?php if ( $show_caption && ! empty( $slide['caption'] ) ) : ?>
                        <p class="cs-wbk-hero-slide-caption"><?php echo esc_html( $slide['caption'] ); ?></p>
                    <?php endif; ?>

                    <?php if ( $show_button && ! empty( $slide['button_label'] ) && ! empty( $slide['button_link'] ) ) : ?>
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
 * Get available animation styles.
 *
 * @return array<string, array<string, string>>
 */
function cs_get_wbk_hero_slider_animation_styles() {
    $styles = [
        'default'     => [
            'label'       => __( 'Default', 'carousel-hero-slider' ),
            'description' => __( 'Standard smooth slide transition.', 'carousel-hero-slider' ),
        ],
        'animation_1' => [
            'label'       => __( 'Animation 1 (Bounce)', 'carousel-hero-slider' ),
            'description' => __( 'Slide content appears with a light bouncing motion.', 'carousel-hero-slider' ),
        ],
        'animation_2' => [
            'label'       => __( 'Animation 2 (Extend)', 'carousel-hero-slider' ),
            'description' => __( 'Elements smoothly expand or stretch into view.', 'carousel-hero-slider' ),
        ],
        'animation_3' => [
            'label'       => __( 'Animation 3 (Fade)', 'carousel-hero-slider' ),
            'description' => __( 'Content gently fades in for a clean and elegant effect.', 'carousel-hero-slider' ),
        ],
        'animation_4' => [
            'label'       => __( 'Animation 4 (Split Enter)', 'carousel-hero-slider' ),
            'description' => __( 'Text slides in from the left while the image moves in from the right.', 'carousel-hero-slider' ),
        ],
        'animation_5' => [
            'label'       => __( 'Animation 5 (Cross Motion)', 'carousel-hero-slider' ),
            'description' => __( 'Text moves up into view while the image glides down for a crossed entry effect.', 'carousel-hero-slider' ),
        ],
        'animation_6' => [
            'label'       => __( 'Animation 6 (Reveal Mask)', 'carousel-hero-slider' ),
            'description' => __( 'Content is smoothly revealed as a sliding mask uncovers the text while the image fades and scales into place.', 'carousel-hero-slider' ),
        ],
        'animation_7' => [
            'label'       => __( 'Animation 7 (Cinematic Parallax)', 'carousel-hero-slider' ),
            'description' => __( 'Slide elements move at different speeds to create a subtle layered parallax entrance.', 'carousel-hero-slider' ),
        ],
    ];

    /**
     * Filter animation style options.
     *
     * @param array<string, array<string, string>> $styles Animation style definitions.
     */
    return apply_filters( 'cs_wbk_hero_slider_animation_styles', $styles );
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
        'height'          => 460,
        'timer'           => 4500,
        'animation_style' => 'default',
        'show_title'      => 1,
        'show_caption'    => 1,
        'show_button'     => 1,
        'center_content'  => 0,
        'enable_arrows'   => 1,
        'arrow_style'     => 'bottom_rounded',
    ];

    $settings = get_option( 'cs_wbk_hero_slider_settings', [] );

    if ( ! is_array( $settings ) ) {
        return $defaults;
    }

    $settings = wp_parse_args( $settings, $defaults );

    if ( empty( $settings['timer'] ) && ! empty( $settings['speed'] ) ) {
        $settings['timer'] = absint( $settings['speed'] );
    }

    return $settings;
}
