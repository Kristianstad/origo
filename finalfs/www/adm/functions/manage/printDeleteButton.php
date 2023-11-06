<?php

	function printDeleteButton($target, $deleteConfirmStr, $inheritPosts)
	{
		$targetType=key($target);
		$targetId=current($target);
		if ($targetType == 'map')
		{
			$inheritPosts=array();
		}
		elseif ($targetType == 'group')
		{
			$groupIdsArr=explode(',', $inheritPosts['groupIds']);
			foreach (array_reverse($groupIdsArr, true) as $k => $v)
			{
				unset($groupIdsArr[$k]);
				if ($v == $targetId)
				{
					break;
				}
			}
			$inheritPosts['groupIds']=implode(',', $groupIdsArr);
		}
		else
		{
			foreach ($inheritPosts as $k => $v)
			{
				unset($inheritPosts[$k]);
				if ($k == $targetType.'Id' && $v == $targetId)
				{
					break;
				}
			}
		}
		echo <<<HERE
				<form method='post' onsubmit='confirmStr="{$deleteConfirmStr}"; return confirm(confirmStr);'>
					<input type="hidden" name="{$targetType}IdDel" value="{$targetId}">
					<button class='deleteButton' type='submit' name='{$targetType}Button' value='delete'>Radera</button>
		HERE;
		printHiddenInputs($inheritPosts);
		echo '</form>';
	}

?>
