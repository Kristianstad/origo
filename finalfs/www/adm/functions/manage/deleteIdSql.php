<?php

	require_once("./functions/pkColumnOfTable.php");

	// Takes an id and a table name as parameters.
	// Returns a sql-query string for deleting the row in the table that has given id as primary key
	function deleteIdSql($id, $tableName)
	{
		require("./constants/configSchema.php");
		$tablePkColumn=pkColumnOfTable($tableName);
		return "DELETE FROM $configSchema.$tableName WHERE $tablePkColumn = '".$id."'";
	}

?>
