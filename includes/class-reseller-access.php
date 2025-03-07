<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Reseller_Access
 */

class Reseller_Access {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $actions    The actions registered with WordPress.
     */
    protected $actions = array();

    /**
     * The filters registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $filters    The filters registered with WordPress.
     */
    protected $filters = array();

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_shortcodes();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // Admin area functionality
        require_once RESELLER_ACCESS_PLUGIN_DIR . 'admin/class-reseller-access-admin.php';
        
        // Public-facing functionality
        require_once RESELLER_ACCESS_PLUGIN_DIR . 'public/class-reseller-access-public.php';
        
        // Shortcodes
        require_once RESELLER_ACCESS_PLUGIN_DIR . 'includes/class-reseller-access-shortcodes.php';
        
        // Metaboxes for content restriction
        require_once RESELLER_ACCESS_PLUGIN_DIR . 'admin/class-reseller-access-metaboxes.php';
        
        // Frontend dashboard
        require_once RESELLER_ACCESS_PLUGIN_DIR . 'public/class-reseller-access-dashboard.php';
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Reseller_Access_Admin();
        
        // Admin menu
        $this->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
        
        // Admin scripts and styles
        $this->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        
        // Ajax actions for admin
        $this->add_action( 'wp_ajax_create_reseller', $plugin_admin, 'create_reseller' );
        $this->add_action( 'wp_ajax_get_resellers', $plugin_admin, 'get_resellers' );
        $this->add_action( 'wp_ajax_delete_reseller', $plugin_admin, 'delete_reseller' );
        
        // Metaboxes
        $metaboxes = new Reseller_Access_Metaboxes();
        $this->add_action( 'add_meta_boxes', $metaboxes, 'add_meta_boxes' );
        $this->add_action( 'save_post', $metaboxes, 'save_metabox_data' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Reseller_Access_Public();
        
        // Content filtering
        $this->add_filter( 'the_content', $plugin_public, 'filter_content', 20 );
        
        // Redirect non-resellers
        $this->add_action( 'template_redirect', $plugin_public, 'check_access_restrictions' );
        
        // Prevent resellers from accessing admin
        $this->add_action( 'admin_init', $plugin_public, 'block_reseller_admin_access' );
        
        // Handle frontend dashboard if enabled
        if ( 'yes' === get_option( 'reseller_access_enable_frontend_dashboard', 'yes' ) ) {
            $dashboard = new Reseller_Access_Dashboard();
            $this->add_action( 'wp_enqueue_scripts', $dashboard, 'enqueue_styles' );
            $this->add_action( 'wp_enqueue_scripts', $dashboard, 'enqueue_scripts' );
            
            // This content filter is critical for displaying the dashboard
            $this->add_filter( 'the_content', $dashboard, 'display_dashboard', 99 ); // Higher priority to ensure it runs
            
            // Handle AJAX actions for the frontend dashboard
            $this->add_action( 'wp_ajax_reseller_update_profile', $dashboard, 'update_profile' );
            $this->add_action( 'wp_ajax_reseller_update_password', $dashboard, 'update_password' );
        }
    }

    /**
     * Register all shortcodes.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_shortcodes() {
        $shortcodes = new Reseller_Access_Shortcodes();
        
        // Register shortcodes
        add_shortcode( 'reseller_content', array( $shortcodes, 'reseller_content_shortcode' ) );
        add_shortcode( 'reseller_check', array( $shortcodes, 'reseller_check_shortcode' ) );
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string    $hook             The name of the WordPress action that is being registered.
     * @param    object    $component        A reference to the instance of the object on which the action is defined.
     * @param    string    $callback         The name of the function definition on the $component.
     * @param    int       $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int       $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->actions = $this->add_hook( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string    $hook             The name of the WordPress filter that is being registered.
     * @param    object    $component        A reference to the instance of the object on which the filter is defined.
     * @param    string    $callback         The name of the function definition on the $component.
     * @param    int       $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int       $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->filters = $this->add_hook( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @since    1.0.0
     * @access   private
     * @param    array     $hooks            The collection of hooks that is being registered (that is, actions or filters).
     * @param    string    $hook             The name of the WordPress filter that is being registered.
     * @param    object    $component        A reference to the instance of the object on which the filter is defined.
     * @param    string    $callback         The name of the function definition on the $component.
     * @param    int       $priority         The priority at which the function should be fired.
     * @param    int       $accepted_args    The number of arguments that should be passed to the $callback.
     * @return   array                        The collection of actions and filters registered with WordPress.
     */
    private function add_hook( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;
    }

    /**
     * Run the plugin.
     *
     * @since    1.0.0
     */
    public function run() {
        // Register all actions
        foreach ( $this->actions as $hook ) {
            add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

        // Register all filters
        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }
    }
}
