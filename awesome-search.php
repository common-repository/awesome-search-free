<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://awesomesearchwp.com
 * @since             1.0.0
 * @package           Awesome_Search
 *
 * @wordpress-plugin
 * Plugin Name:       Awesome Search Free
 * Plugin URI:        https://awesomesearchwp.com
 * Description:       Search for a string in DB and or in Files
 * Version:           1.0.11
 * Author:            Awesome Search LLC
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       awesome-search-free
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
ini_set("allow_url_fopen", 1);
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AWESOME_SEARCH_VERSION', '1.0.7' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-awesome-search-activator.php
 */
function activate_awesome_search_free() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-awesome-search-activator.php';
	Awesome_Search_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-awesome-search-deactivator.php
 */
function deactivate_awesome_search_free() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-awesome-search-deactivator.php';
	Awesome_Search_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_awesome_search_free' );
register_deactivation_hook( __FILE__, 'deactivate_awesome_search_free' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'awesome-search-config.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-awesome-search.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-do-awesome-search.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-parser.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-json-parser.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/do-awesome-search-ajax-handler.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/do-awesome-search-api-handler.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/menu/awesome-search-menu.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_awesome_search_free() {

	$plugin = new Awesome_Search_Free();
	$plugin->run();

}
run_awesome_search_free();
