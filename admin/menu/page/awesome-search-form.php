<style type="text/css">
.media-toolbar {
    position: relative !important;
}

.media-toolbar-secondary {
    float: inherit !important;
}
</style>
<div class="wrap awesome_search">

    <h2> Awesome Search </h2>
    <nav class="nav-tab-wrapper">
        <a href="#" class="dmc-awesome-tab nav-tab nav-tab-active" data-tab="db">Search In Table / Files</a>
    </nav>

    <div class="media-frame wp-core-ui mode-grid">
        <div class="media-toolbar wp-filter" style="position:relative">

            <div class="media-toolbar-secondary db both" style="display:block; float:inherit">

                <div class="select_input">
                    <label>Select All
                        <input type="checkbox" name="select-all" id="dmc-select-all" value="yes" />
                        <span class="input_check"></span>
                    </label>
                </div>
                <?php
                if ($activeTab == "dbfiles") { ?>

                <select id="input-db-fields" class="input-table-column-data filter_input" multiple>

                    <?php
                        ksort($tableColumn['db']);
                        foreach ($tableColumn['db'] as $tablePlugins => $tcJson) { ?>

                    <option value='<?php echo esc_attr($tcJson); ?>'><?php echo esc_attr($tablePlugins); ?></option>

                    <?php } ?>
                </select>

                <?php } ?>


                <div class="dmc-error-msg" id="error-seacrh-dropdown">Please select search area.</div>


                <?php
                if ($activeTab == "dbfiles") { ?>
                <div class="select_input_outer">
                    <div class="select_input">
                        <label>Search in Database
                            <input type="checkbox" id="search-in-database" value="yes" checked="checked" />
                            <span class="input_check"></span>
                        </label>
                    </div>

                    <div class="select_input">
                        <label>Search in Plugin Files
                            <input type="checkbox" id="search-in-files" value="yes" />
                            <span class="input_check"></span>
                        </label>
                    </div>

                    <div class="select_input">
                        <label>Search in Wordpress Root Directory
                            <input type="checkbox" id="search-in-root" value="yes" />
                            <span class="input_check"></span>
                        </label>
                    </div>
                </div>
                <div class="dmc-error-msg" id="error-seacrh-db-files">Please select database or files option.</div>
                <?php } else { ?>
                <label style="display:none">Search in Database
                    <input type="checkbox" id="search-in-database" value="yes" checked="checked" />
                </label>
                <?php } ?>


            </div>


            <div class="media-toolbar-primary search-form search_form_input">

                <?php
                if ($activeTab == "dbfiles") { ?>
                <!--<div>
                      <label class="search-input-label">Search anything in DB and / or in files.</label>
                    </div>-->
                <?php } else { ?>
                <div>
                    <label class="search-input-label">Search anything in Tables.</label>
                </div>
                <?php } ?>
                <input type="search" id="dmc-awesome-search-input" class="search" placeholder="Search..." autocomplete="off">
                <input type="button" name="submit" id="dmc-awesome-search-submit" class="button button-primary" value="Search"> <span class="danger dmc-stop-search"> Stop Search </span>
                <div class="dmc-error-msg" id="error-seacrh-string">At least 2 char's required for search.</div>

            </div>
        </div>
    </div>

    <div class="media-frame wp-core-ui mode-grid">


        <div class="media-toolbar wp-filter search_result" id="search-result-wrapper" style="position:relative">
            <div class="search_title">
                Search Results (Database)
            </div>

            <div class="search-result" id="awesome-search-result-container-db"></div>
            <div class="dmc-searching-gif-for-db dmc-searching-gif-for-files" id="dmc-searching-gif-for-db" style="display:none;">
                <span><img src="<?php echo esc_url(get_site_url()); ?>/wp-admin/images/loading.gif" /> Searching...</span>

            </div>
            <div class="dmc-search-no-result-found" id="dmc-search-no-db-result-found" style="display:none;">
                No Record Found.
            </div>
        </div>

        <?php
        if ($activeTab == "dbfiles") { ?>
        <div class="media-toolbar wp-filter search_result" id="search-result-wrapper" style="position:relative">
            <div class="search_title">
                Search Results (Files)
            </div>

            <div class="search-result" id="awesome-search-result-container-file"></div>
            <div class="dmc-searching-gif-for-files" id="dmc-searching-gif-for-files" style="display:none;">
                <span><img src="<?php echo esc_url(get_site_url()); ?>/wp-admin/images/loading.gif" /> Searching...</span>

            </div>
            <div class="dmc-search-no-result-found" id="dmc-search-no-file-result-found" style="display:none;">
                No Result Found.
            </div>
        </div>
        <?php } ?>


    </div>
</div>