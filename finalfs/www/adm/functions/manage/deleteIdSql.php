<?php

	require_once("./functions/pkColumnOfTable.php");

	function deleteIdSql($id, $tableName)
	{
		require("./constants/configSchema.php");
		$tablePkColumn=pkColumnOfTable($tableName);
		return "DELETE FROM $configSchema.$tableName WHERE $tablePkColumn = '".$id."'";
	}

?>
