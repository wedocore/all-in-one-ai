<?php
/*
Plugin Name: All-in-One AI
Plugin URI: https://github.com/andbalashov/wpai
Description: WordPress AI (WPAI) is a powerful WordPress plugin that integrates artificial intelligence capabilities into your WordPress website.
Version: 1.0
Author: Andrii Balashov
Author URI: https://github.com/andbalashov
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: all-in-one-ai
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