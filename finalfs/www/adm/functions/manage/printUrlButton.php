<?php

	function printUrlButton($url, $type)
	{
		$typeSwe=toSwedish($type);
		echo <<<HERE
			<form>
				<button title="Öppna {$typeSwe} i nytt fönster" type="button" onclick="window.open('$url', '_blank')">
					Öppna {$typeSwe}
				</button>
			</form>
		HERE;
	}

?>
