<?php

	require_once("./functions/pkColumnOfTable.php");

	function insertIdSql($id, $tableName)
	{
		require("./constants/configSchema.php");
		$tablePkColumn=pkColumnOfTable($tableName);
		return "INSERT INTO $configSchema.$tableName($tablePkColumn) VALUES ('".$id."')";
	}

?>
