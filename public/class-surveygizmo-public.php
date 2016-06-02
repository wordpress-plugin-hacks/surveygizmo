<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://squibble-fish.com
 * @since      1.0.0
 *
 * @package    Surveygizmo
 * @subpackage Surveygizmo/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Surveygizmo
 * @subpackage Surveygizmo/public
 * @author     Stephen Fisher <stephen@squibble-fish.com>
 */
class Surveygizmo_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->sg_options = get_option( $this->plugin_name );
        add_action( "surveygizmo_api", array( $this, "sg_api_call" ), 10, 5 );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Surveygizmo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Surveygizmo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/surveygizmo-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Surveygizmo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Surveygizmo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/surveygizmo-public.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Call the API - Make is available via template / or plugins
     *
     * @param string $method
     * @param string $obj
     * @param string $sid
     * @param string $resultsperpage
     * @param array $options
     * @return array|mixed|object|WP_Error
     */
    public function sg_api_call( $method, $obj, $sid, $resultsperpage, $options = array() )
    {
        if ( !$sid ) {
            return new WP_Error( 'missing sid', __( "A survey ID is required", $this->plugin_name ) );
        }
        elseif ( !$method ) {
            return new WP_Error( "missing method", __( "A method is required", $this->plugin_name ) );
        }
        elseif ( ( "put" === $method || "post" === $method || "delete" === $method ) && empty( $options ) ) {
            return new WP_Error( 'missing options', __( "Options required for Put method", $this->plugin_name ) );
        }

        require_once plugin_dir_path( dirname( __FILE__ ) ). "includes/class-surveygizmo-api.php";

        $sg_api_key_obj = get_option( "surveygizmo_wordpress_plugin" );
        $api = new SgApi( $sg_api_key_obj['key'], $sg_api_key_obj['secret'], $resultsperpage, $sid );
        $data = array();

        switch ( $method ) {
            case "put":
                $data = $api->put( $obj, $options );
                break;
            case "post":
                $data = $api->post( $obj, $options );
                break;
            case "delete":
                $data = $api->delete( $obj, $options );
                break;
            case "get":
            default:
                $data = $api->get( $obj );
                break;
        }

        return $data;
    }

}
