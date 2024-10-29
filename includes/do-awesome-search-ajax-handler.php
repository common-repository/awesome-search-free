<?php

add_action('wp_ajax_do_awesome_search_in_database_free', 'do_awesome_search_in_database_free');
add_action('wp_ajax_get_file_list_in_plugin_free', 'get_file_list_in_plugin_free');
add_action('wp_ajax_do_awesome_search_in_files_free', 'do_awesome_search_in_files_free');
add_action('wp_ajax_get_file_list_in_root_dir_free', 'get_file_list_in_root_dir_free');

function do_awesome_search_sanitize_array($array) {
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            $value = do_awesome_search_sanitize_array($value);
        } else {
            $value = sanitize_text_field($value);
        }
    }
    return $array;
}


function do_awesome_search_in_database_free() {

    $searchInDatabase = sanitize_text_field($_POST['search-in-database']) == "yes";
    $searchString = sanitize_text_field($_POST['searchString']);
    $searchParam = do_awesome_search_sanitize_array($_POST['searchParam']);

    if ($searchInDatabase) {
        $searchObject = new DoAwesomeSearchFree();
        $result = $searchObject->doDbSearch($searchString, $searchParam);
        echo json_encode($result);
    }

    wp_die();
}

function do_awesome_search_in_files_free() {

    $searchInFiles = sanitize_text_field($_POST['search-in-files']) == "yes";
    $result = array();
    if ($searchInFiles) {
        $searchString = trim(sanitize_text_field($_POST['searchString']));

        if (strlen($searchString)) {
            $filePath = sanitize_text_field($_POST['filePath']);
            $result['filePath'] = $filePath;
            $searchObject = new DoAwesomeSearchFree();
            $result['result'] = $searchObject->searchInFile($filePath, $searchString);
            echo json_encode($result);
        }
    }

    wp_die();
}

function get_file_list_in_plugin_free() {
    $result = array();
    $result['files'] = array();

    $searchParam = do_awesome_search_sanitize_array($_POST['searchParam']); // array of search params
    $is_plugin = intval($searchParam['is_plugin']) == 1 ? 1 : 0;
    $pluginName = $searchParam['name'];

    if ($is_plugin) {
        $DMCPluginParser = new DMCPluginParserFree();
        $plugin = $DMCPluginParser->getPluginIfExist($pluginName);
        $result['files'] = $DMCPluginParser->getPluginFiles($plugin);
        $result['total'] = count($result['files']);
        $result['is_plugin'] = $is_plugin;

        echo json_encode($result);
    } else {

        $result['files'] = array();
        $result['total'] = 0;
        $result['is_plugin'] = $is_plugin;
        echo json_encode($result);
    }

    wp_die();
}

function get_file_list_in_root_dir_free() {

    $result = array();
    $result['files'] = array();

    $searchParam = do_awesome_search_sanitize_array($_POST['searchParam']); // array of search params
    $is_root = intval($searchParam['is_root']) == 1 ? 1 : 0;

    if ($is_root) {
        $DMCPluginParser = new DMCPluginParserFree();

        $result['files'] = $DMCPluginParser->getRootFiles();
        $result['total'] = count($result['files']);
        $result['is_root'] = $is_root;

        echo json_encode($result);
    } else {
        $result['files'] = array();
        $result['total'] = 0;
        $result['is_root'] = 0;
        echo json_encode($result);
    }

    wp_die();
}