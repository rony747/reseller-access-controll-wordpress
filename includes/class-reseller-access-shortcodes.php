<?php
/**
 * The shortcode functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Reseller_Access
 */

class Reseller_Access_Shortcodes {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Constructor code
    }

    /**
     * Shortcode to display content only for resellers.
     *
     * Usage: [reseller_content]This content is only for resellers[/reseller_content]
     * 
     * @since    1.0.0
     * @param    array     $atts       Shortcode attributes.
     * @param    string    $content    Shortcode content.
     * @return   string                Processed content.
     */
    public function reseller_content_shortcode( $atts, $content = null ) {
        // Parse attributes
        $atts = shortcode_atts( array(
            'message' => '', // Custom message to display for non-resellers
        ), $atts, 'reseller_content' );
        
        // Check if user is a reseller
        if ( $this->is_reseller() ) {
            // Return the content if user is a reseller
            return do_shortcode( $content );
        } else {
            // Show custom message if provided, otherwise show default message
            if ( ! empty( $atts['message'] ) ) {
                $message = $atts['message'];
            } else {
                $message = get_option( 'reseller_access_message', __( 'This content is restricted to Resellers only.', 'reseller-access' ) );
            }
            
            return '<div class="reseller-access-denied">' . wp_kses_post( $message ) . '</div>';
        }
    }

    /**
     * Shortcode to conditionally display content based on whether user is a reseller.
     *
     * Usage: [reseller_check reseller="Content for resellers" non_reseller="Content for non-resellers"]
     * 
     * @since    1.0.0
     * @param    array     $atts       Shortcode attributes.
     * @param    string    $content    Shortcode content.
     * @return   string                Processed content.
     */
    public function reseller_check_shortcode( $atts, $content = null ) {
        // Parse attributes
        $atts = shortcode_atts( array(
            'reseller'     => '', // Content to display for resellers
            'non_reseller' => '', // Content to display for non-resellers
        ), $atts, 'reseller_check' );
        
        // Check if user is a reseller
        if ( $this->is_reseller() ) {
            // Return reseller content
            return do_shortcode( $atts['reseller'] );
        } else {
            // Return non-reseller content
            return do_shortcode( $atts['non_reseller'] );
        }
    }

    /**
     * Check if the current user is a reseller.
     *
     * @since    1.0.0
     * @return   boolean    True if user is a reseller, false otherwise.
     */
    private function is_reseller() {
        // Check if user is logged in
        if ( ! is_user_logged_in() ) {
            return false;
        }
        
        // Check if user has the reseller role
        $user = wp_get_current_user();
        return in_array( 'reseller', (array) $user->roles );
    }
}
