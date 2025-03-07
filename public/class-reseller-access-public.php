<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Reseller_Access
 */

class Reseller_Access_Public {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Constructor code
    }

    /**
     * Filter content to restrict access for non-resellers.
     *
     * @since    1.0.0
     * @param    string    $content    The content of the post.
     * @return   string                The filtered content.
     */
    public function filter_content( $content ) {
        global $post;
        
        // If not a singular post or no post is set, return the content
        if ( ! is_singular() || ! $post ) {
            return $content;
        }
        
        // Check if the content is restricted
        $restrict_to_resellers = get_post_meta( $post->ID, '_restrict_to_resellers', true );
        
        // If content is not restricted, return the content as is
        if ( $restrict_to_resellers !== '1' ) {
            return $content;
        }
        
        // Check if the user is a reseller
        if ( $this->is_reseller() ) {
            return $content;
        }
        
        // Get the access denied message
        $access_message = get_option( 'reseller_access_message', __( 'This content is restricted to Resellers only.', 'reseller-access' ) );
        
        // Return the access denied message instead of the content
        return '<div class="reseller-access-denied">' . wp_kses_post( $access_message ) . '</div>';
    }

    /**
     * Check access restrictions and redirect if necessary.
     *
     * @since    1.0.0
     */
    public function check_access_restrictions() {
        // Only check on singular pages
        if ( ! is_singular() ) {
            return;
        }
        
        global $post;
        
        // Check if the content is restricted
        $restrict_to_resellers = get_post_meta( $post->ID, '_restrict_to_resellers', true );
        
        // If content is not restricted, return
        if ( $restrict_to_resellers !== '1' ) {
            return;
        }
        
        // Check if the user is a reseller
        if ( $this->is_reseller() ) {
            return;
        }
        
        // Get the redirect page ID
        $redirect_page_id = get_option( 'reseller_access_redirect_page', 0 );
        
        // If a redirect page is set, redirect to it
        if ( $redirect_page_id && $redirect_page_id != $post->ID ) {
            wp_redirect( get_permalink( $redirect_page_id ) );
            exit;
        }
    }

    /**
     * Check if the current user is a reseller.
     *
     * @since    1.0.0
     * @return   boolean    True if user is a reseller, false otherwise.
     */
    public function is_reseller() {
        // Check if user is logged in
        if ( ! is_user_logged_in() ) {
            return false;
        }
        
        // Check if user has the reseller role
        $user = wp_get_current_user();
        return in_array( 'reseller', (array) $user->roles );
    }
    
    /**
     * Block resellers from accessing the WordPress admin.
     *
     * @since    1.0.0
     */
    public function block_reseller_admin_access() {
        // If not on admin page, return
        if ( ! is_admin() ) {
            return;
        }
        
        // If AJAX request, return (to allow frontend dashboard AJAX)
        if ( wp_doing_ajax() ) {
            return;
        }
        
        // If not a reseller, return
        if ( ! $this->is_reseller() ) {
            return;
        }
        
        // Get the dashboard page ID
        $dashboard_page_id = get_option( 'reseller_access_dashboard_page', 0 );
        
        // If a dashboard page is set, redirect to it
        if ( $dashboard_page_id ) {
            wp_redirect( get_permalink( $dashboard_page_id ) );
            exit;
        } else {
            // Otherwise redirect to the home page
            wp_redirect( home_url() );
            exit;
        }
    }
}
