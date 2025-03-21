<?php

	function printReadSchemaTablesButton($schemaId)
	{
		$confirmStr="Läser in eventuella nya tabeller från schemat. Befintiga metadata påverkas ej.";
		echo <<<HERE
			<form onsubmit='confirmStr="{$confirmStr}"; confirm(confirmStr); setTimeout(function() {if (document.getElementById("schemas1HeadForm") != null) {document.getElementById("schemas1HeadForm").submit();} else {document.getElementById("schemasHeadForm").submit();}}, 1000);' action="read_schema_tables.php" method="get" target="hiddenFrame">
				<button title="Uppdatera verktyget med nya tabeller från schemat" class="updateButton" type="submit" name="schema" value="{$schemaId}">
					Läs in nya tabeller
				</button>
			</form>
		HERE;
	}

?>
