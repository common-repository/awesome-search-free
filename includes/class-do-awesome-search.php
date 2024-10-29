<?php

class DoAwesomeSearchFree {

	public $result;
	public $wpdb;
	public function __construct() {
	}

	/**
	 * Prepare post data and apply the search in db. return result or error msg.
	 */
	public function doDbSearch($searchString, $searchParam) {

		$table = $searchParam['table'];
		$column = $searchParam['column'];
		$name = $searchParam['name'];

		$resultData = array();
		$resultData['table'] = $table;
		$resultData['column'] = $column;
		$resultData['name'] = $name;
		$actualTableName = $this->getTableNameIfExist($table);

		if ($actualTableName) {
			$sql = $this->prepareSearchQuery($actualTableName, $column, $searchString);
			$rows = $this->getSearchResult($sql);
			$total = count($rows);
			$rows = $this->arrayInHtmlEntities($rows, $searchString);
			$resultData['rows'] = $rows;
			$resultData['total'] = $total;
			$result = $this->prepareDbResultResponse($resultData, false);
		} else {

			$resultData['error_msg'] = "Table does not exist";
			$result = $this->prepareDbResultResponse($resultData, true);
		}

		return $result;
	}

	/**
	 * Check if Table is exist, return the table name or false
	 * table : table name - without prefix
	 */
	public function getTableNameIfExist($table) {
		global $wpdb;
		$table = trim($table, "_");
		//$wpdb->show_errors( true );

		$tablePrefix = $wpdb->prefix;
		$tablePrefixLength = strlen($tablePrefix);

		$actualTableName = $table;

		$hasPrefix = substr($table, 0, $tablePrefixLength) == $tablePrefix;

		if (!$hasPrefix) {
			$actualTableName = $tablePrefix . $table;
		}

		$tableName = $wpdb->get_var("SHOW TABLES LIKE '%$actualTableName%'");

		if (strlen($tableName)) {
			return $tableName;
		}
		return false;
	}

	/**
	 * Prepare the search query from the request vars. 
	 * table : table name with prefix
	 * column : single column name or list of column as array
	 * searchString : request param - search string
	 * Return SQL query.
	 */
	public function prepareSearchQuery($table, $column, $searchString) {

		$columnQuery = array();
		$columnLists = $column;

		$allTableColumns = $this->getAllColumnsFromTable($table);

		// if all column (*) specified in json
		if ($column == "*") {
			$column = $allTableColumns;
		}

		if (is_array($column)) { // if multiple column specified in json

			foreach ($column as $c) {
				// if column really exists in table
				if (in_array($c, $allTableColumns)) {
					$columnQuery[] = $c . " LIKE '%$searchString%'";
				}
			}
		} else { // if only single column specified in json
			$columnQuery[] = $column . " LIKE '%$searchString%'";
		}

		$whereColumnsLike = implode(" or ", $columnQuery);

		if (is_array($column)) {

			$column = array_intersect($column, $allTableColumns);
			$columnLists = implode(",", $column);
		}

		return  "SELECT $columnLists FROM $table WHERE " . $whereColumnsLike;
	}

	public function getAllColumnsFromTable($table) {
		global $wpdb;
		// An array of Field names
		return $wpdb->get_col("DESC " . $table, 0);
	}

	/**
	 * Execute the search sql query and return result.
	 * sql : raw sql
	 */
	public function getSearchResult($sql) {
		global $wpdb;

		return $wpdb->get_results($sql, ARRAY_A);
	}

	/**
	 * Format the result - and it will be passed as search response.
	 * dataOrMsg : result array or error msg
	 * error : true or false - default false
	 */
	public function prepareDbResultResponse($dataOrMsg, $error = false) {

		$result = array();
		$result['error'] = $error;
		$result['data'] = array();

		if ($error) {
			$result['status'] = "Failed";
			$result['data'] = $dataOrMsg;
		} else {

			$result['data'] = $dataOrMsg;
			$result['status'] = "Success";
		}

		return $result;
	}

	public function arrayInHtmlEntities($arrayData, $searchString) {
		$rows = array();
		if (is_array($arrayData)) {

			foreach ($arrayData as  $index =>  $row) {

				foreach ($row as  $key =>  $data) {

					$rows[$index][$key] = $this->stringWrapInSpan(htmlentities($data), $searchString);
				}
			}
		}

		return $rows;
	}

	public function searchInFile($filePath, $searchString) {

		$config = get_awesome_search_config_vars_free();
		$PNRange = $config['SHOW_LINES_BEFORE_AFTER_SEARCH_LINE'];
		$lines = file($filePath);
		$result = array("found" => 0);
		$stringLength = strlen($searchString);
		$count = -1;
		$totalFoundInFiles = 0;
		foreach ($lines as $lineNumber => $line) {

			$lineText = trim($line, " \t\n\r");

			$pos = stripos($lineText, $searchString);
			if ($pos !== false) {

				$previousNextLines = $this->getPreviousAndNextLines($lines, $lineNumber, $PNRange);
				$count++; // set found index

				$lineTextHtmlOff = htmlentities($lineText);
				$totalFindInLine = substr_count(strtolower($lineText), strtolower($searchString));

				$lineTextPlain = $this->stringWrapInSpan($lineTextHtmlOff, $searchString);
				$result['lines'][$count]['lineNumber'] = $lineNumber + 1;
				$result['lines'][$count]['pos'] = $pos;
				$result['lines'][$count]['line'] = $lineTextPlain;
				$result['lines'][$count]['string_length'] = $stringLength;
				$result['lines'][$count]['total_found_in_line'] = $totalFindInLine;
				$result['lines'][$count]['previousLines'] = $previousNextLines['previousLines'];
				$result['lines'][$count]['nextLines'] = $previousNextLines['nextLines'];

				$totalFoundInFiles += $totalFindInLine;
			}
		}

		$result['total_found_in_file'] = $totalFoundInFiles;
		$result['found'] = $count > -1;
		return $result;
	}

	public function stringWrapInSpan($line, $string) {
		return preg_replace("/($string)/i", "<span class='found-string'>$1</span>", $line);
	}

	public function getPreviousAndNextLines($lines, $lineIndex, $PNRange = 3) {

		$previousLines = array();
		$nextLines = array();

		$nextPrevious = array("previousLines" => array(), "nextLines" => array());
		$iteratorCount = $PNRange + 1; // as i start with 1
		if ($lineIndex >= 0 && $PNRange > 0) {

			for ($i = 1; $i < $iteratorCount; ++$i) {

				$previousLineIndex = $lineIndex - $i;
				$nextLineIndex = $lineIndex + $i;

				$previousLineNumber = $previousLineIndex + 1;
				$nextLineNumber = $nextLineIndex + 1;


				if (isset($lines[$previousLineIndex]) && strlen($lines[$previousLineIndex])) {
					$previousLines[$previousLineNumber] = htmlentities($lines[$previousLineIndex]);
				}

				if (isset($lines[$nextLineIndex]) && strlen($lines[$nextLineIndex])) {
					$nextLines[$nextLineNumber] = htmlentities($lines[$nextLineIndex]);
				}
			}

			$nextPrevious = array("previousLines" => $previousLines, "nextLines" => $nextLines);
		}

		return $nextPrevious;
	}
}
