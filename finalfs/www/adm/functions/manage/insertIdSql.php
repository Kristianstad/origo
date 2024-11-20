<?php

	require_once("./functions/pkColumnOfTable.php");

	// Takes an id and a table name as parameters.
	// Returns a sql-query string for creating a new row in the table with given id as primary key
	function insertIdSql($id, $tableName)
	{
		require("./constants/configSchema.php");
		$tablePkColumn=pkColumnOfTable($tableName);
		return "INSERT INTO $configSchema.$tableName($tablePkColumn) VALUES ('".$id."')";
	}

?>
