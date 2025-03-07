<?php
/**
 * Frontend dashboard template.
 *
 * @since      1.0.0
 * @package    Reseller_Access
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Get user data
$user_id = get_current_user_id();
$user_data = get_userdata($user_id);
?>

<div class="reseller-dashboard-wrapper">
    <div class="reseller-dashboard">
        <h2><?php _e('Reseller Dashboard', 'reseller-access'); ?></h2>
        
        <div class="dashboard-content">
            <div class="dashboard-section">
                <h3><?php _e('Profile Information', 'reseller-access'); ?></h3>
                
                <form id="reseller-profile-form" class="reseller-form">
                    <div class="form-group">
                        <label for="first_name"><?php _e('First Name', 'reseller-access'); ?></label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo esc_attr($user_data->first_name); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name"><?php _e('Last Name', 'reseller-access'); ?></label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo esc_attr($user_data->last_name); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><?php _e('Email Address', 'reseller-access'); ?> <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo esc_attr($user_data->user_email); ?>" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="button button-primary"><?php _e('Update Profile', 'reseller-access'); ?></button>
                        <div id="profile-update-message" class="update-message"></div>
                    </div>
                </form>
            </div>
            
            <div class="dashboard-section">
                <h3><?php _e('Change Password', 'reseller-access'); ?></h3>
                
                <form id="reseller-password-form" class="reseller-form">
                    <div class="form-group">
                        <label for="current_password"><?php _e('Current Password', 'reseller-access'); ?> <span class="required">*</span></label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password"><?php _e('New Password', 'reseller-access'); ?> <span class="required">*</span></label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password"><?php _e('Confirm New Password', 'reseller-access'); ?> <span class="required">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="button button-primary"><?php _e('Update Password', 'reseller-access'); ?></button>
                        <div id="password-update-message" class="update-message"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
