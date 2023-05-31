<?php
/*
Plugin Name: All-in-One AI
Plugin URI: https://github.com/andbalashov/wpai
Description: All-in-One AI is a powerful WordPress plugin that integrates artificial intelligence capabilities into your WordPress website.
Version: 1.0
Author: Andrii Balashov
Author URI: https://github.com/andbalashov
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: all-in-one-ai
*/

defined('ABSPATH') or die('No script kiddies please!');

// Define the path to the plugin
define('AIOAI_PATH', dirname(__FILE__));

// Require the autoload file
require_once(AIOAI_PATH . '/vendor/autoload.php');

// Enqueue custom CSS file
function aioai_enqueue_styles() {   
   // Enqueue the custom CSS file
   wp_enqueue_style('aioai-custom-styles', plugins_url('assets/custom-styles.css', __FILE__), array(), '1.0');
}
add_action('admin_enqueue_scripts', 'aioai_enqueue_styles');

// Include admin page file
require_once(AIOAI_PATH . '/admin/admin-page.php');

// Include functions file
require_once(AIOAI_PATH . '/includes/functions.php');