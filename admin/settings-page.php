<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register admin menu and settings submenu.
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

    add_submenu_page(
        'carousel-hero-slider',
        __( 'Slider Settings', 'carousel-hero-slider' ),
        __( 'Settings', 'carousel-hero-slider' ),
        'manage_options',
        'carousel-hero-slider',
        'cs_render_settings_page'
    );
}
add_action( 'admin_menu', 'cs_register_admin_menu' );

/**
 * Enqueue admin styles for plugin screens.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function cs_enqueue_admin_assets( $hook_suffix ) {
    $screen = get_current_screen();

    if ( 'toplevel_page_carousel-hero-slider' === $hook_suffix || ( $screen && 'cs_hero_slide' === $screen->post_type ) ) {
        wp_enqueue_style( 'cs-admin-css', CS_URL . 'assets/css/admin.css', [], CS_VER );
    }
}
add_action( 'admin_enqueue_scripts', 'cs_enqueue_admin_assets' );

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
        __( 'Display Settings', 'carousel-hero-slider' ),
        '__return_false',
        'carousel-hero-slider'
    );

    add_settings_section(
        'cs_wbk_hero_slider_animation',
        __( 'Animation Settings', 'carousel-hero-slider' ),
        'cs_render_animation_section_text',
        'carousel-hero-slider'
    );

    add_settings_section(
        'cs_wbk_hero_slider_visibility',
        __( 'Content Visibility', 'carousel-hero-slider' ),
        'cs_render_visibility_section_text',
        'carousel-hero-slider'
    );

    $display_fields = [
        'height' => __( 'Slider Height (px)', 'carousel-hero-slider' ),
        'timer'  => __( 'Slide Timer (ms)', 'carousel-hero-slider' ),
    ];

    foreach ( $display_fields as $key => $label ) {
        add_settings_field(
            'cs_' . $key,
            $label,
            'cs_render_number_field',
            'carousel-hero-slider',
            'cs_wbk_hero_slider_main',
            [
                'key' => $key,
            ]
        );
    }

    add_settings_field(
        'cs_animation_style',
        __( 'Animation Style', 'carousel-hero-slider' ),
        'cs_render_animation_style_field',
        'carousel-hero-slider',
        'cs_wbk_hero_slider_animation'
    );

    $visibility_fields = [
        'show_title'   => __( 'Show Slide Title', 'carousel-hero-slider' ),
        'show_caption' => __( 'Show Caption Text', 'carousel-hero-slider' ),
        'show_button'  => __( 'Show Slide Button', 'carousel-hero-slider' ),
    ];

    foreach ( $visibility_fields as $key => $label ) {
        add_settings_field(
            'cs_' . $key,
            $label,
            'cs_render_toggle_field',
            'carousel-hero-slider',
            'cs_wbk_hero_slider_visibility',
            [
                'key' => $key,
            ]
        );
    }
}
add_action( 'admin_init', 'cs_register_settings' );

/**
 * Render helper text for animation section.
 */
function cs_render_animation_section_text() {
    echo '<p>' . esc_html__( 'Choose one animation style and control how frequently slides change automatically.', 'carousel-hero-slider' ) . '</p>';
}

/**
 * Render helper text for visibility section.
 */
function cs_render_visibility_section_text() {
    echo '<p>' . esc_html__( 'Enable or disable slide title, caption, and button output on the frontend.', 'carousel-hero-slider' ) . '</p>';
}

/**
 * Add metaboxes for slide fields.
 */
