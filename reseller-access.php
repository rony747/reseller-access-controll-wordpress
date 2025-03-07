<?php
/**
 * Plugin Name: Reseller Access Manager
 * Plugin URI: https://yourwebsite.com/plugins/reseller-access
 * Description: Create and manage Reseller user type with content access control.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Text Domain: reseller-access
 * Domain Path: /languages
 * License: GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define plugin constants
define( 'RESELLER_ACCESS_VERSION', '1.0.0' );
define( 'RESELLER_ACCESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RESELLER_ACCESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include required files
require_once RESELLER_ACCESS_PLUGIN_DIR . 'includes/class-reseller-access.php';

// Activation and deactivation hooks
register_activation_hook( __FILE__, 'reseller_access_activate' );
register_deactivation_hook( __FILE__, 'reseller_access_deactivate' );

/**
 * Plugin activation function.
 * This function runs when the plugin is activated.
 */
function reseller_access_activate() {
    // Add the Reseller role with capabilities
    add_role(
        'reseller',
        __( 'Reseller', 'reseller-access' ),
        array(
            'read'                   => true,
            'edit_posts'             => false,
            'delete_posts'           => false,
            'publish_posts'          => false,
            'upload_files'           => false,
            'access_reseller_content' => true, // Custom capability for resellers
        )
    );
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

/**
 * Plugin deactivation function.
 * This function runs when the plugin is deactivated.
 */
function reseller_access_deactivate() {
    // We don't remove the Reseller role on deactivation to prevent data loss
    // It will be removed on uninstall
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

/**
 * Initialize the plugin.
 */
function run_reseller_access() {
    $plugin = new Reseller_Access();
    $plugin->run();
}

// Run the plugin
run_reseller_access();
