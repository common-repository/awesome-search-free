<?php

function awesome_search_send_plugin_list_free() {

    $config = get_awesome_search_config_vars_free();
    $intervalOptionName = $config['AS_API_POST_PLUGIN_OPTION_NAME'];

    update_option($intervalOptionName, time()); // update the next api call time 

    $jsonParser = new DMCJsonParserFree();
    $pluginParser = new DMCPluginParserFree();
    $allPluginsInJsonFile = $jsonParser->getAllPluginNameFromJsonFile();
    $pluginNotExistInJsonFile = $pluginParser->getPluginThatAreNotInJsonFile($allPluginsInJsonFile);

    $pluginsList = $pluginNotExistInJsonFile['name'];

    if ($pluginsList) {

        $postData = array('key' => $config['API_KEY'], 'plugin' => $pluginsList);
        $postFormData = http_build_query($postData);

        $response = wp_remote_post($config['API_URL'], [
            'method' => 'POST',
            'timeout' => 0,
            'redirection' => 10,
            'httpversion' => CURL_HTTP_VERSION_1_1,
            'headers' => array(),
            'body' => $postFormData,
        ]);
    }
}

function send_plugin_list_API_free() {

    $now = time();

    $config = get_awesome_search_config_vars_free();
    $intervalOptionName = $config['AS_API_POST_PLUGIN_OPTION_NAME'];
    $apiCallInterval = $config['AS_API_POST_PLUGIN_INTERVAL']; // default 24 hrs

    $lastApiCallTime = get_option($intervalOptionName);
    if (!$lastApiCallTime) {
        $lastApiCallTime = time() - (60 * 60 * 24 * 2); // asume first api is called 2 days ago.
    }

    $nextCallTime = $lastApiCallTime + $apiCallInterval;

    if ($now > $nextCallTime) {
        awesome_search_send_plugin_list_free();
    }
}

add_action('init', 'send_plugin_list_API_free');