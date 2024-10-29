<?php


class DMCPluginParserFree {

	public $allWPPlugins;
	public $acceptedFileExt = array("php", "txt", "json", "xml", "css", "html", "log");
	public $searchAblePlugins = array();

	public function __construct() {
		$config = get_awesome_search_config_vars_free();
		$this->acceptedFileExt = $config['FILE_TO_SEARCH'];
		$this->allWPPlugins = get_plugins();
	}

	public function getAllWpInstalledPlugins() {
		return $this->allWPPlugins;
	}

	public function getPluginThatAreNotInJsonFile($jsonPlugins) {

		$pluginNameNotExistInJson = array();
		$pluginFolderNotExistInJson = array();

		$jsonPlugins = array_map('strtolower', $jsonPlugins); // convert to lowercase

		$jsonPlugins[] = strtolower("Awesome Search"); // exclude this plugin also.
		$jsonPlugins[] = strtolower("Akismet Anti-Spam"); // exclude this plugin also.
		$jsonPlugins[] = strtolower("Hello Dolly"); // exclude this plugin also.

		foreach ($this->allWPPlugins as $baseFileLocation => $pluginData) {

			$wpDirFile = explode("/", $baseFileLocation);
			$wpPluginDir = strtolower($wpDirFile[0]);
			$wpPluginName = strtolower($pluginData['Name']);

			if (in_array($wpPluginDir, $jsonPlugins) || in_array($wpPluginName, $jsonPlugins)) {
				// plugin exist in json file
			} else {
				$pluginNameNotExistInJson[] = $pluginData['Name'];
				$pluginFolderNotExistInJson[] = $wpPluginDir;
			}
		}

		return array("name" => $pluginNameNotExistInJson, "folder" => $pluginFolderNotExistInJson);
	}

	public function getPluginIfExist($pluginName) {

		$pluginNameLower = strtolower($pluginName);
		$foundPlugin = array();

		foreach ($this->allWPPlugins as $baseFileLocation => $pluginData) {

			$wpDirFile = explode("/", $baseFileLocation);
			$wpDirName = strtolower($wpDirFile[0]);
			$wpPluginName = strtolower($pluginData['Name']);

			if ($pluginNameLower == $wpDirName || $pluginNameLower == $wpPluginName) {

				$actualPluginFolder = WP_PLUGIN_DIR . "/" . $wpDirName;
				$pluginData['Path'] = $actualPluginFolder;
				$pluginData['Base'] = $baseFileLocation;
				$pluginData['Folder'] = $wpDirName;

				$this->searchAblePlugins[$wpDirName] = $pluginData;
				$foundPlugin = $pluginData;
				break;
			}
		}

		return $foundPlugin;
	}

	public function getPluginFiles($pluginInfo) {

		$path = $pluginInfo['Path'];

		$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

		$files = array();

		foreach ($rii as $file) {

			if ($file->isDir()) {
				continue;
			}

			$filePath = $file->getPathname();
			$fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
			if (in_array($fileExt, $this->acceptedFileExt)) {
				$files[] = $filePath;
			}

			usort($files, function ($a, $b) {
				return strlen($a) - strlen($b);
			});
		}

		return $files;
	}

	public function getRootFiles() {

		$files = array();

		$scanned_directory = array_diff(scandir(ABSPATH), array('..', '.'));
		foreach ($scanned_directory as $file) {

			$filePath = ABSPATH . $file;

			$fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
			if (in_array($fileExt, $this->acceptedFileExt)) {
				$files[] = $filePath;
			}

			usort($files, function ($a, $b) {
				return strlen($a) - strlen($b);
			});
		}
		return $files;
	}
}