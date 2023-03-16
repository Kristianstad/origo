<?php

	function printDeleteButton($target, $deleteConfirmStr)
	{
		$targetType=key($target);
		$targetId=current($target);
		echo <<<HERE
			<div class="deleteButtonDiv">
				<form method='post' onsubmit='confirmStr="{$deleteConfirmStr}"; return confirm(confirmStr);' style='line-height:2'>
					<input type="hidden" name="{$targetType}IdDel" value="{$targetId}">
					<button class='deleteButton' type='submit' name='{$targetType}Button' value='delete'><img class="resizeimg" src onerror="this.parentNode.style.marginTop=this.parentNode.parentNode.parentNode.previousElementSibling.offsetHeight+'px'; this.parentNode.style.visibility='visible';">Radera</button>
				</form>
			</div>
		HERE;
	}
	
?>