function cs_add_slide_metaboxes() {
    add_meta_box(
        'cs-slide-content-metabox',
        __( 'Slide Content', 'carousel-hero-slider' ),
        'cs_render_slide_content_metabox',
        'cs_hero_slide',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'cs_add_slide_metaboxes' );

/**
 * Render slide meta fields.
 *
 * @param WP_Post $post Post object.
 */
function cs_render_slide_content_metabox( $post ) {
    wp_nonce_field( 'cs_save_slide_meta', 'cs_slide_meta_nonce' );

    $caption      = (string) get_post_meta( $post->ID, '_cs_slide_caption', true );
    $button_label = (string) get_post_meta( $post->ID, '_cs_slide_button_label', true );
    $button_link  = (string) get_post_meta( $post->ID, '_cs_slide_button_link', true );
    ?>
    <p>
        <label for="cs-slide-caption"><strong><?php esc_html_e( 'Caption', 'carousel-hero-slider' ); ?></strong></label><br />
        <textarea id="cs-slide-caption" class="widefat" rows="3" name="cs_slide_caption"><?php echo esc_textarea( $caption ); ?></textarea>
    </p>

    <p>
        <label for="cs-slide-button-label"><strong><?php esc_html_e( 'Button Label', 'carousel-hero-slider' ); ?></strong></label><br />
        <input id="cs-slide-button-label" class="regular-text" type="text" name="cs_slide_button_label" value="<?php echo esc_attr( $button_label ); ?>" maxlength="40" />
    </p>

    <p>
        <label for="cs-slide-button-link"><strong><?php esc_html_e( 'Button Link', 'carousel-hero-slider' ); ?></strong></label><br />
        <input id="cs-slide-button-link" class="widefat" type="url" name="cs_slide_button_link" value="<?php echo esc_attr( $button_link ); ?>" placeholder="https://example.com/page" />
    </p>

    <p class="description">
        <?php esc_html_e( 'Tip: Use the slide title field for the heading and set a Featured Image for the background image.', 'carousel-hero-slider' ); ?>
    </p>
    <?php
}

/**
 * Save slide meta fields.
 *
 * @param int $post_id Post ID.
 */
function cs_save_slide_meta( $post_id ) {
    if ( ! isset( $_POST['cs_slide_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cs_slide_meta_nonce'] ) ), 'cs_save_slide_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( 'cs_hero_slide' !== get_post_type( $post_id ) ) {
        return;
    }

    $caption      = isset( $_POST['cs_slide_caption'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cs_slide_caption'] ) ) : '';
    $button_label = isset( $_POST['cs_slide_button_label'] ) ? sanitize_text_field( wp_unslash( $_POST['cs_slide_button_label'] ) ) : '';
    $button_link  = isset( $_POST['cs_slide_button_link'] ) ? esc_url_raw( wp_unslash( $_POST['cs_slide_button_link'] ) ) : '';

    update_post_meta( $post_id, '_cs_slide_caption', $caption );
    update_post_meta( $post_id, '_cs_slide_button_label', $button_label );
    update_post_meta( $post_id, '_cs_slide_button_link', $button_link );
}
add_action( 'save_post', 'cs_save_slide_meta' );

/**
 * Add duplicate row action to slide posts.
 *
 * @param array<string, string> $actions Existing row actions.
 * @param WP_Post               $post Current post.
 * @return array<string, string>
 */
function cs_add_duplicate_slide_action( $actions, $post ) {
    if ( 'cs_hero_slide' !== $post->post_type || ! current_user_can( 'edit_posts' ) ) {
        return $actions;
    }

    $duplicate_url = wp_nonce_url(
        add_query_arg(
            [
                'action' => 'cs_duplicate_slide',
                'post'   => $post->ID,
            ],
            admin_url( 'admin.php' )
        ),
        'cs_duplicate_slide_' . $post->ID
    );

    $actions['cs_duplicate_slide'] = '<a href="' . esc_url( $duplicate_url ) . '">' . esc_html__( 'Duplicate', 'carousel-hero-slider' ) . '</a>';

    return $actions;
}
add_filter( 'post_row_actions', 'cs_add_duplicate_slide_action', 10, 2 );

/**
 * Handle duplicate action.
 */
function cs_handle_duplicate_slide_action() {
    if ( ! is_admin() || ! isset( $_GET['action'] ) || 'cs_duplicate_slide' !== $_GET['action'] ) {
        return;
    }

    $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

    if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
        wp_die( esc_html__( 'You are not allowed to duplicate this slide.', 'carousel-hero-slider' ) );
    }

    check_admin_referer( 'cs_duplicate_slide_' . $post_id );

    $post = get_post( $post_id );

    if ( ! $post || 'cs_hero_slide' !== $post->post_type ) {
        wp_die( esc_html__( 'Invalid slide for duplication.', 'carousel-hero-slider' ) );
    }

    $new_post_id = wp_insert_post(
        [
            'post_type'   => 'cs_hero_slide',
            'post_status' => 'draft',
            'post_title'  => $post->post_title . ' (Copy)',
            'menu_order'  => (int) $post->menu_order,
        ]
    );

    if ( ! $new_post_id || is_wp_error( $new_post_id ) ) {
        wp_die( esc_html__( 'Unable to duplicate slide.', 'carousel-hero-slider' ) );
    }

    $meta_keys = [ '_cs_slide_caption', '_cs_slide_button_label', '_cs_slide_button_link' ];

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
                'post_type'  => 'cs_hero_slide',
                'duplicated' => 1,
            ],
            admin_url( 'edit.php' )
        )
    );
    exit;
}
add_action( 'admin_init', 'cs_handle_duplicate_slide_action' );

/**
 * Admin notice for duplicated slide.
 */
