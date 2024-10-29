<?php


class DMCJsonParserFree {

	public $config;
	public function __construct() {
		$this->config = get_awesome_search_config_vars_free();
	}

	public function getJSON() {
		$this->checkAndUpdateJSONContent();
		return file_get_contents($this->config['JSON_FILE_PATH']);
	}

	public function isJson($string) {
		json_decode($string);
		return json_last_error() === JSON_ERROR_NONE;
	}

	public function updateJSONFromURLToLocalFile() {
		$jsonUrl = $this->config['JSON_FILE_URL'];
		$jsonPath = $this->config['JSON_FILE_PATH'];
		$intervalOptionName = $this->config['JSON_URL_PATH_LAST_UPDATE_OPTION_NAME'];
		$json = $this->getJSONFromJSONUrl($jsonUrl);

		if ($this->isJson($json)) {
			update_option($intervalOptionName, time()); // update the next api call time 
			file_put_contents($jsonPath, $json); // update local file json content
		}
	}

	public function getJSONFromJSONUrl($jsonUrl) {
		return file_get_contents($jsonUrl);
	}

	public function checkAndUpdateJSONContent() {
		$now = time();

		$config = get_awesome_search_config_vars_free();
		$intervalOptionName = $config['JSON_URL_PATH_LAST_UPDATE_OPTION_NAME'];
		$apiCallInterval = $config['JSON_FILE_REFRESH_INTERVAL']; // default 24 hrs


		$lastApiCallTime = get_option($intervalOptionName);
		if (!$lastApiCallTime) { // first time $lastApiCallTime it will be null
			$lastApiCallTime = time() - (60 * 60 * 24 * 2); // asume first api is called 2 days ago.
		}

		$nextCallTime = $lastApiCallTime + $apiCallInterval;

		if ($now > $nextCallTime) {

			$this->updateJSONFromURLToLocalFile();
		}
	}


	public function getJSONFromUrl() {
		$jsonUrl = $this->config['JSON_FILE_URL'];
		return file_get_contents($jsonUrl);
	}


	public function getJSONAsArray() {
		$json = $this->getJSON();
		return json_decode($json, true);
	}

	public function getAllPluginNameFromJsonFile() {

		$dataArray = $this->getJSONAsArray();
		$data = $dataArray['data'];
		$pluginNames = array();
		$pluginsTableData = [];
		if (isset($data['plugins'])) {
			$pluginsTableData = $data['plugins'];
		}

		foreach ($pluginsTableData as $pluginName => $pluginTableColumnItems) {
			$pluginNames[] = $pluginName;
		}

		return $pluginNames;
	}

	public function getDropdownArray() {

		$list = array();
		$wp = array();
		$pluginsTableData = array();

		$dataArray = $this->getJSONAsArray();
		$data = $dataArray['data'];

		if (isset($data['wp'])) {
			$wp = $data['wp'];
		}

		if (isset($data['plugins'])) {
			$pluginsTableData = $data['plugins'];
		}

		$pluginParser = new DMCPluginParserFree();

		foreach ($wp as $wpPostType => $tableColumn) {

			$tableColumn['name'] = $wpPostType;
			$tableColumn['is_plugin'] = 0;
			$list['db'][$wpPostType] = json_encode($tableColumn);
		}

		foreach ($pluginsTableData as $pluginName => $pluginTableColumnItems) {
			//$pluginTableColumnItems['type'] = $pluginName;

			if (!$pluginParser->getPluginIfExist($pluginName)) {
				continue;
			}
			$pluginTabelInfos = array();
			foreach ($pluginTableColumnItems as $tableColumn) {
				$tableColumn['name'] = $pluginName;
				$tableColumn['is_plugin'] = 1;
				$pluginTabelInfos[] = $tableColumn;
			}

			if (!count($pluginTableColumnItems)) {
				// if no table or column info found
				$info = array();
				$info['name'] = $pluginName;
				$info['is_plugin'] = 1;
				$pluginTabelInfos[] = $info;
			}
			// list of all plugins available in json file
			$list['plugins'][$pluginName] = json_encode($pluginTabelInfos);
			$list['db'][$pluginName] = json_encode($pluginTabelInfos);
		}

		return $list;
	}

	public function getWPTablesDropdownArray() {
		global $wpdb;
		$allWPTables = $wpdb->get_results(
			"SHOW TABLES",
			ARRAY_N
		);

		$dropdown = array();
		foreach ($allWPTables as $item) {
			$table = $item[0];

			$wpTableInfos = array();

			$wptableColumn['table'] = $table;
			$wptableColumn['name'] = $table;
			$wptableColumn['column'] = "*";
			$wptableColumn['is_plugin'] = 0;
			$wpTableInfos[] = $wptableColumn;

			$dropdown[$table] = json_encode($wpTableInfos);
		}

		return $dropdown;
	}

	public function excludeUninstalledPluginFromDropdown($jsonPluginTableInfo) {
		$jsonPluginTableInfo['test'] = '[{"table":"posts","column":"*","name":"Posts","is_plugin":0}]';

		$jsonPluginNames = array_keys($jsonPluginTableInfo);
		$jsonPluginNamesLower = array_map('strtolower', $jsonPluginNames);
		$pluginParser = new DMCPluginParserFree();
		$allWpInstalledPlugins = $pluginParser->getAllWpInstalledPlugins();

		$allPluginsInJsonFile = $this->getAllPluginNameFromJsonFile();

		foreach ($allWpInstalledPlugins as $baseFileLocation => $pluginData) {

			$wpDirFile = explode("/", $baseFileLocation);
			$wpPluginDir = strtolower($wpDirFile[0]);
			$wpPluginName = $pluginData['Name'];
			$wpPluginNameLower = strtolower($pluginData['Name']);

			if (
				in_array($wpPluginDir, $jsonPluginNamesLower)
				|| in_array($wpPluginName, $jsonPluginNames)
				|| in_array($wpPluginNameLower, $jsonPluginNamesLower)

			) {
				echo esc_html($wpPluginName . " -In Array- <br>");
				// plugin available in json
			} else {
				unset($jsonPluginTableInfo[$wpPluginDir]);
				unset($jsonPluginTableInfo[$wpPluginName]);
				unset($jsonPluginTableInfo[$wpPluginNameLower]);
			}
		}

		unset($jsonPluginTableInfo['test']);
		print_r($jsonPluginTableInfo);
	}
}