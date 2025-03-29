<?php
/*
Plugin Name: Secure Admin Email Change
Plugin URI: https://wordpress.org/plugins/secure-admin-email-change/
Description: Enables admins to update the site’s admin email without email confirmation, making it easier for sites without email-sending capabilities.
Version: 1.0
Author: Priyank Sukhadiya
Author URI: https://profiles.wordpress.org/priyanksukhadiya/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

namespace SECUADEM;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Include necessary files
require_once plugin_dir_path( __FILE__ ) . 'includes/class-saec-plugin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-notice.php';

// Initialize the plugin
$SECUADEMPlugin = new SECUADEMPlugin();
$SECUADEMPlugin->secuadem_run();
