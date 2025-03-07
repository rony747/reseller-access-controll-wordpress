<?php
/**
 * The frontend dashboard functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Reseller_Access
 */

class Reseller_Access_Dashboard {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Constructor code
    }

    /**
     * Register the stylesheets for the frontend dashboard.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        // Only enqueue on the dashboard page
        if ($this->is_dashboard_page()) {
            wp_enqueue_style('reseller-access-dashboard', RESELLER_ACCESS_PLUGIN_URL . 'public/css/reseller-access-dashboard.css', array(), RESELLER_ACCESS_VERSION, 'all');
        }
    }

    /**
     * Register the JavaScript for the frontend dashboard.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        // Only enqueue on the dashboard page
        if ($this->is_dashboard_page()) {
            wp_enqueue_script('reseller-access-dashboard', RESELLER_ACCESS_PLUGIN_URL . 'public/js/reseller-access-dashboard.js', array('jquery'), RESELLER_ACCESS_VERSION, false);
            
            // Add the ajax url to our script
            wp_localize_script('reseller-access-dashboard', 'reseller_access_dashboard', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('reseller_access_dashboard_nonce'),
                'messages' => array(
                    'profile_updated' => __('Profile updated successfully.', 'reseller-access'),
                    'password_updated' => __('Password updated successfully.', 'reseller-access'),
                    'error' => __('An error occurred. Please try again.', 'reseller-access'),
                    'password_mismatch' => __('Passwords do not match.', 'reseller-access'),
                    'password_weak' => __('Password is too weak.', 'reseller-access'),
                    'current_password_invalid' => __('Current password is invalid.', 'reseller-access'),
                ),
            ));
        }
    }

    /**
     * Display the frontend dashboard content.
     *
     * @since    1.0.0
     * @param    string    $content    The content of the post.
     * @return   string                The filtered content.
     */
    public function display_dashboard($content) {
        global $post;
        
        // Only modify the content on the dashboard page
        if (!$this->is_dashboard_page()) {
            return $content;
        }
        
        // Check if user has appropriate permissions
        if (!$this->is_reseller()) {
            return $content . '<p>You must be logged in as a reseller or administrator to view this dashboard.</p>';
        }
        
        // Get the current user
        $user = wp_get_current_user();
        
        // Determine the template path using reliable methods
        if (!defined('RESELLER_ACCESS_PLUGIN_DIR') || empty(RESELLER_ACCESS_PLUGIN_DIR)) {
            $plugin_path = plugin_dir_path(dirname(dirname(__FILE__)));
        } else {
            $plugin_path = RESELLER_ACCESS_PLUGIN_DIR;
        }
        
        $template_path = $plugin_path . 'public/partials/reseller-access-dashboard-display.php';
        $alt_template_path = dirname(dirname(__FILE__)) . '/partials/reseller-access-dashboard-display.php';
        
        // Try primary path first, then fallback to alternative path
        if (file_exists($template_path)) {
            ob_start();
            include $template_path;
            $dashboard_content = ob_get_clean();
            return $dashboard_content;
        } else if (file_exists($alt_template_path)) {
            ob_start();
            include $alt_template_path;
            $dashboard_content = ob_get_clean();
            return $dashboard_content;
        } else {
            // If all else fails, generate simple dashboard content
            $user_id = get_current_user_id();
            $user_data = get_userdata($user_id);
            
            $simple_dashboard = '<div class="reseller-dashboard-wrapper">';
            $simple_dashboard .= '<div class="reseller-dashboard">';
            $simple_dashboard .= '<h2>Reseller Dashboard</h2>';
            $simple_dashboard .= '<div class="dashboard-content">';
            $simple_dashboard .= '<div class="dashboard-section">';
            $simple_dashboard .= '<h3>Profile Information</h3>';
            $simple_dashboard .= '<p>First Name: ' . esc_html($user_data->first_name) . '</p>';
            $simple_dashboard .= '<p>Last Name: ' . esc_html($user_data->last_name) . '</p>';
            $simple_dashboard .= '<p>Email: ' . esc_html($user_data->user_email) . '</p>';
            $simple_dashboard .= '</div></div></div></div>';
            
            return $simple_dashboard;
        }
    }

    /**
     * Check if the current page is the dashboard page.
     *
     * @since    1.0.0
     * @return   boolean    True if current page is the dashboard page, false otherwise.
     */
    private function is_dashboard_page() {
        global $post;
        
        if (!$post) {
            return false;
        }
        
        $dashboard_page_id = get_option('reseller_access_dashboard_page', 0);
        
        // Convert to integers for strict comparison
        $post_id = (int) $post->ID;
        $dashboard_page_id = (int) $dashboard_page_id;
        
        // More detailed debugging if admin
        if (current_user_can('manage_options')) {
            error_log('Dashboard page check: Post ID = ' . $post_id . ', Dashboard Page ID = ' . $dashboard_page_id);
        }
        
        return $post_id === $dashboard_page_id && $dashboard_page_id > 0;
    }

    /**
     * Check if the current user is a reseller or administrator.
     *
     * @since    1.0.0
     * @return   boolean    True if user is a reseller or administrator, false otherwise.
     */
    private function is_reseller() {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return false;
        }
        
        // Also allow administrators to view the dashboard
        if (current_user_can('manage_options')) {
            return true;
        }
        
        // Check if user has the reseller role
        $user = wp_get_current_user();
        return in_array('reseller', (array) $user->roles);
    }

    /**
     * AJAX callback to update the user profile.
     *
     * @since    1.0.0
     */
    public function update_profile() {
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reseller_access_dashboard_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'reseller-access')));
        }
        
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in to update your profile', 'reseller-access')));
        }
        
        // Check if user is a reseller
        if (!$this->is_reseller()) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action', 'reseller-access')));
        }
        
        // Get the current user
        $user_id = get_current_user_id();
        
        // Sanitize and validate form data
        $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        
        // Validate email
        if (empty($email) || !is_email($email)) {
            wp_send_json_error(array('message' => __('Please enter a valid email address', 'reseller-access')));
        }
        
        // Check if email exists and belongs to another user
        if (email_exists($email) && email_exists($email) != $user_id) {
            wp_send_json_error(array('message' => __('Email address already in use', 'reseller-access')));
        }
        
        // Update user data
        $userdata = array(
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_email' => $email,
        );
        
        $result = wp_update_user($userdata);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        } else {
            wp_send_json_success(array('message' => __('Profile updated successfully', 'reseller-access')));
        }
    }

    /**
     * AJAX callback to update the user password.
     *
     * @since    1.0.0
     */
    public function update_password() {
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reseller_access_dashboard_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'reseller-access')));
        }
        
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in to update your password', 'reseller-access')));
        }
        
        // Check if user is a reseller
        if (!$this->is_reseller()) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action', 'reseller-access')));
        }
        
        // Get the current user
        $user_id = get_current_user_id();
        $user = get_user_by('id', $user_id);
        
        // Sanitize and validate form data
        $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validate required fields
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            wp_send_json_error(array('message' => __('All password fields are required', 'reseller-access')));
        }
        
        // Check if current password is correct
        if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
            wp_send_json_error(array('message' => __('Current password is incorrect', 'reseller-access')));
        }
        
        // Check if new passwords match
        if ($new_password !== $confirm_password) {
            wp_send_json_error(array('message' => __('New passwords do not match', 'reseller-access')));
        }
        
        // Update user password
        wp_set_password($new_password, $user_id);
        
        // Log the user back in
        wp_set_auth_cookie($user_id);
        
        wp_send_json_success(array('message' => __('Password updated successfully', 'reseller-access')));
    }
}
