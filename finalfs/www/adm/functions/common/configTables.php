<?php

	// Uses common functions: tableNamesFromSchema, all_from_table

	// Takes a pg_connect database handle as parameter, reads a schema name from constant configSchema.php, 
	// and returns an associative array with all tables in the database schema. The array keys holds the table 
	// names while the associated values holds the "configurations"
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
