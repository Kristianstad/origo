<!DOCTYPE html>
<html>
<head>
	<style>
		<?php require("./styles/updated.css"); ?>
	</style>
</head>
<body>
<?php
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");

	require_once("./functions/dbh.php");
	require_once("./functions/includeDirectory.php");

	includeDirectory("./functions/updated");

	$dbhConnectionString = "host=geodata port=5432 dbname=geodata user=titta password=titta";
	$dbh=dbh($dbhConnectionString);
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
