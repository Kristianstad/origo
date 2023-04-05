<!DOCTYPE html>
<?php
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	require_once("./functions/dbh.php");
	require_once("./functions/all_from_table.php");
	require_once("./functions/array_column_search.php");
	require_once("./functions/tableNamesFromSchema.php");
	$dbSchema=explode('.', $_GET['schema'], 2);
	unset($_GET);
	$database=$dbSchema[0];
	$schema=$dbSchema[1];
	$dbh_config=dbh();
	require("./constants/configSchema.php");
	$connectionString=array_column_search($database, 'database_id', all_from_table($dbh_config, $configSchema, 'databases'))['connectionstring'];
	$dbh=dbh($connectionString);
	unset($connectionString);
	foreach (tableNamesFromSchema($dbh, $schema) as $tableName)
	{
		$sql="INSERT INTO $configSchema.tables(table_id) VALUES ('$database.$schema.$tableName') ON CONFLICT (table_id) DO NOTHING;";
		if (!empty($sql))
		{
			$result=pg_query($dbh_config, $sql);
			if (!$result)
			{
				die("Error in SQL query: " . pg_last_error());
			}
			unset($result);
		}
	}
?>
