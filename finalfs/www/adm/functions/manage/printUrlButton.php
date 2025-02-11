<?php

	function printUrlButton($url)
	{
		$url=str_replace('#', '%23', $url);
		echo <<<HERE
			<form action="$url" method="get" target="_blank">
				<button title="Öppna karta i nytt fönster" type="submit">
					Öppna karta
				</button>
			</form>
		HERE;
	}

?>
