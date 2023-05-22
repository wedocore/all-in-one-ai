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

// Enqueue custom CSS file
function wpai_enqueue_styles() {   
   // Enqueue the custom CSS file
   wp_enqueue_style('wpai-custom-styles', plugins_url('assets/custom-styles.css', __FILE__), array(), '1.0');
}
add_action('admin_enqueue_scripts', 'wpai_enqueue_styles');

// Include admin page file
require_once(WPAI_PATH . '/admin/admin-page.php');

// Include functions file
require_once(WPAI_PATH . '/includes/functions.php');