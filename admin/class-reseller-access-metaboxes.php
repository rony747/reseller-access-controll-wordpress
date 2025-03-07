<?php
/**
 * The metabox-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Reseller_Access
 */

class Reseller_Access_Metaboxes {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Constructor code
    }

    /**
     * Register metaboxes.
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        // Get all public post types
        $post_types = get_post_types( array( 'public' => true ), 'names' );
        
        // Add metabox to all public post types
        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'reseller_access_restriction',
                __( 'Reseller Access Restriction', 'reseller-access' ),
                array( $this, 'render_metabox' ),
                $post_type,
                'side',
                'high'
            );
        }
    }

    /**
     * Render the metabox.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_metabox( $post ) {
        // Add nonce for security
        wp_nonce_field( 'reseller_access_meta_box', 'reseller_access_meta_box_nonce' );
        
        // Get current value
        $restrict_to_resellers = get_post_meta( $post->ID, '_restrict_to_resellers', true );
        
        ?>
        <p>
            <input type="checkbox" id="restrict_to_resellers" name="restrict_to_resellers" value="1" <?php checked( $restrict_to_resellers, '1' ); ?> />
            <label for="restrict_to_resellers"><?php _e( 'Restrict to Resellers only', 'reseller-access' ); ?></label>
        </p>
        <p class="description">
            <?php _e( 'If checked, only users with the Reseller role will be able to view this content.', 'reseller-access' ); ?>
        </p>
        <?php
    }

    /**
     * Save metabox data.
     *
     * @since    1.0.0
     * @param    int       $post_id    The post ID.
     */
    public function save_metabox_data( $post_id ) {
        // Check if our nonce is set
        if ( ! isset( $_POST['reseller_access_meta_box_nonce'] ) ) {
            return;
        }
        
        // Verify that the nonce is valid
        if ( ! wp_verify_nonce( $_POST['reseller_access_meta_box_nonce'], 'reseller_access_meta_box' ) ) {
            return;
        }
        
        // If this is an autosave, our form has not been submitted, so we don't want to do anything
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        // Check the user's permissions
        if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }
        
        // Save the data
        $restrict_to_resellers = isset( $_POST['restrict_to_resellers'] ) ? '1' : '0';
        update_post_meta( $post_id, '_restrict_to_resellers', $restrict_to_resellers );
    }
}
