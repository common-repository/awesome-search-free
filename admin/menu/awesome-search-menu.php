<?php
// all the plugin menu definations


add_action('admin_menu', 'dmc_awesome_search_menus_free', 30);
function dmc_awesome_search_menus_free() {

    add_menu_page(
        __('Awesome Search', 'my-textdomain'),
        __('Awesome Search', 'my-textdomain'),
        'manage_options',
        'awesome-search',
        'dmc_awesome_search_contents_free',
        'dashicons-schedule',
        2
    );
}



function dmc_awesome_search_contents_free() {

    $jsonParser = new DMCJsonParserFree();
    $tableColumn = $jsonParser->getDropdownArray();
    $allWpTables = $jsonParser->getWPTablesDropdownArray();

    $activeTab = "dbfiles";
    if (isset($_GET['tab'])) {
        $activeTab = $_GET['tab'] == "custom" ? "custom" : "dbfiles";
    }

    ob_start();
    require_once plugin_dir_path(__FILE__) . 'page/awesome-search-form.php';
    echo ob_get_clean();
}