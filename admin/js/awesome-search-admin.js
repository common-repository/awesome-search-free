(function ($) {
	'use strict';

	var UI = {};

	var searchTab = {
		active: "db"
	};

	// this varialbe will be true if any search result found for a search string. 
	// every time the search button click - it will be false
	var dbSearchFoundAnyResult = false;

	var searchParams = {
		db: null,
		plugins : null,
		file: {
			plugins: null,
			theme: false,
			root: false
		}
	};

	var dmcUtilObject = {};
	dmcUtilObject.allFileListToSearch = [];
	dmcUtilObject.selectedPluginList = [];
	dmcUtilObject.allFileListInRootDir = [];


	dmcUtilObject.init = function(){
		UI.resultContainerDb = $("#awesome-search-result-container-db");
		UI.resultContainerFile = $("#awesome-search-result-container-file");
		UI.errorSearchString = $("#error-seacrh-string");
		UI.errorSearchDropdown = $("#error-seacrh-dropdown");
		UI.errorSeacrhInDbFiles = $("#error-seacrh-db-files");
		UI.allErrorMsg = $(".dmc-error-msg");
	}
	dmcUtilObject.isSearchActive = true;

	dmcUtilObject.getSearchString = function () {

		var string = $("#dmc-awesome-search-input").val();
		return string;

	}

	dmcUtilObject.showSearchingLoaderForDb = function(){

		$("#dmc-searching-gif-for-db").show();
	}

	dmcUtilObject.hideSearchingLoaderForDb = function(){

		$("#dmc-searching-gif-for-db").hide();
	}

	dmcUtilObject.showSearchingLoaderForFiles = function(){

		$("#dmc-searching-gif-for-files").show();
	}

	dmcUtilObject.hideSearchingLoaderForFiles = function(){

		$("#dmc-searching-gif-for-files").hide();
	}



	dmcUtilObject.showNoDbRecordFoundMsg = function(){

		$("#dmc-search-no-db-result-found").show();
	}

	dmcUtilObject.hideNoDbRecordFoundMsg = function(){

		$("#dmc-search-no-db-result-found").hide();
	}
	
	dmcUtilObject.showNoFileResultFoundMsg = function(){

		$("#dmc-search-no-file-result-found").show();
	}

	dmcUtilObject.hideNoFileResultFoundMsg = function(){

		$("#dmc-search-no-file-result-found").hide();
	}
	dmcUtilObject.showStopSearchButton = function(){

		$("span.dmc-stop-search").show();
	}
	dmcUtilObject.hideStopSearchButton = function(){

		$("span.dmc-stop-search").hide();
	}
	

	dmcUtilObject.getTableDropdownValues = function () {
		var dbFields = $("#input-db-fields").val();

		searchParams.db = null;
		if (dbFields.length) {
			searchParams.db = [];

			dbFields.forEach(function (dbColumnInfoJsonString, i) {

				console.log(dbColumnInfoJsonString);
				var tableFields = JSON.parse(dbColumnInfoJsonString);
				if (tableFields) {

				var tableFields = JSON.parse(dbColumnInfoJsonString);
					if(Array.isArray(tableFields)){

						tableFields.forEach(function (pluginTable, i) {
							searchParams.db.push(pluginTable);
						});						
					}else{
						searchParams.db.push(tableFields);
					}

					
				}
			});

			console.log(searchParams);

			return searchParams.db;

		}
		return dbFields;
	}

	dmcUtilObject.getSelectedPluginsFromDropwdown = function () {
		var dropdownValues = $("#input-db-fields").val();

		searchParams.db = null;
		if (dropdownValues.length) {
			searchParams.plugins = [];

			dropdownValues.forEach(function (arrayString, i) {

				console.log(arrayString);
				var items = JSON.parse(arrayString);
				if (items) {

					if(Array.isArray(items)){

						searchParams.plugins.push(items[0]);				
					}else{
						searchParams.plugins.push(items);
					}

					
				}
			});

			console.log(searchParams);

			return searchParams.plugins;

		}
		return dropdownValues;
	}


	dmcUtilObject.buildHtmlTableFromRows = function(rows){

		var tableId = "table" + new Date().getTime();
		var table = $('<table class="awesome-search-result-table" width="100%">').attr("id", tableId);

		if(rows.length > 0){

			var firstRow = rows[0];
			var thead = $("<thead>");
			thead.appendTo(table);

			var tbody = $("<tbody>");
			tbody.appendTo(table);


			var tr = $("<tr>");
			tr.appendTo(thead);

			Object.keys(firstRow).forEach(function(column, i){

				var th = $("<th>").text(column);
				th.appendTo(tr);

			});

			rows.forEach(function(row, i){
				var tr = $("<tr>");
				tr.appendTo(tbody);
				Object.keys(row).forEach(function(rowColumn, i){

					var td = $("<td>").html(row[rowColumn]);
					td.appendTo(tr);
	
				});

			});



		}

		return table;
		

	}


	dmcUtilObject.showDbResult = function (result) {


		var resultItemWrapper = $("<div>").addClass("result-item-wrapper").appendTo(UI.resultContainerDb);

		var rowHtml = $("<div>").addClass("result-item").appendTo(resultItemWrapper);

		//var resultDetailsWrapper = $("<div>").addClass("result-item-details").appendTo(resultItemWrapper).hide();

		var table = dmcUtilObject.buildHtmlTableFromRows(result.data.rows);
		
		//table.appendTo(resultDetailsWrapper);

		// var jsonData = [
		// 	{ "version": 1, "type": "test" } 
		// ];
		
		// $("table#"+tableId).DataTable({
		// 	"data": result.data.rows,
		// 	"columns": [
		// 	  { "data": "type" },
		// 	  { "data": "version" }
		// 	]
		// });



		var table = $("<span>").addClass("result-span result-table").html("<span style=\"font-weight:bold;\">Table:</span> " + result.data.table);
		table.appendTo(rowHtml);

		var table = $("<span>").addClass("result-span result-column").html(" <span style=\"font-weight:bold;\">Column:</span> " + result.data.column);
		table.appendTo(rowHtml);

		var totalFound = $("<span>").addClass("result-span total-found").html(" <span style=\"font-weight:bold;\">Total Rows:</span> " + result.data.total);
		totalFound.appendTo(rowHtml);

		var showDetails = $("<a href='https://awesomesearchwp.com' target='_blank'>").addClass("result-span show-details").html("Show Details (Get Pro version)");
		showDetails.data("label", "Show Details");
		showDetails.appendTo(rowHtml);

	}

	dmcUtilObject.showError = function (errorResult) {

	}


	dmcUtilObject.prepareAndShowDbSearchResult = function (result) {

		if (result.error) {
			// show error msg.
			dmcUtilObject.showError(result);

		} else {
			// has result - success
			if(result && result.data && result.data.total){
				// has a result 
				dbSearchFoundAnyResult = true;
				dmcUtilObject.showDbResult(result);
			}
			
		}

	}

	dmcUtilObject.dbSearchResultHandler = function (result, currentIndex, dbParams) {
		var currentIndex = parseInt(currentIndex);
		var nextIndex = currentIndex + 1;

		console.log(result);

		// Show search result in UI
		dmcUtilObject.prepareAndShowDbSearchResult(result);

		// start search for the next index 
		if (dbParams[nextIndex]) {
			dmcUtilObject.doAwesomeSearchInDatabase(dbParams, nextIndex);
		} else {
			
			// search finished - nothing to search
			// show not found msg if no result found
			if(!dbSearchFoundAnyResult){
				dmcUtilObject.showNoDbRecordFoundMsg();
			}
			dmcUtilObject.hideSearchingLoaderForDb();
			dmcUtilObject.hideStopSearchButton();

			// enable search button
			jQuery("#dmc-awesome-search-submit").removeAttr("disabled");			
		}
	}

	dmcUtilObject.doAwesomeSearchInDatabase = function (dbParams, index = 0) {



		var searchString = dmcUtilObject.getSearchString().trim();
		if (searchString.length == 0) {
			UI.errorSearchString.show();
			return false;
		}

		var searchInDb = $("#search-in-database").is(':checked') ? "yes" :"no";
		var searchInFiles = $("#search-in-files").is(':checked') ? "yes" :"no";

		if(searchInDb == "no" && searchInFiles == "no"){
			UI.errorSeacrhInDbFiles.show();
			return false;
		}

		if (dbParams[index] && dmcUtilObject.isSearchActive) {

			dmcUtilObject.showSearchingLoaderForDb(); 
			dmcUtilObject.showStopSearchButton();
			var searchItem = dbParams[index];

			// disable search button
			jQuery("#dmc-awesome-search-submit").attr("disabled",true);	

			var data = {
				'action': 'do_awesome_search_in_database_free',
				'searchParam': searchItem,
				'searchString': searchString,
				'search-in-database' : searchInDb,
				'search-in-files' : "no"
			};

			jQuery.post(ajaxurl, data, function (response) {
				dmcUtilObject.dbSearchResultHandler(response, index, dbParams);

			}, "json");

		} else {
			dmcUtilObject.hideSearchingLoaderForDb();
			dmcUtilObject.hideStopSearchButton();

			// enable search button
			jQuery("#dmc-awesome-search-submit").removeAttr("disabled");			
		}
	}
	


	dmcUtilObject.showFileResult = function (response) {

		var filePath = response.filePath;
		var result = response.result;
		var total_found_in_file = result.total_found_in_file;
		var lines = result.lines;


		var resultItemWrapper = $("<div>").addClass("result-item-wrapper").appendTo(UI.resultContainerFile);

		var resultHtml = $("<div>").addClass("result-item").appendTo(resultItemWrapper);

		var filePathDiv = $("<div>").addClass("dmc-file-path").appendTo(resultHtml);
		var pathLabel = $("<span>").addClass("dmc-file-path-label").text("File : ");
		var fileName = $("<span>").addClass("dmc-file-path-name").text(filePath);
		pathLabel.appendTo(filePathDiv);
		fileName.appendTo(filePathDiv);

		var totalFoundDiv = $("<div>").addClass("dmc-file-path").appendTo(resultHtml);
		var totalFoundLabel = $("<span>").addClass("dmc-total-found-label").text("Total Found : ");
		var totalFoundCount = $("<span>").addClass("dmc-total-found-count").text(total_found_in_file);
		totalFoundLabel.appendTo(totalFoundDiv);
		totalFoundCount.appendTo(totalFoundDiv);

		var showDetails = $("<a href='https://awesomesearchwp.com' target='_blank'>").addClass("result-span show-details").html("Show Details (Get Pro version)");
		showDetails.data("label", "Show Details");
		showDetails.appendTo(totalFoundDiv);


	}

	dmcUtilObject.itemLineDetails = function(item){

		var htmlLines = $("<div>").addClass("lines-html");

		var previousLines = item.previousLines;
		var nextLines = item.nextLines;

		Object.keys(previousLines).forEach(linenumber => {

			var lineItem = $("<div>").addClass("line-content-inner").appendTo(htmlLines);
			var lineNumber = $("<div>").addClass("line-number-inner").appendTo(lineItem);//.text(linenumber);
			var lineText = $("<div>").addClass("line-text").appendTo(lineItem).html(previousLines[linenumber]);

		});

		var lineItem = $("<div>").addClass("line-content-inner").appendTo(htmlLines);
		var lineNumber = $("<div>").addClass("line-number-inner").appendTo(lineItem);//.text(item.lineNumber);
		var lineText = $("<div>").addClass("line-text").appendTo(lineItem).html(item.line);



		Object.keys(nextLines).forEach(linenumber => {
			var lineItem = $("<div>").addClass("line-content-inner").appendTo(htmlLines);
			var lineNumber = $("<div>").addClass("line-number-inner").appendTo(lineItem);//.text(linenumber);
			var lineText = $("<div>").addClass("line-text").appendTo(lineItem).html(nextLines[linenumber]);

		});

		return htmlLines;


	}


	dmcUtilObject.prepareAndShowFileSearchResult = function (response) {

		console.log(response);

		if(response && response.result && response.result.found){
			
			dmcUtilObject.showFileResult(response);
			
		}

	}



	dmcUtilObject.fileSearchResultHandler = function (response) {


		// Show search result in UI
		dmcUtilObject.prepareAndShowFileSearchResult(response);


	}

	dmcUtilObject.doAwesomeSearchInFiles = function( index = 0, nextPluginIndex){


		if(index == 1){
			//index = 1950; // fro debugging
		}
		var nextIdex = index + 1;
		var searchString = dmcUtilObject.getSearchString().trim();
		if (searchString.length == 0) {
			UI.errorSearchString.show();
			return false;
		}

		var searchInDb = $("#search-in-database").is(':checked') ? "yes" :"no";
		var searchInFiles = $("#search-in-files").is(':checked') ? "yes" :"no";

		if(searchInDb == "no" && searchInFiles == "no"){
			UI.errorSeacrhInDbFiles.show();
			return false;
		}

		if (dmcUtilObject.allFileListToSearch[index] && dmcUtilObject.isSearchActive) {

			dmcUtilObject.showSearchingLoaderForFiles(); 
			dmcUtilObject.showStopSearchButton();
			var filePath = dmcUtilObject.allFileListToSearch[index];

			var data = {
				'action': 'do_awesome_search_in_files_free',
				'filePath': filePath,
				'searchString': searchString,
				'search-in-database' : "no",
				'search-in-files' : searchInFiles
			};

			// disable search button
			jQuery("#dmc-awesome-search-submit").attr("disabled",true);			
			

			jQuery.post(ajaxurl, data, function (response) {
				dmcUtilObject.fileSearchResultHandler(response);
				dmcUtilObject.hideSearchingLoaderForFiles();
				dmcUtilObject.hideStopSearchButton();
				// recursive call for the search.
				dmcUtilObject.doAwesomeSearchInFiles(nextIdex, nextPluginIndex);

			}, "json");

		} else {
			dmcUtilObject.getPluginFileLists(nextPluginIndex);
			// enable search button
			jQuery("#dmc-awesome-search-submit").removeAttr("disabled");			
		}


	}

	dmcUtilObject.doAwesomeSearchInRootFiles = function( index = 0){


		var nextIndex = index + 1;
		var searchString = dmcUtilObject.getSearchString().trim();
		if (searchString.length == 0) {
			UI.errorSearchString.show();
			return false;
		}

		var searchInFiles = $("#search-in-root").is(':checked') ? "yes" :"no";

		if (dmcUtilObject.allFileListInRootDir[index] && dmcUtilObject.isSearchActive) {

			dmcUtilObject.showSearchingLoaderForFiles(); 
			dmcUtilObject.showStopSearchButton();
			var filePath = dmcUtilObject.allFileListInRootDir[index];

			var data = {
				'action': 'do_awesome_search_in_files_free',
				'filePath': filePath,
				'searchString': searchString,
				'search-in-database' : "no",
				'search-in-files' : searchInFiles
			};

			// disable search button
			jQuery("#dmc-awesome-search-submit").attr("disabled",true);	

			jQuery.post(ajaxurl, data, function (response) {
				dmcUtilObject.fileSearchResultHandler(response);
				dmcUtilObject.hideSearchingLoaderForFiles();
				dmcUtilObject.hideStopSearchButton();
				// recursive call for the search.
				dmcUtilObject.doAwesomeSearchInRootFiles(nextIndex);

			}, "json");

		} else {
			//dmcUtilObject.getPluginFileLists(nextPluginIndex);

			// enable search button
			jQuery("#dmc-awesome-search-submit").removeAttr("disabled");			
		}


	}


	dmcUtilObject.getPluginFileLists = function(index){

		
		var nextPluginIndex = index + 1;
		if (dmcUtilObject.selectedPluginList[index] && dmcUtilObject.isSearchActive) {

			dmcUtilObject.showSearchingLoaderForFiles(); 
			var pluginListItem = dmcUtilObject.selectedPluginList[index];

			var data = {
				'action': 'get_file_list_in_plugin_free',
				'searchParam': pluginListItem,
				'file_list' : 'yes'
			};

			// disable search button
			jQuery("#dmc-awesome-search-submit").attr("disabled",true);

			jQuery.post(ajaxurl, data, function (response) {

				var totalFiles = response.total;
				var is_plugin = response.is_plugin;

				if(totalFiles > 0 && is_plugin){
					dmcUtilObject.allFileListToSearch = response.files;
					console.log(response);
					dmcUtilObject.doAwesomeSearchInFiles(0, nextPluginIndex);
				}else{
					dmcUtilObject.getPluginFileLists(nextPluginIndex);
				}

			}, "json");

		} else {

			dmcUtilObject.hideSearchingLoaderForFiles();
			// enable search button
			jQuery("#dmc-awesome-search-submit").removeAttr("disabled");

		}

	}


	dmcUtilObject.getRootFileLists = function(){

		
		//var nextPluginIndex = index + 1;
		if ( dmcUtilObject.isSearchActive) {

			dmcUtilObject.showSearchingLoaderForFiles(); 
			var param = {is_root : 1};

			var data = {
				'action': 'get_file_list_in_root_dir_free',
				'searchParam': param,
				'file_list' : 'yes'
			};


			jQuery.post(ajaxurl, data, function (response) {

				var totalFiles = response.total;
				var is_root = response.is_root;

				if(totalFiles > 0 && is_root){
					dmcUtilObject.allFileListInRootDir = response.files;
					console.log(response);
					dmcUtilObject.doAwesomeSearchInRootFiles(0);
				}else{
					//dmcUtilObject.getPluginFileLists(nextPluginIndex);
				}
				dmcUtilObject.hideSearchingLoaderForFiles();
			}, "json");

		} else {

			//console.log("Search file list end"); 
			//console.log("Last Index = " + index);
			// start search in fiels from dmcUtilObject.allFileListToSearch

		}

	}	



	$(document).ready(function () {

		// init all predefined selectors and vars
		dmcUtilObject.init();

		/*
		$(".dmc-awesome-tab").on("click", function () {

			var tab = $(this).data("tab");
			// highlight active tab 
			$(".dmc-awesome-tab").removeClass("nav-tab-active");
			$(this).addClass("nav-tab-active");

			// save the current active tab
			searchTab.active = tab;

			// hide all tabs content
			$(".media-toolbar-secondary").hide();

			// show only clicked tab content
			$(".media-toolbar-secondary." + tab).show();

		});
		*/

		$("#dmc-select-all").on("change",function(){
			var selectall = $(this).is(':checked');
			if(selectall){
				$("#input-db-fields").find("option").prop('selected', true);
			}else{
				$("#input-db-fields").find("option").prop('selected', false);
			}

		});

		// on stop search button click
		$("span.dmc-stop-search").on("click",function(){

			dmcUtilObject.isSearchActive = false;
		});

		// on search button click
		$("#dmc-awesome-search-submit").on("click", function () {

			var searchInDb = $("#search-in-database").is(':checked') ? "yes" :"no";
			var searchInFiles = $("#search-in-files").is(':checked') ? "yes" :"no";
			var searchInRoot = $("#search-in-root").is(':checked') ? "yes" :"no";

			UI.resultContainerDb.empty();
			UI.resultContainerFile.empty();
			UI.allErrorMsg.hide();

			// reset vars
			dmcUtilObject.isSearchActive = true;
			dbSearchFoundAnyResult = false; // first time set it false = no result found yet.
			dmcUtilObject.hideNoDbRecordFoundMsg();
			
			// reset vars end

			var dbParams = dmcUtilObject.getTableDropdownValues();
			dmcUtilObject.selectedPluginList = dmcUtilObject.getSelectedPluginsFromDropwdown();


			var searchString = dmcUtilObject.getSearchString().trim();
			if (searchString.length <= 1) {
				UI.errorSearchString.show();
				return false;
			}

			if(!dbParams.length && searchInRoot == "no"){
				UI.errorSearchDropdown.show();
				return false;
			}

			if(searchInDb == "no" && searchInFiles == "no" && searchInRoot == "no"){
				UI.errorSeacrhInDbFiles.show();
				return false;
			}


			dmcUtilObject.showStopSearchButton();

			if (searchInDb == "yes") {
				dmcUtilObject.doAwesomeSearchInDatabase(dbParams, 0);
			}

			if (searchInFiles == "yes") {
				dmcUtilObject.getPluginFileLists( 0 );
			}

			if (searchInRoot == "yes") {
				dmcUtilObject.getRootFileLists( 0 );
			}	

		});



	});

})(jQuery);