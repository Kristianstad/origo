<?php

	function printReadDbSchemasButton($databaseId)
	{
		$confirmStr="Läser in eventuella nya scheman från databasen (utan tabeller). Befintiga metadata påverkas ej.";
		echo <<<HERE
			<form id='readDbSchemas' onsubmit='confirmStr="{$confirmStr}"; confirm(confirmStr); setTimeout(function() {document.getElementById("databasesHeadForm").submit();}, 1000);' action="read_db_schemas.php" method="get" target="hiddenFrame">
				<button title="Uppdatera verktyget med nya scheman från databasen" class="updateButton" type="submit" name="database" value="{$databaseId}">
					Läs in nya scheman
				</button>
			</form>
		HERE;
	}

?>
