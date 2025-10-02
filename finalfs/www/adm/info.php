<!DOCTYPE html>
<html>
<head>
	<style>
		<?php require("./styles/info.css"); ?>
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
	includeDirectory("./functions/info");
	
	require("./constants/configSchema.php");
	$dbh=dbh();
	$childType=$_GET['type'];
	$childId=$_GET['id'];
	$childTypeSv=toSwedish($childType);
	if (!empty($childId))
	{
		echo "<div>";
		echo "<h2>$childId</h2> ($childTypeSv)</br>";
		$allOfChildType=all_from_table($dbh, $configSchema, $childType.'s');
		$child=array($childType=>$childId);
		$childFull=array_column_search($childId, pkColumnOfTable($childType.'s'), $allOfChildType);
		if (!empty($childFull['name']))
		{
			echo "<b>Namn: </b>".$childFull['name']."</br>";
		}
		if (!empty($childFull['alias']))
		{
			echo "<b>Alias: </b>".$childFull['alias']."</br>";
		}
		if (!empty($childFull['info']))
		{
			echo $childFull['info']."</br>";
		}
		if ($childType == 'source')
		{
			$services=all_from_table($dbh, $configSchema, 'services');
			$serviceType=array_column_search($childFull['service'], pkColumnOfTable('services'), $services)['type'];
			if (strtolower($serviceType) == 'qgis')
			{
				$qgsXml = simplexml_load_file('/services/'.$childFull['service'].'/'.explode('#', $childId)[0].'.qgs');
				if (!empty($qgsXml))
				{
					echo "<b>Qgis-version: </b>".$qgsXml['version']."<br>";
					echo "<b>Senast uppdaterad: </b>".$qgsXml['saveDateTime'].", ".$qgsXml['saveUserFull']."<br>";
				}
			}
		}
		if ($childType == 'aduser')
		{
			printUniqueLogins(array_column($allOfChildType, 'lastlogin'));
		}
		$allParents=findAllParents($dbh, $child);
		if (!empty(array_values($allParents)))
		{
 			echo "<h3 style='margin-top:0.5em'>Används av</h3></br>";
			printParents($allParents);
		}
		echo '</div>';
		if (strpos($_SERVER['HTTP_REFERER'], 'info.php') !== false)
		{
			echo '<button style="margin-left:0.3em" onclick="history.back()">Tillbaks</button>';
		}
		echo "<form action='manage.php' method='post' target='_blank' style='display:inline;margin-left:0.2em'><button type='submit' name='".$childType."Id' value='".$childId."'>Administrera</button></form>";
	}
?>
</body>
</html>
