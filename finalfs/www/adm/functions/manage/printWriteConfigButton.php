<?php

	// Takes a map-id and (optionally) a $changed-value, and prints a "Write configuration to disk (json)"-button.
	function printWriteConfigButton($mapId, $changed='f')
	{
		if ($changed == 't')
		{
			$changeClass=' change';
		}
		else
		{
			$changeClass='';
		}
		$confirmStr="Är du säker att du vill skriva över den befintliga konfigurationen för $mapId?";
		echo <<<HERE
			<form onsubmit='confirmStr="{$confirmStr}"; if (confirm(confirmStr)) {this.children[0].classList.remove("change"); return true;} else {return false;}' action="writeConfig.php" method="get" target="hiddenFrame">
				<button title="Skriv konfiguration till disk (json)" class="updateButton{$changeClass}" type="submit" name="map" value="{$mapId}">
					Skriv kartkonfiguration
				</button>
			</form>
		HERE;
	}

?>
