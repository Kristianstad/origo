<!DOCTYPE html>
<?php
	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	
	// Expose specific functions
	require_once("./functions/includeDirectory.php");
	
	// Expose all functions in given folders
	includeDirectory("./functions/common");
	includeDirectory("./functions/read_db_schemas");
	
	$database=$_GET['database'];
	unset($_GET);
	$dbh_config=dbh();
	require("./constants/configSchema.php");
	$connectionString=array_column_search($database, 'database_id', all_from_table($dbh_config, $configSchema, 'databases'))['connectionstring'];
	$dbh=dbh($connectionString);
	unset($connectionString);
	foreach (schemaNamesFromDb($dbh) as $schemaName)
	{
		$sql="INSERT INTO $configSchema.schemas(schema_id) VALUES ('$database.$schemaName') ON CONFLICT (schema_id) DO NOTHING;";
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
	pg_flush($dbh);
?>
