<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Reseller_Access
 */

class Reseller_Access_Admin {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Constructor code
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( 'reseller-access-admin', RESELLER_ACCESS_PLUGIN_URL . 'admin/css/reseller-access-admin.css', array(), RESELLER_ACCESS_VERSION, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'reseller-access-admin', RESELLER_ACCESS_PLUGIN_URL . 'admin/js/reseller-access-admin.js', array( 'jquery' ), RESELLER_ACCESS_VERSION, false );
        
        // Add the ajax url to our script
        wp_localize_script( 'reseller-access-admin', 'reseller_access_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'reseller_access_nonce' ),
        ) );
    }

    /**
     * Add menu items to the admin menu.
     *
     * @since    1.0.0
     */
    public function add_admin_menu() {
        // Add main menu page
        add_menu_page(
            __( 'Reseller Access', 'reseller-access' ),
            __( 'Reseller Access', 'reseller-access' ),
            'manage_options',
            'reseller-access',
            array( $this, 'display_reseller_page' ),
            'dashicons-groups',
            25
        );
        
        // Add submenu pages
        add_submenu_page(
            'reseller-access',
            __( 'Manage Resellers', 'reseller-access' ),
            __( 'Manage Resellers', 'reseller-access' ),
            'manage_options',
            'reseller-access',
            array( $this, 'display_reseller_page' )
        );
        
        add_submenu_page(
            'reseller-access',
            __( 'Reseller Settings', 'reseller-access' ),
            __( 'Settings', 'reseller-access' ),
            'manage_options',
            'reseller-access-settings',
            array( $this, 'display_settings_page' )
        );
    }

    /**
     * Display the main reseller management page.
     *
     * @since    1.0.0
     */
    public function display_reseller_page() {
        include_once RESELLER_ACCESS_PLUGIN_DIR . 'admin/partials/reseller-access-admin-display.php';
    }

    /**
     * Display the settings page.
     *
     * @since    1.0.0
     */
    public function display_settings_page() {
        include_once RESELLER_ACCESS_PLUGIN_DIR . 'admin/partials/reseller-access-admin-settings.php';
    }

    /**
     * Ajax callback to create a new reseller.
     *
     * @since    1.0.0
     */
    public function create_reseller() {
        // Check nonce for security
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'reseller_access_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed', 'reseller-access' ) ) );
        }
        
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to perform this action', 'reseller-access' ) ) );
        }
        
        // Sanitize and validate form data
        $username = isset( $_POST['username'] ) ? sanitize_user( $_POST['username'] ) : '';
        $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        $last_name = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
        $password = isset( $_POST['password'] ) ? $_POST['password'] : wp_generate_password( 12, true, false );
        
        // Validate required fields
        if ( empty( $username ) || empty( $email ) ) {
            wp_send_json_error( array( 'message' => __( 'Username and email are required fields', 'reseller-access' ) ) );
        }
        
        // Check if username exists
        if ( username_exists( $username ) ) {
            wp_send_json_error( array( 'message' => __( 'Username already exists', 'reseller-access' ) ) );
        }
        
        // Check if email exists
        if ( email_exists( $email ) ) {
            wp_send_json_error( array( 'message' => __( 'Email already exists', 'reseller-access' ) ) );
        }
        
        // Create the user
        $user_id = wp_create_user( $username, $password, $email );
        
        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
        }
        
        // Update user meta
        wp_update_user( array(
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'role' => 'reseller'
        ) );
        
        // Send user notification if needed
        if ( isset( $_POST['send_notification'] ) && $_POST['send_notification'] === 'yes' ) {
            wp_new_user_notification( $user_id, null, 'both' );
        }
        
        wp_send_json_success( array( 
            'message' => __( 'Reseller created successfully', 'reseller-access' ),
            'user_id' => $user_id 
        ) );
    }

    /**
     * Ajax callback to get all resellers.
     *
     * @since    1.0.0
     */
    public function get_resellers() {
        // Check nonce for security
        if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'reseller_access_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed', 'reseller-access' ) ) );
        }
        
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to perform this action', 'reseller-access' ) ) );
        }
        
        // Get all resellers
        $resellers = get_users( array(
            'role' => 'reseller',
        ) );
        
        $reseller_data = array();
        
        foreach ( $resellers as $reseller ) {
            $reseller_data[] = array(
                'id' => $reseller->ID,
                'username' => $reseller->user_login,
                'email' => $reseller->user_email,
                'first_name' => $reseller->first_name,
                'last_name' => $reseller->last_name,
                'registered' => $reseller->user_registered,
                'edit_url' => get_edit_user_link( $reseller->ID ),
            );
        }
        
        wp_send_json_success( array( 'resellers' => $reseller_data ) );
    }

    /**
     * Ajax callback to delete a reseller.
     *
     * @since    1.0.0
     */
    public function delete_reseller() {
        // Check nonce for security
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'reseller_access_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed', 'reseller-access' ) ) );
        }
        
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to perform this action', 'reseller-access' ) ) );
        }
        
        // Get user ID
        $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
        
        if ( $user_id === 0 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid user ID', 'reseller-access' ) ) );
        }
        
        // Check if user is a reseller
        $user = get_userdata( $user_id );
        
        if ( ! $user || ! in_array( 'reseller', $user->roles ) ) {
            wp_send_json_error( array( 'message' => __( 'This user is not a reseller', 'reseller-access' ) ) );
        }
        
        // Delete the user
        $deleted = wp_delete_user( $user_id );
        
        if ( ! $deleted ) {
            wp_send_json_error( array( 'message' => __( 'Failed to delete user', 'reseller-access' ) ) );
        }
        
        wp_send_json_success( array( 'message' => __( 'Reseller deleted successfully', 'reseller-access' ) ) );
    }
}
