<?php

	function printConfigPreviewButton($mapId, $group=null, $layer=null)
	{
		if (isset($group))
		{
			$groupInput="<input type='hidden' name='group' value='$group'>";
		}
		if (isset($layer))
		{
			$layerInput="<input type='hidden' name='layer' value='$layer'>";
		}
		echo <<<HERE
			<form action="writeConfig2.php" method="get" target="_blank">
				<input type="hidden" name="getHtml" value="y">
				{$groupInput}
				{$layerInput}
				<button class="updateButton" type="submit" name="map" value="{$mapId}">
					Förhandsgranska
				</button>
			</form>
		HERE;
	}

?>
