<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register admin menu and settings submenu.
 */
function wkhs_register_admin_menu() {
    add_options_page(
        __( 'WebKih Hero Slider', 'webkih-hero-slider' ),
        __( 'WebKih Hero Slider', 'webkih-hero-slider' ),
        'manage_options',
        'webkih-hero-slider',
        'wkhs_render_settings_page'
    );
}
add_action( 'admin_menu', 'wkhs_register_admin_menu' );

/**
 * Enqueue admin styles for plugin screens.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function wkhs_enqueue_admin_assets( $hook_suffix ) {
    $screen = get_current_screen();

    if ( 'settings_page_webkih-hero-slider' === $hook_suffix || ( $screen && 'wkhs_hero_slide' === $screen->post_type ) ) {
        wp_enqueue_style( 'cs-admin-css', WKHS_URL . 'assets/css/admin.css', [], WKHS_VER );
    }

    if ( 'settings_page_webkih-hero-slider' === $hook_suffix ) {
        wp_enqueue_script( 'cs-admin-settings-js', WKHS_URL . 'assets/js/admin-settings.js', [], WKHS_VER, true );
    }
}
add_action( 'admin_enqueue_scripts', 'wkhs_enqueue_admin_assets' );

/**
 * Register plugin settings.
 */
function wkhs_register_settings() {
    register_setting(
        'wkhs_webkih_hero_slider_group',
        'wkhs_webkih_hero_slider_settings',
        [
            'type'              => 'array',
            'sanitize_callback' => 'wkhs_sanitize_webkih_hero_slider_settings',
            'default'           => wkhs_get_webkih_hero_slider_settings(),
        ]
    );

    add_settings_section(
        'wkhs_webkih_hero_slider_main',
        __( 'Layout & Display', 'webkih-hero-slider' ),
        '__return_false',
        'webkih-hero-slider'
    );

    add_settings_section(
        'wkhs_webkih_hero_slider_timing',
        __( 'Timing Controls', 'webkih-hero-slider' ),
        'wkhs_render_timing_section_text',
        'webkih-hero-slider'
    );

    add_settings_section(
        'wkhs_webkih_hero_slider_animation',
        __( 'Animation Settings', 'webkih-hero-slider' ),
        'wkhs_render_animation_section_text',
        'webkih-hero-slider'
    );

    add_settings_section(
        'wkhs_webkih_hero_slider_visibility',
        __( 'Content Visibility', 'webkih-hero-slider' ),
        'wkhs_render_visibility_section_text',
        'webkih-hero-slider'
    );

    add_settings_section(
        'wkhs_webkih_hero_slider_navigation',
        __( 'Navigation Arrows', 'webkih-hero-slider' ),
        'wkhs_render_navigation_section_text',
        'webkih-hero-slider'
    );

    add_settings_field(
        'wkhs_height',
        __( 'Slider Height (px)', 'webkih-hero-slider' ),
        'wkhs_render_number_field',
        'webkih-hero-slider',
        'wkhs_webkih_hero_slider_main',
        [
            'key' => 'height',
        ]
    );

    add_settings_field(
        'wkhs_timer',
        __( 'Slide Timer (ms)', 'webkih-hero-slider' ),
        'wkhs_render_number_field',
        'webkih-hero-slider',
        'wkhs_webkih_hero_slider_timing',
        [
            'key' => 'timer',
        ]
    );

    add_settings_field(
        'wkhs_mobile_image_behavior',
        __( 'Mobile Image Behavior', 'webkih-hero-slider' ),
        'wkhs_render_mobile_image_behavior_field',
        'webkih-hero-slider',
        'wkhs_webkih_hero_slider_main'
    );

    add_settings_field(
        'wkhs_animation_style',
        __( 'Animation Style', 'webkih-hero-slider' ),
        'wkhs_render_animation_style_field',
        'webkih-hero-slider',
        'wkhs_webkih_hero_slider_animation'
    );

    add_settings_field(
        'wkhs_text_color',
        __( 'Text Color', 'webkih-hero-slider' ),
        'wkhs_render_color_field',
        'webkih-hero-slider',
        'wkhs_webkih_hero_slider_main',
        [
            'key' => 'text_color',
        ]
    );

    add_settings_field(
        'wkhs_button_bg_color',
        __( 'Button Background Color', 'webkih-hero-slider' ),
        'wkhs_render_color_field',
        'webkih-hero-slider',
        'wkhs_webkih_hero_slider_main',
        [
            'key' => 'button_bg_color',
        ]
    );

    $visibility_fields = [
        'show_title'     => __( 'Show Slide Title', 'webkih-hero-slider' ),
        'show_caption'   => __( 'Show Caption Text', 'webkih-hero-slider' ),
        'show_button'    => __( 'Show Slide Button', 'webkih-hero-slider' ),
        'center_content' => __( 'Center Content', 'webkih-hero-slider' ),
    ];

    foreach ( $visibility_fields as $key => $label ) {
        add_settings_field(
            'wkhs_' . $key,
            $label,
            'wkhs_render_toggle_field',
            'webkih-hero-slider',
            'wkhs_webkih_hero_slider_visibility',
            [
                'key' => $key,
            ]
        );
    }

    add_settings_field(
        'wkhs_enable_arrows',
        __( 'Show Navigation Arrows', 'webkih-hero-slider' ),
        'wkhs_render_toggle_field',
        'webkih-hero-slider',
        'wkhs_webkih_hero_slider_navigation',
        [
            'key' => 'enable_arrows',
        ]
    );

    add_settings_field(
        'wkhs_arrow_style',
        __( 'Arrow Style', 'webkih-hero-slider' ),
        'wkhs_render_arrow_style_field',
        'webkih-hero-slider',
        'wkhs_webkih_hero_slider_navigation'
    );
}
add_action( 'admin_init', 'wkhs_register_settings' );

