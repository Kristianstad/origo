<?php

	function printExportJsonButton($mapId)
	{
		echo <<<HERE
			<form action="writeConfig.php" method="get" target="hiddenFrame">
				<input type="hidden" name="getJson" value="y">
				<input type="hidden" name="download" value="y">
				<button class="exportButton" type="submit" name="map" value="{$mapId}">
					Exportera JSON
				</button>
			</form>
		HERE;
	}

?>
