<!DOCTYPE html>
<?php 
	require_once("./functions/includeDirectory.php");
	require_once("./functions/toSwedish.php");
?>
<html>
<head>
	<!--<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>-->
	<script src="/origo/jquery/jquery-1.11.0.min.js?ttl=36000"></script>
	<script>
		<?php includeDirectory("./js-functions/multiselect"); ?>
		if (parseInt(navigator.appVersion)>3)
		{
			document.onmousedown = mouseDown;
			if (document.layers && navigator.appName=="Netscape")
			{
				document.captureEvents(Event.MOUSEDOWN);
			}
		}
	</script>
	<style>
		<?php require("./styles/multiselect.css"); ?>
	</style>
</head>
<body>
<?php
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	require_once("./functions/dbh.php");
	require_once("./functions/all_from_table.php");
	$dbh=dbh();
	$submitValue=explode(':', $_GET['table']);
	$table=$submitValue[0];
	if (empty($submitValue[1]))
	{
		$currentValue='';
		$dataSortedValues='';
	}
	else
	{
		$currentValue=$submitValue[1];
		$dataSortedValues=$currentValue.',';
	}
	$values=all_from_table($dbh, 'map_configs', $table);
	echo "<select onChange='update(this);' data-sorted-values='$dataSortedValues' multiple>";
	if ($table == 'proj4defs')
	{
		$idColumn='code';
	}
	else
	{
		$idColumn=rtrim($table, 's').'_id';
	}
	foreach (array_column($values, $idColumn) as $option)
	{
		$options="<option value='$option'";
		$options="$options>$option</option>";
		echo $options;
	}
	echo '</select>';
	$header=ucfirst(toSwedish($table));
	echo '<h3>'.$header.'</h3>';
	echo "<textarea readonly id='selection'>$currentValue</textarea>";
	echo '<button onClick="window.location.reload();">Återställ</button>&nbsp;';
	echo "<button onClick='document.querySelector(\"#selection\").innerHTML=null;document.querySelector(\"#selection\").value=null;'>Töm</button>&nbsp;";
	echo '<button onclick="copyTextById('."'selection');".'">Kopiera text</button>';
?>
</body>
</html>
