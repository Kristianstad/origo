<!DOCTYPE html>
<html>
<head>
	<style>
		<?php require("./styles/updated.css"); ?>
	</style>
</head>
<body>
<?php
	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");

	// Expose specific functions
	require_once("./functions/includeDirectory.php");

	// Expose all functions in given folders
	includeDirectory("./functions/common");
	includeDirectory("./functions/updated");

	require("./constants/dbhConnectionStringForUpdated.php");
	$dbh=dbh($dbhConnectionStringForUpdated);
	$tablesWithSchema=$_GET['table'];
	$tablesWithSchema=explode(',', $tablesWithSchema);
	$updates=array();
	foreach ($tablesWithSchema as $tableWithSchema)
	{
		$updated=updated_from_table2($dbh, $tableWithSchema);
		if (isset($updated[1]))
		{
			$updates[$updated[0]]=$updated[1];
		}
	}
	arsort($updates);
	$updated=key($updates);
	$updated=substr($updated, 0, 10);
	echo $updated;
?>
