<?php

	function printUrlButton($url)
	{
		echo <<<HERE
			<form>
				<button title="Öppna karta i nytt fönster" type="button" onclick="window.open('$url', '_blank')">
					Öppna karta
				</button>
			</form>
		HERE;
	}

?>
