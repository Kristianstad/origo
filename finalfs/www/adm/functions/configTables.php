<?php

	require_once("./functions/tableNamesFromSchema.php");
	require_once("./functions/all_from_table.php");

	function configTables(&$dbh)
	{
		require("./constants/configSchema.php");
		$configTables=array_flip(tableNamesFromSchema($dbh, $configSchema));
		foreach ($configTables as $table => $content)
		{
			$configTables[$table]=all_from_table($dbh, $configSchema, $table);
		}
		return $configTables;
	}
	
?>
