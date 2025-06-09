<?php

	// Takes a type (string) and prints a form submit-button with class="updateButton", name="<type>Button", value="update", and the label "Uppdatera".
	function printUpdateButton($type)
	{
		GLOBAL $formChangedGlobal;
		if ($formChangedGlobal)
		{
			$formChangedGlobal=false;
			$changeClass=' change';
		}
		else
		{
			$changeClass='';
		}
		echo '<button title="Skriv ändringar till databas" class="updateButton'.$changeClass.'" type="submit" name="'.$type.'Button" value="update">Uppdatera</button>';
	}

?>
