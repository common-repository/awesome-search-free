<?php
function get_awesome_search_config_vars_free(){

	$config = array();
	$config['AS_API_POST_PLUGIN_OPTION_NAME'] = "AS_API_LAST_CALL_TIME";
	$config['AS_API_POST_PLUGIN_INTERVAL'] = 60 * 60 * 24 ; // 24 hrs
	$config['API_KEY'] = '@NKMDK';
	$config['API_URL'] = 'https://awesomesearchwp.com/api/?method=updatePlugins';
	$config['SHOW_LINES_BEFORE_AFTER_SEARCH_LINE'] = 0; // 5 max
	$config['FILE_TO_SEARCH'] = array("php","txt","json","xml","css", "html", "log");
	$config['JSON_FILE_URL'] = 'https://awesomesearchwp.com/api/awesome-search.json';
	$config['JSON_FILE_PATH'] = plugin_dir_path( __FILE__ ) . "awesome-search.json";
	$config['JSON_FILE_REFRESH_INTERVAL'] = 60 * 60 * 24 * 1; // 1 Day
	$config['JSON_URL_PATH_LAST_UPDATE_OPTION_NAME'] = "AS_JSON_URL_PATH_LAST_UPDATE_TIME";

	return $config;

}