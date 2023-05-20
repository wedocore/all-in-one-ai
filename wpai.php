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

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'WPAI_PATH', dirname( __FILE__ ) );

require_once( WPAI_PATH . '/vendor/autoload.php' );

// Register the menu.
add_action( 'admin_menu', 'wpai_plugin_menu_func' );
function wpai_plugin_menu_func() {
   add_submenu_page( 'options-general.php',
      'WPAI',
      'WPAI',
      'manage_options',
      'wpai',
      'wpai_plugin_options'
   );
}

// Print the markup for the page
function wpai_plugin_options() {
   if ( !current_user_can( 'manage_options' ) )  {
      wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
   }
   ?>
      <form method='post' action='<?php echo admin_url( 'admin-post.php'); ?>'>
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

   // $yourApiKey = get_option('API_KEY');
   // $client = OpenAI::client($yourApiKey);

   // $result = $client->completions()->create([
   //    'model' => 'text-davinci-003',
   //    'prompt' => 'PHP is',
   // ]);

   // echo $result['choices'][0]['text'];
}

add_action( 'admin_post_update_wpai_settings', 'wpai_handle_save' );
function wpai_handle_save() {
   $API_KEY = (!empty($_POST['API_KEY'])) ? $_POST['API_KEY'] : NULL;

   update_option( 'API_KEY', $API_KEY, TRUE );

   $redirect_url = get_bloginfo('url') . '/wp-admin/options-general.php?page=wpai&status=success';
   header('Location: '.$redirect_url);
   exit;
}