function cs_duplicate_slide_notice() {
    if ( ! isset( $_GET['post_type'], $_GET['duplicated'] ) || 'cs_hero_slide' !== $_GET['post_type'] || '1' !== $_GET['duplicated'] ) {
        return;
    }

    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Slide duplicated successfully as draft.', 'carousel-hero-slider' ) . '</p></div>';
}
add_action( 'admin_notices', 'cs_duplicate_slide_notice' );

/**
 * Sanitize settings input.
 *
 * @param array<string, mixed> $input Raw input.
 * @return array<string, mixed>
 */
function cs_sanitize_wbk_hero_slider_settings( $input ) {
    $defaults = cs_get_wbk_hero_slider_settings();

    $animation_style = isset( $input['animation_style'] ) ? sanitize_key( $input['animation_style'] ) : $defaults['animation_style'];

    if ( ! in_array( $animation_style, [ 'default', 'animation_1', 'animation_2', 'animation_3', 'animation_4' ], true ) ) {
        $animation_style = 'default';
    }

    return [
        'height'          => max( 220, absint( $input['height'] ?? $defaults['height'] ) ),
        'timer'           => max( 2000, absint( $input['timer'] ?? $defaults['timer'] ) ),
        'animation_style' => $animation_style,
        'show_title'      => empty( $input['show_title'] ) ? 0 : 1,
        'show_caption'    => empty( $input['show_caption'] ) ? 0 : 1,
        'show_button'     => empty( $input['show_button'] ) ? 0 : 1,
    ];
}

/**
 * Render one numeric settings field.
 *
 * @param array<string, string> $args Field args.
 */
function cs_render_number_field( $args ) {
    $settings = cs_get_wbk_hero_slider_settings();
    $key      = $args['key'];
    $value    = $settings[ $key ] ?? '';
    ?>
    <input
        type="number"
        class="regular-text"
        name="cs_wbk_hero_slider_settings[<?php echo esc_attr( $key ); ?>]"
        value="<?php echo esc_attr( (string) $value ); ?>"
        min="1"
        step="1"
    />
    <?php
}

/**
 * Render animation style selection radios.
 */
function cs_render_animation_style_field() {
    $settings      = cs_get_wbk_hero_slider_settings();
    $current_style = isset( $settings['animation_style'] ) ? $settings['animation_style'] : 'default';
    $options       = [
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
    ];

    foreach ( $options as $value => $option ) {
        ?>
        <label style="display:block;margin-bottom:10px;">
            <input
                type="radio"
                name="cs_wbk_hero_slider_settings[animation_style]"
                value="<?php echo esc_attr( $value ); ?>"
                <?php checked( $current_style, $value ); ?>
            />
            <strong><?php echo esc_html( $option['label'] ); ?></strong><br />
            <span class="description"><?php echo esc_html( $option['description'] ); ?></span>
        </label>
        <?php
    }
}

/**
 * Render one visibility toggle field.
 *
 * @param array<string, string> $args Field args.
 */
function cs_render_toggle_field( $args ) {
    $settings = cs_get_wbk_hero_slider_settings();
    $key      = $args['key'];
    $checked  = ! empty( $settings[ $key ] );
    ?>
    <label for="cs-<?php echo esc_attr( $key ); ?>">
        <input
            id="cs-<?php echo esc_attr( $key ); ?>"
            type="checkbox"
            name="cs_wbk_hero_slider_settings[<?php echo esc_attr( $key ); ?>]"
            value="1"
            <?php checked( $checked ); ?>
        />
        <?php esc_html_e( 'Enable', 'carousel-hero-slider' ); ?>
    </label>
    <?php
}

/**
 * Render settings page.
 */
function cs_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    ?>
    <div class="wrap cs-admin-wrap">
        <h1><?php esc_html_e( 'Carousel Hero Slider Settings', 'carousel-hero-slider' ); ?></h1>
        <p><?php esc_html_e( 'Manage global slider behavior, animation style, timer, and content visibility.', 'carousel-hero-slider' ); ?></p>

        <p>
            <a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=cs_hero_slide' ) ); ?>">
                <?php esc_html_e( 'Add New Slide', 'carousel-hero-slider' ); ?>
            </a>
            <a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=cs_hero_slide' ) ); ?>">
                <?php esc_html_e( 'Manage Slides', 'carousel-hero-slider' ); ?>
            </a>
        </p>

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
        <p><?php esc_html_e( 'Optional overrides:', 'carousel-hero-slider' ); ?> <code>[wbk_hero_slider height="460" timer="4500"]</code></p>
    </div>
    <?php
}
