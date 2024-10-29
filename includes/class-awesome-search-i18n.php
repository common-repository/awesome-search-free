<?php

/**
 * Define the internationalization functionality.
 * 
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Awesome_Search
 * @subpackage Awesome_Search/includes
 * @link       https://awesomesearchwp.com
 * @since      1.0.0
 */
class Awesome_Search_i18n_Free {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'awesome-search',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}