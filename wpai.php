<?php
/*
Plugin Name: WPAI
Plugin URI: #
Description: OpanAI API for Wordpress
Version: 1.0
Author: WPAI Team
Author URI: #
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wpai
*/

defined('ABSPATH') or die('No script kiddies please!');

// Define the path to the plugin
define('WPAI_PATH', dirname(__FILE__));

// Require the autoload file
require_once(WPAI_PATH . '/vendor/autoload.php');

use Orhanerday\OpenAi\OpenAi;

// Add plugin menu page
add_action('admin_menu', 'wpai_plugin_menu_func');
function wpai_plugin_menu_func() {
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
function wpai_plugin_options() {
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
                     <label for='openai-api-key'><?php _e('OpenAI API Key', 'wpai'); ?></label>
                  </th>
                  <td>
                     <input name='API_KEY' type='text' id='openai-api-key' value='<?php echo get_option('API_KEY'); ?>' class='regular-text'>
                     <p class='description'>Enter the <a href='https://platform.openai.com/account/api-keys' target="_blank">OpanAI Secret Key</a>.</p>
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
function wpai_handle_save() {
   $API_KEY = (!empty($_POST['API_KEY'])) ? $_POST['API_KEY'] : NULL;

   update_option('API_KEY', $API_KEY, true);

   $redirect_url = get_bloginfo('url') . '/wp-admin/options-general.php?page=wpai&status=success';
   header('Location: '.$redirect_url);
   exit;
}

// Function to generate content for a WordPress post using ChatGPT
function generate_post_content($title) {
   // Set up OpenAI API credentials
   $open_ai_key = get_option('API_KEY');
   $open_ai = new OpenAI($open_ai_key);

   $chat = $open_ai->chat([
      'model' => 'gpt-3.5-turbo',
      'messages' => [
         [
            'role' => 'system',
            'content' => 'You are a WordPress user requesting a blog post about a specific topic.',
         ],
         [
            'role' => 'user',
            'content' => 'Generate a new blog post about ' . $title,
         ],
      ],
   ]);

   // Decode response
   $response = json_decode($chat, true);

   // Return the generated content
   return $response['choices'][0]['message']['content'];
}

// Hook into the 'wp_insert_post_data' action to automatically generate content for a new post
function auto_generate_post_content($data, $postarr) {
   if (isset($_POST['generate_content'])) {
      $title = !empty($postarr['post_title']) ? $postarr['post_title'] : 'topic';
      $data['post_content'] = generate_post_content($title);
   }
   return $data;
}
add_filter('wp_insert_post_data', 'auto_generate_post_content', 10, 2);

// Add a custom button to generate content on the post editing page
function add_generate_content_button() {
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