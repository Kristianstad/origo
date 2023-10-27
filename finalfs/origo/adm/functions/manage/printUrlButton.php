<?php

	function printUrlButton($url)
	{
		echo <<<HERE
			<form action="$url" method="get" target="_blank">
				<button type="submit">
					Öppna karta
				</button>
			</form>
		HERE;
	}

?>
