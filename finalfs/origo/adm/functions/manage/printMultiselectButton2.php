<?php

	function printMultiselectButton2($target, $table)
	{
		echo <<<HERE
			<button form="{$target}MultiselectForm" onclick="toggleTopFrame('{$table}');" type="submit" name="table" value="{$table}" style="margin-left:-0.5em">
				+
			</button>
		HERE;
	}

?>
