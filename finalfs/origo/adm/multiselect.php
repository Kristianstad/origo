<!DOCTYPE html>
<?php require_once("./functions/includeDirectory.php"); ?>
<html>
<head>
	<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
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
	$values=all_from_table($dbh, 'map_configs', $_GET['table']);
	echo '<select onChange="update(this);" data-sorted-values="" multiple>';
	if ($_GET['table'] == 'proj4defs')
	{
		$idColumn='code';
	}
	else
	{
		$idColumn=rtrim($_GET['table'], 's').'_id';
	}
	foreach (array_column($values, $idColumn) as $option)
	{
		$options="<option value='$option'";
		$options="$options>$option</option>";
		echo $options;
	}
	echo '</select>';
	if ($_GET['table'] == 'controls')
	{
		$header='Kontroller';
	}
	elseif ($_GET['table'] == 'groups')
	{
		$header='Grupper';
	}
	elseif ($_GET['table'] == 'layers')
	{
		$header='Lager';
	}
	else
	{
		$header=ucfirst($_GET['table']);
	}
	echo '<h3>'.$header.'</h3>';
	echo '<textarea readonly id="selection"></textarea>';
	echo '<button onClick="window.location.reload();">Töm</button>&nbsp;';
	echo '<button onclick="copyTextById('."'selection');".'">Kopiera text</button>';
?>
</body>
</html>
