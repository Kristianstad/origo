<?php

	// Takes a basic target array.
	// Prints a form with a button labeled "Info" as only visible element. The button toggles the topFrame-iframe with contents from info.php.
	// The type and id of the given target is posted (method=get) to info.php
	function printInfoButton($basicTarget)
	{
		$type=key($basicTarget);
		$id=current($basicTarget);
		echo <<<HERE
			<form></form>
			<form action="info.php" method="get" target="topFrame">
				<input type="hidden" name="type" value="{$type}">
				<button title="Visa/dölj ytterligare information" class="updateButton" onclick="toggleTopFrame('info');" type="submit" name="id" value="{$id}">
					Info
				</button>
			</form>
		HERE;
	}

?>
