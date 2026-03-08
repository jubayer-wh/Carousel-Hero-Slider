<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register admin menu.
 */
function cs_register_admin_menu() {
    add_menu_page(
        __( 'Carousel Hero Slider', 'carousel-hero-slider' ),
        __( 'Carousel Hero Slider', 'carousel-hero-slider' ),
        'manage_options',
        'carousel-hero-slider',
        'cs_render_settings_page',
        'dashicons-images-alt2',
        25
    );
}
add_action( 'admin_menu', 'cs_register_admin_menu' );

/**
 * Register plugin settings.
 */
function cs_register_settings() {
    register_setting(
        'cs_wbk_hero_slider_group',
        'cs_wbk_hero_slider_settings',
        [
            'type'              => 'array',
            'sanitize_callback' => 'cs_sanitize_wbk_hero_slider_settings',
            'default'           => cs_get_wbk_hero_slider_settings(),
        ]
    );

    add_settings_section(
        'cs_wbk_hero_slider_main',
        __( 'Hero Slider Settings', 'carousel-hero-slider' ),
        '__return_false',
        'carousel-hero-slider'
    );

    $fields = [
        'img1'   => __( 'Image 1 URL', 'carousel-hero-slider' ),
        'img2'   => __( 'Image 2 URL', 'carousel-hero-slider' ),
        'img3'   => __( 'Image 3 URL', 'carousel-hero-slider' ),
        'height' => __( 'Slider Height (px)', 'carousel-hero-slider' ),
        'speed'  => __( 'Auto Slide Speed (ms)', 'carousel-hero-slider' ),
    ];

    foreach ( $fields as $key => $label ) {
        add_settings_field(
            'cs_' . $key,
            $label,
            'cs_render_field',
            'carousel-hero-slider',
            'cs_wbk_hero_slider_main',
            [
                'key' => $key,
            ]
        );
    }
}
add_action( 'admin_init', 'cs_register_settings' );

/**
 * Sanitize settings input.
 *
 * @param array<string, mixed> $input Raw input.
 * @return array<string, mixed>
 */
function cs_sanitize_wbk_hero_slider_settings( $input ) {
    $defaults = cs_get_wbk_hero_slider_settings();

    return [
        'img1'   => esc_url_raw( $input['img1'] ?? $defaults['img1'] ),
        'img2'   => esc_url_raw( $input['img2'] ?? $defaults['img2'] ),
        'img3'   => esc_url_raw( $input['img3'] ?? $defaults['img3'] ),
        'height' => max( 200, absint( $input['height'] ?? $defaults['height'] ) ),
        'speed'  => max( 1500, absint( $input['speed'] ?? $defaults['speed'] ) ),
    ];
}

/**
 * Render one settings field.
 *
 * @param array<string, string> $args Field args.
 */
function cs_render_field( $args ) {
    $settings = cs_get_wbk_hero_slider_settings();
    $key      = $args['key'];
    $value    = $settings[ $key ] ?? '';
    $type     = in_array( $key, [ 'height', 'speed' ], true ) ? 'number' : 'url';
    ?>
    <input
        type="<?php echo esc_attr( $type ); ?>"
        class="regular-text"
        name="cs_wbk_hero_slider_settings[<?php echo esc_attr( $key ); ?>]"
        value="<?php echo esc_attr( (string) $value ); ?>"
        <?php echo 'number' === $type ? 'min="1" step="1"' : ''; ?>
    />
    <?php
}

/**
 * Render settings page.
 */
function cs_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    wp_enqueue_style( 'cs-admin-css', CS_URL . 'assets/css/admin.css', [], CS_VER );
    ?>
    <div class="wrap cs-admin-wrap">
        <h1><?php esc_html_e( 'Carousel Hero Slider', 'carousel-hero-slider' ); ?></h1>
        <p><?php esc_html_e( 'Build responsive hero carousel sliders for landing pages, headers, and showcase sections.', 'carousel-hero-slider' ); ?></p>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'cs_wbk_hero_slider_group' );
            do_settings_sections( 'carousel-hero-slider' );
            submit_button();
            ?>
        </form>

        <hr />

        <h2><?php esc_html_e( 'Shortcode', 'carousel-hero-slider' ); ?></h2>
        <p><code>[wbk_hero_slider]</code></p>
        <p><?php esc_html_e( 'Optional overrides:', 'carousel-hero-slider' ); ?> <code>[wbk_hero_slider img1="..." img2="..." img3="..." height="360" speed="3500"]</code></p>
    </div>
    <?php
}