/**
 * Render helper text for timing section.
 */
function wkhs_render_timing_section_text() {
    echo '<p>' . esc_html__( 'Set how fast the slider rotates through slides.', 'webkih-hero-slider' ) . '</p>';
}

/**
 * Render helper text for animation section.
 */
function wkhs_render_animation_section_text() {
    echo '<p>' . esc_html__( 'Choose one animation style and control how slide content enters.', 'webkih-hero-slider' ) . '</p>';
}

/**
 * Render helper text for visibility section.
 */
function wkhs_render_visibility_section_text() {
    echo '<p>' . esc_html__( 'Enable or disable slide title, caption, and button output on the frontend.', 'webkih-hero-slider' ) . '</p>';
}

/**
 * Render helper text for navigation section.
 */
function wkhs_render_navigation_section_text() {
    echo '<p>' . esc_html__( 'Enable overlay arrows and choose one style for slider navigation.', 'webkih-hero-slider' ) . '</p>';
}

/**
 * Add metaboxes for slide fields.
 */
function wkhs_add_slide_metaboxes() {
    add_meta_box(
        'cs-slide-content-metabox',
        __( 'Slide Content', 'webkih-hero-slider' ),
        'wkhs_render_slide_content_metabox',
        'wkhs_hero_slide',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wkhs_add_slide_metaboxes' );

/**
 * Render slide meta fields.
 *
 * @param WP_Post $post Post object.
 */
function wkhs_render_slide_content_metabox( $post ) {
    wp_nonce_field( 'wkhs_save_slide_meta', 'wkhs_slide_meta_nonce' );

    $caption      = (string) get_post_meta( $post->ID, '_wkhs_slide_caption', true );
    $button_label = (string) get_post_meta( $post->ID, '_wkhs_slide_button_label', true );
    $button_link  = (string) get_post_meta( $post->ID, '_wkhs_slide_button_link', true );
    ?>
    <p>
        <label for="cs-slide-caption"><strong><?php esc_html_e( 'Caption', 'webkih-hero-slider' ); ?></strong></label><br />
        <textarea id="cs-slide-caption" class="widefat" rows="3" name="wkhs_slide_caption"><?php echo esc_textarea( $caption ); ?></textarea>
    </p>

    <p>
        <label for="cs-slide-button-label"><strong><?php esc_html_e( 'Button Label', 'webkih-hero-slider' ); ?></strong></label><br />
        <input id="cs-slide-button-label" class="regular-text" type="text" name="wkhs_slide_button_label" value="<?php echo esc_attr( $button_label ); ?>" maxlength="40" />
    </p>

    <p>
        <label for="cs-slide-button-link"><strong><?php esc_html_e( 'Button Link', 'webkih-hero-slider' ); ?></strong></label><br />
        <input id="cs-slide-button-link" class="widefat" type="url" name="wkhs_slide_button_link" value="<?php echo esc_attr( $button_link ); ?>" placeholder="https://example.com/page" />
    </p>

    <p class="description">
        <?php esc_html_e( 'Tip: Use the slide title field for the heading and set a Featured Image for the background image.', 'webkih-hero-slider' ); ?>
    </p>
    <?php
}

/**
 * Save slide meta fields.
 *
 * @param int $post_id Post ID.
 */
function wkhs_save_slide_meta( $post_id ) {
    if ( ! isset( $_POST['wkhs_slide_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wkhs_slide_meta_nonce'] ) ), 'wkhs_save_slide_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( 'wkhs_hero_slide' !== get_post_type( $post_id ) ) {
        return;
    }

    $caption      = isset( $_POST['wkhs_slide_caption'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wkhs_slide_caption'] ) ) : '';
    $button_label = isset( $_POST['wkhs_slide_button_label'] ) ? sanitize_text_field( wp_unslash( $_POST['wkhs_slide_button_label'] ) ) : '';
    $button_link  = isset( $_POST['wkhs_slide_button_link'] ) ? esc_url_raw( wp_unslash( $_POST['wkhs_slide_button_link'] ) ) : '';

    update_post_meta( $post_id, '_wkhs_slide_caption', $caption );
    update_post_meta( $post_id, '_wkhs_slide_button_label', $button_label );
    update_post_meta( $post_id, '_wkhs_slide_button_link', $button_link );
}
add_action( 'save_post', 'wkhs_save_slide_meta' );

/**
 * Add duplicate row action to slide posts.
 *
 * @param array<string, string> $actions Existing row actions.
 * @param WP_Post               $post Current post.
 * @return array<string, string>
 */
function wkhs_add_duplicate_slide_action( $actions, $post ) {
    if ( 'wkhs_hero_slide' !== $post->post_type || ! current_user_can( 'edit_posts' ) ) {
        return $actions;
    }

    $duplicate_url = wp_nonce_url(
        add_query_arg(
            [
                'action' => 'wkhs_duplicate_slide',
                'post'   => $post->ID,
            ],
            admin_url( 'admin.php' )
        ),
        'wkhs_duplicate_slide_' . $post->ID
    );

    $actions['wkhs_duplicate_slide'] = '<a href="' . esc_url( $duplicate_url ) . '">' . esc_html__( 'Duplicate', 'webkih-hero-slider' ) . '</a>';

    return $actions;
}
add_filter( 'post_row_actions', 'wkhs_add_duplicate_slide_action', 10, 2 );

/**
 * Handle duplicate action.
 */
function wkhs_handle_duplicate_slide_action() {
    if ( ! is_admin() || ! isset( $_GET['action'] ) || 'wkhs_duplicate_slide' !== $_GET['action'] ) {
        return;
    }

    $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

    if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
        wp_die( esc_html__( 'You are not allowed to duplicate this slide.', 'webkih-hero-slider' ) );
    }

    check_admin_referer( 'wkhs_duplicate_slide_' . $post_id );

    $post = get_post( $post_id );

    if ( ! $post || 'wkhs_hero_slide' !== $post->post_type ) {
        wp_die( esc_html__( 'Invalid slide for duplication.', 'webkih-hero-slider' ) );
    }

    $new_post_id = wp_insert_post(
        [
            'post_type'   => 'wkhs_hero_slide',
            'post_status' => 'draft',
            'post_title'  => $post->post_title . ' (Copy)',
            'menu_order'  => (int) $post->menu_order,
        ]
    );

    if ( ! $new_post_id || is_wp_error( $new_post_id ) ) {
        wp_die( esc_html__( 'Unable to duplicate slide.', 'webkih-hero-slider' ) );
    }

    $meta_keys = [ '_wkhs_slide_caption', '_wkhs_slide_button_label', '_wkhs_slide_button_link' ];

    foreach ( $meta_keys as $meta_key ) {
        update_post_meta( $new_post_id, $meta_key, get_post_meta( $post_id, $meta_key, true ) );
    }

    $thumbnail_id = get_post_thumbnail_id( $post_id );
    if ( $thumbnail_id ) {
        set_post_thumbnail( $new_post_id, $thumbnail_id );
    }

    wp_safe_redirect(
        add_query_arg(
            [
                'post_type'  => 'wkhs_hero_slide',
                'duplicated' => 1,
            ],
            admin_url( 'edit.php' )
        )
    );
    exit;
}
add_action( 'admin_init', 'wkhs_handle_duplicate_slide_action' );

/**
 * Admin notice for duplicated slide.
 */
function wkhs_duplicate_slide_notice() {
    $post_type  = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $duplicated = filter_input( INPUT_GET, 'duplicated', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

    if ( 'wkhs_hero_slide' !== $post_type || '1' !== $duplicated ) {
        return;
    }

    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Slide duplicated successfully as draft.', 'webkih-hero-slider' ) . '</p></div>';
}
add_action( 'admin_notices', 'wkhs_duplicate_slide_notice' );

/**
 * Sanitize settings input.
 *
 * @param array<string, mixed> $input Raw input.
 * @return array<string, mixed>
 */
function wkhs_sanitize_webkih_hero_slider_settings( $input ) {
    $defaults = wkhs_get_webkih_hero_slider_settings();

    $animation_style = isset( $input['animation_style'] ) ? sanitize_key( $input['animation_style'] ) : $defaults['animation_style'];

    $animation_styles = wkhs_get_webkih_hero_slider_animation_styles();

    if ( ! isset( $animation_styles[ $animation_style ] ) ) {
        $animation_style = 'animation_12';
    }

    $arrow_style = isset( $input['arrow_style'] ) ? sanitize_key( $input['arrow_style'] ) : $defaults['arrow_style'];

    if ( ! in_array( $arrow_style, [ 'side', 'bottom_right', 'bottom_rounded' ], true ) ) {
        $arrow_style = 'bottom_rounded';
    }

    $mobile_image_behavior = isset( $input['mobile_image_behavior'] ) ? sanitize_key( $input['mobile_image_behavior'] ) : $defaults['mobile_image_behavior'];

    if ( 'no_repeat' === $mobile_image_behavior ) {
        $mobile_image_behavior = 'no-repeat';
    }

    if ( ! in_array( $mobile_image_behavior, [ 'cover', 'contain', 'no-repeat' ], true ) ) {
        $mobile_image_behavior = 'cover';
    }

    return [
        'height'          => max( 220, absint( $input['height'] ?? $defaults['height'] ) ),
        'timer'           => max( 2000, absint( $input['timer'] ?? $defaults['timer'] ) ),
        'animation_style' => $animation_style,
        'show_title'      => empty( $input['show_title'] ) ? 0 : 1,
        'show_caption'    => empty( $input['show_caption'] ) ? 0 : 1,
        'show_button'     => empty( $input['show_button'] ) ? 0 : 1,
        'center_content'  => empty( $input['center_content'] ) ? 0 : 1,
        'enable_arrows'   => empty( $input['enable_arrows'] ) ? 0 : 1,
        'arrow_style'     => $arrow_style,
        'mobile_image_behavior' => $mobile_image_behavior,
        'text_color'      => wkhs_sanitize_color_value( $input['text_color'] ?? $defaults['text_color'], $defaults['text_color'] ),
        'button_bg_color' => wkhs_sanitize_color_value( $input['button_bg_color'] ?? $defaults['button_bg_color'], $defaults['button_bg_color'] ),
    ];
}

/**
 * Render one numeric settings field.
 *
 * @param array<string, string> $args Field args.
 */
function wkhs_render_number_field( $args ) {
    $settings = wkhs_get_webkih_hero_slider_settings();
    $key      = $args['key'];
    $value    = $settings[ $key ] ?? '';
    ?>
    <div class="cs-setting-input">
        <input
            type="number"
            class="regular-text"
            name="wkhs_webkih_hero_slider_settings[<?php echo esc_attr( $key ); ?>]"
            value="<?php echo esc_attr( (string) $value ); ?>"
            min="1"
            step="1"
        />
        <?php if ( 'height' === $key ) : ?>
            <span class="cs-setting-input__hint"><?php esc_html_e( 'Recommended: 380–560 px', 'webkih-hero-slider' ); ?></span>
        <?php endif; ?>
        <?php if ( 'timer' === $key ) : ?>
            <span class="cs-setting-input__hint"><?php esc_html_e( 'Minimum: 2000 ms', 'webkih-hero-slider' ); ?></span>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Sanitize color value with fallback.
 *
 * @param mixed  $value Raw color value.
 * @param string $fallback Fallback color.
 */
function wkhs_sanitize_color_value( $value, $fallback ) {
    $color = is_string( $value ) ? sanitize_hex_color( $value ) : null;

    return $color ? $color : $fallback;
}

/**
 * Render one color settings field.
 *
 * @param array<string, string> $args Field args.
 */
function wkhs_render_color_field( $args ) {
    $settings = wkhs_get_webkih_hero_slider_settings();
    $key      = $args['key'];
    $value    = isset( $settings[ $key ] ) ? (string) $settings[ $key ] : '';
    ?>
    <input
        type="color"
        name="wkhs_webkih_hero_slider_settings[<?php echo esc_attr( $key ); ?>]"
        value="<?php echo esc_attr( $value ); ?>"
    />
    <?php
}

/**
 * Render animation style selection radios.
 */
function wkhs_render_animation_style_field() {
    $settings      = wkhs_get_webkih_hero_slider_settings();
    $current_style = isset( $settings['animation_style'] ) ? $settings['animation_style'] : 'animation_12';
    $options       = wkhs_get_webkih_hero_slider_animation_styles();

    foreach ( $options as $value => $option ) {
        ?>
        <label class="cs-animation-choice">
            <input
                type="radio"
                name="wkhs_webkih_hero_slider_settings[animation_style]"
                value="<?php echo esc_attr( $value ); ?>"
                <?php checked( $current_style, $value ); ?>
            />
            <span class="cs-animation-choice__content">
                <strong><?php echo esc_html( $option['label'] ); ?></strong><br />
                <span class="description"><?php echo esc_html( $option['description'] ); ?></span>
            </span>
        </label>
        <?php
    }
}

/**
 * Render mobile image behavior selection radios.
 */
function wkhs_render_mobile_image_behavior_field() {
    $settings         = wkhs_get_webkih_hero_slider_settings();
    $current_behavior = isset( $settings['mobile_image_behavior'] ) ? $settings['mobile_image_behavior'] : 'cover';
    $options          = [
        'cover'     => [
            'label'       => __( 'Cover', 'webkih-hero-slider' ),
            'description' => __( 'Fill the slide area by cropping as needed (current behavior).', 'webkih-hero-slider' ),
        ],
        'contain'   => [
            'label'       => __( 'Contain', 'webkih-hero-slider' ),
            'description' => __( 'Show the full image on mobile while keeping it inside the slide area.', 'webkih-hero-slider' ),
        ],
        'no-repeat' => [
            'label'       => __( 'No Repeat', 'webkih-hero-slider' ),
            'description' => __( 'Prevent background tiling on mobile while preserving the existing cover style.', 'webkih-hero-slider' ),
        ],
    ];

    foreach ( $options as $value => $option ) {
        ?>
        <label class="cs-animation-choice">
            <input
                type="radio"
                name="wkhs_webkih_hero_slider_settings[mobile_image_behavior]"
                value="<?php echo esc_attr( $value ); ?>"
                <?php checked( $current_behavior, $value ); ?>
            />
            <span class="cs-animation-choice__content">
                <strong><?php echo esc_html( $option['label'] ); ?></strong><br />
                <span class="description"><?php echo esc_html( $option['description'] ); ?></span>
            </span>
        </label>
        <?php
    }
}


/**
 * Render arrow style selection radios.
 */
function wkhs_render_arrow_style_field() {
    $settings      = wkhs_get_webkih_hero_slider_settings();
    $current_style = isset( $settings['arrow_style'] ) ? $settings['arrow_style'] : 'bottom_rounded';
    $options       = [
        'side'           => [
            'label'       => __( 'Side Arrows', 'webkih-hero-slider' ),
            'description' => __( 'Left and right arrows on both sides of the slider image.', 'webkih-hero-slider' ),
        ],
        'bottom_right'   => [
            'label'       => __( 'Bottom Right Arrows', 'webkih-hero-slider' ),
            'description' => __( 'A compact arrow pair pinned to the slider\'s bottom-right corner.', 'webkih-hero-slider' ),
        ],
        'bottom_rounded' => [
            'label'       => __( 'Bottom Rounded Arrows', 'webkih-hero-slider' ),
            'description' => __( 'Small rounded arrows centered along the bottom of the slider.', 'webkih-hero-slider' ),
        ],
    ];

    foreach ( $options as $value => $option ) {
        ?>
        <label class="cs-animation-choice">
            <input
                type="radio"
                name="wkhs_webkih_hero_slider_settings[arrow_style]"
                value="<?php echo esc_attr( $value ); ?>"
                <?php checked( $current_style, $value ); ?>
            />
            <span class="cs-animation-choice__content">
                <strong><?php echo esc_html( $option['label'] ); ?></strong><br />
                <span class="description"><?php echo esc_html( $option['description'] ); ?></span>
            </span>
        </label>
        <?php
    }
}

/**
 * Render one visibility toggle field.
 *
 * @param array<string, string> $args Field args.
 */
function wkhs_render_toggle_field( $args ) {
    $settings = wkhs_get_webkih_hero_slider_settings();
    $key      = $args['key'];
    $checked  = ! empty( $settings[ $key ] );
    ?>
    <label class="cs-switch" for="cs-<?php echo esc_attr( $key ); ?>">
        <input
            id="cs-<?php echo esc_attr( $key ); ?>"
            type="checkbox"
            name="wkhs_webkih_hero_slider_settings[<?php echo esc_attr( $key ); ?>]"
            value="1"
            <?php checked( $checked ); ?>
        />
        <span class="cs-switch__track" aria-hidden="true"></span>
        <span class="cs-switch__label"><?php esc_html_e( 'Enable setting', 'webkih-hero-slider' ); ?></span>
    </label>
    <?php
}

/**
 * Render settings page.
 */
function wkhs_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $sections = [
        'wkhs_webkih_hero_slider_main'       => [
            'title'       => __( 'Layout & Display', 'webkih-hero-slider' ),
            'description' => __( 'Set the slider height and overall display layout.', 'webkih-hero-slider' ),
        ],
        'wkhs_webkih_hero_slider_timing'     => [
            'title'       => __( 'Timing Controls', 'webkih-hero-slider' ),
            'description' => __( 'Set how fast the slider rotates through slides.', 'webkih-hero-slider' ),
        ],
        'wkhs_webkih_hero_slider_animation'  => [
            'title'       => __( 'Animation Settings', 'webkih-hero-slider' ),
            'description' => __( 'Choose one animation style and control how slide content enters.', 'webkih-hero-slider' ),
        ],
        'wkhs_webkih_hero_slider_navigation' => [
            'title'       => __( 'Navigation Arrows', 'webkih-hero-slider' ),
            'description' => __( 'Enable overlay arrows and choose one style for slider navigation.', 'webkih-hero-slider' ),
        ],
        'wkhs_webkih_hero_slider_visibility' => [
            'title'       => __( 'Content Visibility', 'webkih-hero-slider' ),
            'description' => __( 'Enable or disable title, caption, and button output on the frontend.', 'webkih-hero-slider' ),
        ],
    ];

    ?>
    <div class="wrap cs-admin-wrap">
        <div class="cs-admin-panel">
            <div class="cs-admin-hero">
                <div class="cs-admin-hero__row cs-admin-hero__row--top">
                    <div class="cs-admin-hero__title-group">
                        <h1><?php esc_html_e( 'WebKih Hero Slider Settings', 'webkih-hero-slider' ); ?></h1>
                        <p><?php esc_html_e( 'Controls for display, timing, animation, navigation, and visibility.', 'webkih-hero-slider' ); ?></p>
                    </div>

                    <div class="cs-admin-actions">
                        <a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=wkhs_hero_slide' ) ); ?>">
                            <?php esc_html_e( 'Add New Slide', 'webkih-hero-slider' ); ?>
                        </a>
                        <a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=wkhs_hero_slide' ) ); ?>">
                            <?php esc_html_e( 'Manage Slides', 'webkih-hero-slider' ); ?>
                        </a>
                    </div>
                </div>

                <div class="cs-admin-hero__row cs-admin-hero__row--bottom" aria-label="<?php esc_attr_e( 'Shortcode helper', 'webkih-hero-slider' ); ?>">
                    <p class="cs-admin-hero__shortcode-label"><?php esc_html_e( 'Shortcode', 'webkih-hero-slider' ); ?></p>
                    <div class="cs-admin-hero__shortcode-tools" data-cs-shortcode-copy>
                        <code class="cs-shortcode-copy__value" data-cs-shortcode-text>[webkih_hero_slider]</code>
                        <button type="button" class="button cs-shortcode-copy__button" data-cs-shortcode-button data-copy-label="<?php esc_attr_e( 'Copy', 'webkih-hero-slider' ); ?>" data-copied-label="<?php esc_attr_e( 'Copied!', 'webkih-hero-slider' ); ?>"><?php esc_html_e( 'Copy', 'webkih-hero-slider' ); ?></button>
                        <span class="cs-shortcode-copy__tooltip" data-cs-shortcode-tooltip role="status" aria-live="polite" aria-hidden="true"><?php esc_html_e( 'Copied!', 'webkih-hero-slider' ); ?></span>
                    </div>
                </div>

                <p class="cs-admin-hero__shortcode-hint"><?php esc_html_e( 'Optional:', 'webkih-hero-slider' ); ?> <code>[webkih_hero_slider height="460" timer="4500"]</code></p>
            </div>

            <div class="cs-admin-card">
                <form method="post" action="options.php" class="cs-settings-form">
                    <?php settings_fields( 'wkhs_webkih_hero_slider_group' ); ?>

                    <div class="cs-settings-layout" data-cs-settings-layout>
                        <div class="cs-settings-nav">
                            <div class="cs-settings-nav__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Settings categories', 'webkih-hero-slider' ); ?>">
                                <?php $index = 0; ?>
                                <?php foreach ( $sections as $section_id => $section ) : ?>
                                    <button
                                        type="button"
                                        class="cs-settings-nav__button<?php echo 0 === $index ? ' is-active' : ''; ?>"
                                        role="tab"
                                        id="<?php echo esc_attr( $section_id . '-tab' ); ?>"
                                        aria-controls="<?php echo esc_attr( $section_id . '-panel' ); ?>"
                                        aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                                        data-panel-target="<?php echo esc_attr( $section_id ); ?>"
                                    >
                                        <?php echo esc_html( $section['title'] ); ?>
                                    </button>
                                    <?php $index++; ?>
                                <?php endforeach; ?>
                            </div>

                            <div class="cs-settings-nav__actions">
                                <?php submit_button( __( 'Save Settings', 'webkih-hero-slider' ), 'primary cs-save-button', 'submit', false ); ?>
                                <?php $settings_updated = filter_input( INPUT_GET, 'settings-updated', FILTER_SANITIZE_FULL_SPECIAL_CHARS ); ?>
                                <p class="cs-settings-save-feedback<?php echo 'true' === $settings_updated ? ' is-visible' : ''; ?>" data-cs-save-feedback role="status" aria-live="polite">
                                    <?php esc_html_e( 'Settings saved.', 'webkih-hero-slider' ); ?>
                                </p>
                            </div>
                        </div>

                        <div class="cs-settings-content">
                            <?php $index = 0; ?>
                            <?php foreach ( $sections as $section_id => $section ) : ?>
                                <section
                                    class="cs-settings-panel<?php echo 0 === $index ? ' is-active' : ''; ?>"
                                    id="<?php echo esc_attr( $section_id . '-panel' ); ?>"
                                    role="tabpanel"
                                    tabindex="0"
                                    aria-labelledby="<?php echo esc_attr( $section_id . '-tab' ); ?>"
                                    <?php echo 0 === $index ? '' : 'hidden'; ?>
                                >
                                    <h2><?php echo esc_html( $section['title'] ); ?></h2>
                                    <p class="description"><?php echo esc_html( $section['description'] ); ?></p>
                                    <table class="form-table" role="presentation">
                                        <tbody>
                                        <?php do_settings_fields( 'webkih-hero-slider', $section_id ); ?>
                                        </tbody>
                                    </table>
                                </section>
                                <?php $index++; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </form>
            </div>


            <div class="cs-admin-card cs-admin-support">
                <h3><?php esc_html_e( 'Support the Plugin', 'webkih-hero-slider' ); ?></h3>
                <form action="https://www.paypal.com/donate" method="post" target="_blank">
                    <input type="hidden" name="business" value="jubayerhossain.wh@gmail.com" />
                    <input type="hidden" name="currency_code" value="USD" />
                    <button type="submit" class="button">
                        <?php esc_html_e( 'Buy Me a Coffee', 'webkih-hero-slider' ); ?>
                    </button>
                </form>
            </div>

        </div>
    </div>
    <?php
}
