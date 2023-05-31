<?php
// admin-page.php

// Add plugin menu page
add_action('admin_menu', 'aioai_plugin_menu_func');
function aioai_plugin_menu_func()
{
    add_submenu_page(
        'options-general.php',
        'All-in-One AI',
        'All-in-One AI',
        'manage_options',
        'all-in-one-ai',
        'aioai_plugin_options'
    );
}

// Render the plugin options page
function aioai_plugin_options() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    $api_key = get_option( 'API_KEY', '' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

        <?php settings_errors(); ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="update_aioai_settings" />
            <?php wp_nonce_field( 'update_aioai_settings', 'aioai_settings_nonce' ); ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="openai-api-key"><?php _e( 'OpenAI API Key', 'aioai' ); ?></label>
                        </th>
                        <td>
                            <input name="API_KEY" type="text" id="openai-api-key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text">
                            <p class="description"><?php printf( __( 'Enter the <a href="%s" target="_blank">OpenAI Secret Key</a>.', 'aioai' ), 'https://platform.openai.com/account/api-keys' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button( __( 'Save Changes', 'aioai' ) ); ?>
        </form>
    </div>
    <?php
}

// Handle saving of plugin options
add_action( 'admin_post_update_aioai_settings', 'aioai_handle_save' );
function aioai_handle_save() {
    if ( ! isset( $_POST['aioai_settings_nonce'] ) || ! wp_verify_nonce( $_POST['aioai_settings_nonce'], 'update_aioai_settings' ) ) {
        wp_die( __( 'Invalid nonce specified.', 'aioai' ) );
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    if ( isset( $_POST['API_KEY'] ) ) {
        $api_key = sanitize_text_field( $_POST['API_KEY'] );

        update_option( 'API_KEY', $api_key );

        $redirect_url = add_query_arg( 'status', 'success', admin_url( 'options-general.php?page=all-in-one-ai' ) );
        wp_safe_redirect( $redirect_url );
        exit;
    } else {
        // Handle empty or invalid input
        add_settings_error( 'aioai_settings', 'empty_api_key', __( 'Error: Incorrect field value. Please correct it.', 'aioai' ), 'error' );
        $redirect_url = add_query_arg( 'status', 'error', admin_url( 'options-general.php?page=all-in-one-ai' ) );
        wp_safe_redirect( $redirect_url );
        exit;
    }
}

// Add a custom button to generate content on the post editing page
function aioai_add_generate_content_button()
{
    global $post;
    $post_type = get_post_type($post);

    if ($post_type === 'post') { // Customize this condition based on your post type
        ?>
        <div class="misc-pub-section">
            <button type="submit" class="button button-primary" name="generate_content">Generate Content</button>
        </div>
        <?php
    }
}
add_action('post_submitbox_misc_actions', 'aioai_add_generate_content_button');

// Add meta box for meta description
function aioai_add_meta_description_meta_box()
{
    $screens = array('post', 'page'); // Customize this array to include the post types you want to add the meta box to

    foreach ($screens as $screen) {
        add_meta_box(
            'meta_description_meta_box',
            'Meta Description',
            'aioai_render_meta_description_meta_box',
            $screen,
            'advanced',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'aioai_add_meta_description_meta_box');

// Render meta box content
function aioai_render_meta_description_meta_box($post)
{
    $meta_description = get_post_meta($post->ID, 'meta_description', true);
    ?>
    <div class="meta-description-meta-box">
        <label for="meta_description">Meta Description:</label>
        <textarea id="meta_description" name="meta_description" rows="4"><?php echo esc_textarea($meta_description); ?></textarea>
        <button type="submit" class="button button-primary" name="generate_meta_description">Generate Meta Description</button>
    </div>
    <?php
}

// Save meta description
function aioai_save_meta_description($post_id)
{
    if (isset($_POST['meta_description'])) {
        $meta_description = sanitize_textarea_field($_POST['meta_description']);
        update_post_meta($post_id, 'meta_description', $meta_description);
    }
}
add_action('save_post', 'aioai_save_meta_description');