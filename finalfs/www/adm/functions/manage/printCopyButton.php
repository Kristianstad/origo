<?php

	require_once("./functions/toSwedish.php");

	// Takes a type (string) and prints a form submit-button with class="updateButton", name="<type>Button", value="copy", and the label "Spara kopia".
	function printCopyButton($type)
	{
		$typeSwe=toSwedish($type);
		echo '<button title="Spara kopia av '.$typeSwe.' till databas" class="updateButton" type="submit" name="'.$type.'Button" value="copy">Spara kopia</button>';
	}

?>
