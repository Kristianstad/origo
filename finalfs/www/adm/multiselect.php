<!DOCTYPE html>
<?php
	// Tell browsers to not cache response
	header("Cache-Control: must-revalidate, max-age=0, s-maxage=0, no-cache, no-store");
	
	// Expose specific functions
	require_once("./functions/includeDirectory.php");
	
	// Expose all functions in given folders
	includeDirectory("./functions/common");
?>
<html>
<head>
	<script>
		<?php includeDirectory("./js-functions/multiselect"); ?>
        // Initialize event listeners
        $(document).ready(function() {
            document.addEventListener('mousedown', handleMouseDown);
            $('#clear-button').on('click', function() {
                $('#selection').val('').text('');
                $('select[multiple]').val([]).trigger('change');
            });
            $('#reset-button').on('click', function() {
                window.location.reload();
            });
        });
	</script>
	<style>
		<?php require("./styles/multiselect.css"); ?>
	</style>
</head>
<body>
<?php
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
	echo "<select id='selectbox' onChange='update(this);' data-sorted-values='$dataSortedValues' multiple>";
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
	if (!empty($currentValue))
	{
		echo '<button onClick="window.location.reload();">Återställ</button>&nbsp;';
	}
	echo "<button onClick='document.querySelector(\"#selection\").innerHTML=null;document.querySelector(\"#selection\").value=null;document.querySelector(\"#selectbox\").setAttribute(\"data-sorted-values\", \"\");document.querySelector(\"#selectbox\").value=\"\";document.querySelector(\"#selectbox\")?.querySelectorAll(\"option\").forEach(o => o.removeAttribute(\"selected\"));'>Töm</button>&nbsp;";
	if (!empty($_SERVER['HTTPS']) || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == '443'))
	{
		echo '<button onclick="copyTextById('."'selection');".'">Kopiera text</button>';
	}
?>
</body>
</html>
