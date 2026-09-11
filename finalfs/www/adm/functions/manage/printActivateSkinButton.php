<?php

function printActivateSkinButton(string $skinId): void
{
	$skinIdJson=json_encode($skinId, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
	$skinIdJson=htmlspecialchars($skinIdJson, ENT_QUOTES, 'UTF-8');
	echo <<<HTML
		<button title="Byt till detta utseende" type="button" onclick="document.cookie='origo_admin_skin='+encodeURIComponent({$skinIdJson})+';path=/;max-age=31536000;samesite=lax';location.reload();">Byt till detta utseende</button>
HTML;
}

?>
