<?php

	// Takes a type (string) and prints a form submit-button with class="updateButton", name="<type>Button", value="update", and the label "Uppdatera".
	function printUpdateButton($type)
	{
		echo '<button title="Skriv ändringar till databas" class="updateButton" type="submit" name="'.$type.'Button" value="update">Uppdatera</button>';
	}

?>
