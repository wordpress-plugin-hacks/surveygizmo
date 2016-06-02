<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://squibble-fish.com
 * @since             1.0.0
 * @package           Surveygizmo
 *
 * @wordpress-plugin
 * Plugin Name:       surveygizmo
 * Plugin URI:        http://www.surveygizmo.com
 * Description:       Build and complete your SurveyGizmo surveys.
 * Version:           1.0.0
 * Author:            Stephen Fisher
 * Author URI:        http://squibble-fish.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       surveygizmo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-surveygizmo-activator.php
 */
function activate_surveygizmo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-surveygizmo-activator.php';
	Surveygizmo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-surveygizmo-deactivator.php
 */
function deactivate_surveygizmo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-surveygizmo-deactivator.php';
	Surveygizmo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_surveygizmo' );
register_deactivation_hook( __FILE__, 'deactivate_surveygizmo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-surveygizmo.php';

$plugin_dir = plugin_dir_path( __FILE__ );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_surveygizmo() {

	$plugin = new Surveygizmo();
	$plugin->run();

}
run_surveygizmo();
