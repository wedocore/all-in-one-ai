<?php
// admin-page.php

// Add plugin menu page
add_action('admin_menu', 'wpai_plugin_menu_func');
function wpai_plugin_menu_func()
{
    add_submenu_page(
        'options-general.php',
        'WPAI',
        'WPAI',
        'manage_options',
        'wpai',
        'wpai_plugin_options'
    );
}

// Render the plugin options page
function wpai_plugin_options()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>
    <form method='post' action='<?php echo admin_url('admin-post.php'); ?>'>
        <input type='hidden' name='action' value='update_wpai_settings' />
        <div class='wrap'>
            <h1>WPAI Settings</h1>
            <table class='form-table' role='presentation'>
                <tbody>
                    <tr>
                        <th scope='row'>
                            <label for='openai-api-key'>
                                <?php _e('OpenAI API Key', 'wpai'); ?>
                            </label>
                        </th>
                        <td>
                            <input name='API_KEY' type='text' id='openai-api-key'
                                value='<?php echo get_option('API_KEY'); ?>' class='regular-text'>
                            <p class='description'>Enter the <a href='https://platform.openai.com/account/api-keys'
                                    target="_blank">OpanAI Secret Key</a>.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input class='button button-primary' type='submit' value='<?php _e('Save Changes', 'wpai'); ?>' />
        </div>
    </form>
    <?php
}

// Handle saving of plugin options
add_action('admin_post_update_wpai_settings', 'wpai_handle_save');
function wpai_handle_save()
{
    $API_KEY = (!empty($_POST['API_KEY'])) ? $_POST['API_KEY'] : NULL;

    update_option('API_KEY', $API_KEY, true);

    $redirect_url = get_bloginfo('url') . '/wp-admin/options-general.php?page=wpai&status=success';
    header('Location: ' . $redirect_url);
    exit;
}

// Add a custom button to generate content on the post editing page
function add_generate_content_button()
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
add_action('post_submitbox_misc_actions', 'add_generate_content_button');

// Add meta box for meta description
function add_meta_description_meta_box()
{
    $screens = array('post', 'page'); // Customize this array to include the post types you want to add the meta box to

    foreach ($screens as $screen) {
        add_meta_box(
            'meta_description_meta_box',
            'Meta Description',
            'render_meta_description_meta_box',
            $screen,
            'advanced',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'add_meta_description_meta_box');

// Render meta box content
function render_meta_description_meta_box($post)
{
    $meta_description = get_post_meta($post->ID, 'meta_description', true);
    ?>
    <div class="meta-description-meta-box">
        <label for="meta_description">Meta Description:</label>
        <textarea id="meta_description" name="meta_description"
            rows="4"><?php echo esc_textarea($meta_description); ?></textarea>
        <button type="submit" class="button button-primary" name="generate_meta_description">Generate Meta
            Description</button>
    </div>
    <?php
}

// Save meta description
function save_meta_description($post_id)
{
    if (isset($_POST['meta_description'])) {
        $meta_description = sanitize_textarea_field($_POST['meta_description']);
        update_post_meta($post_id, 'meta_description', $meta_description);
    }
}
add_action('save_post', 'save_meta_description');