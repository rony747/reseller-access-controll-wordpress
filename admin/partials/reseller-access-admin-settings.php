<?php
/**
 * Admin area display for plugin settings.
 *
 * @since      1.0.0
 * @package    Reseller_Access
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get saved settings
$redirect_page = get_option( 'reseller_access_redirect_page', 0 );
$access_message = get_option( 'reseller_access_message', __( 'This content is restricted to Resellers only.', 'reseller-access' ) );
$enable_frontend_dashboard = get_option( 'reseller_access_enable_frontend_dashboard', 'yes' );
$dashboard_page = get_option( 'reseller_access_dashboard_page', 0 );
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?> <?php _e( 'Settings', 'reseller-access' ); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields( 'reseller_access_options' );
        do_settings_sections( 'reseller_access_settings' );
        ?>
        
        <h2><?php _e( 'Content Access Settings', 'reseller-access' ); ?></h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Redirect Page', 'reseller-access' ); ?></th>
                <td>
                    <?php
                    wp_dropdown_pages( array(
                        'name' => 'reseller_access_redirect_page',
                        'echo' => 1,
                        'show_option_none' => __( '— Select —', 'reseller-access' ),
                        'option_none_value' => '0',
                        'selected' => $redirect_page
                    ) );
                    ?>
                    <p class="description"><?php _e( 'Select a page to redirect non-resellers when trying to access restricted content. Leave blank to show the access message instead.', 'reseller-access' ); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Access Denied Message', 'reseller-access' ); ?></th>
                <td>
                    <textarea name="reseller_access_message" rows="5" cols="50"><?php echo esc_textarea( $access_message ); ?></textarea>
                    <p class="description"><?php _e( 'Message to display when a non-reseller tries to access restricted content and no redirect page is set.', 'reseller-access' ); ?></p>
                </td>
            </tr>
        </table>
        
        <h2><?php _e( 'Frontend Dashboard Settings', 'reseller-access' ); ?></h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Enable Frontend Dashboard', 'reseller-access' ); ?></th>
                <td>
                    <select name="reseller_access_enable_frontend_dashboard">
                        <option value="yes" <?php selected( $enable_frontend_dashboard, 'yes' ); ?>><?php _e( 'Enabled', 'reseller-access' ); ?></option>
                        <option value="no" <?php selected( $enable_frontend_dashboard, 'no' ); ?>><?php _e( 'Disabled', 'reseller-access' ); ?></option>
                    </select>
                    <p class="description"><?php _e( 'Enable or disable the frontend dashboard for Resellers.', 'reseller-access' ); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Dashboard Page', 'reseller-access' ); ?></th>
                <td>
                    <?php
                    wp_dropdown_pages( array(
                        'name' => 'reseller_access_dashboard_page',
                        'echo' => 1,
                        'show_option_none' => __( '— Select —', 'reseller-access' ),
                        'option_none_value' => '0',
                        'selected' => $dashboard_page
                    ) );
                    ?>
                    <p class="description"><?php _e( 'Select a page to use for the Reseller frontend dashboard. Create a new page with any title, and this plugin will replace the content with the dashboard interface.', 'reseller-access' ); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
</div>
