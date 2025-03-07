<?php
/**
 * Admin area display for managing resellers.
 *
 * @since      1.0.0
 * @package    Reseller_Access
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <div class="reseller-access-admin">
        <div class="reseller-container">
            <div class="reseller-form-container">
                <h2><?php _e( 'Add New Reseller', 'reseller-access' ); ?></h2>
                
                <form id="create-reseller-form" class="reseller-form">
                    <div class="form-group">
                        <label for="username"><?php _e( 'Username', 'reseller-access' ); ?> <span class="required">*</span></label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><?php _e( 'Email', 'reseller-access' ); ?> <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="first_name"><?php _e( 'First Name', 'reseller-access' ); ?></label>
                        <input type="text" id="first_name" name="first_name">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name"><?php _e( 'Last Name', 'reseller-access' ); ?></label>
                        <input type="text" id="last_name" name="last_name">
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><?php _e( 'Password', 'reseller-access' ); ?></label>
                        <input type="password" id="password" name="password">
                        <p class="description"><?php _e( 'Leave blank to auto-generate', 'reseller-access' ); ?></p>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="send_notification" value="yes">
                            <?php _e( 'Send user notification', 'reseller-access' ); ?>
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="button button-primary"><?php _e( 'Create Reseller', 'reseller-access' ); ?></button>
                        <div id="create-reseller-message" class="reseller-message"></div>
                    </div>
                </form>
            </div>
            
            <div class="reseller-list-container">
                <h2><?php _e( 'Existing Resellers', 'reseller-access' ); ?></h2>
                
                <div id="resellers-table-container">
                    <table class="wp-list-table widefat fixed striped resellers-table">
                        <thead>
                            <tr>
                                <th><?php _e( 'Username', 'reseller-access' ); ?></th>
                                <th><?php _e( 'Name', 'reseller-access' ); ?></th>
                                <th><?php _e( 'Email', 'reseller-access' ); ?></th>
                                <th><?php _e( 'Registered', 'reseller-access' ); ?></th>
                                <th><?php _e( 'Actions', 'reseller-access' ); ?></th>
                            </tr>
                        </thead>
                        <tbody id="resellers-list">
                            <tr>
                                <td colspan="5"><?php _e( 'Loading resellers...', 'reseller-access' ); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
