<?php

	function printExportJsonButton($mapId)
	{
		echo <<<HERE
			<form action="writeConfig.php" method="get" target="hiddenFrame">
				<input type="hidden" name="getJson" value="y">
				<input type="hidden" name="download" value="y">
				<button title="Ladda ner konfiguration" class="updateButton" type="submit" name="map" value="{$mapId}">
					Exportera JSON
				</button>
			</form>
		HERE;
	}

?>
