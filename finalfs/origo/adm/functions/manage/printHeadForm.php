<?php

	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/manage/printSelectOptions.php");
	require_once("./functions/manage/printHiddenInputs.php");

	function printHeadForm($table, $inheritPosts)
	{
		$tableName=key($table);
		echo <<<HERE
			<div class="headFormDiv1">
				<form id="{$tableName}HeadForm" class="headForm" method="post">
					<div class="headFormDiv2">
		HERE;
		$sId='';
		$target=rtrim($tableName, 's');
		$sName=$target.'Id';
		$selected=null;
		if (isset($inheritPosts[$target.'Id']))
		{
			$selected=$inheritPosts[$target.'Id'];
		}
		if ($target == 'layer')
		{
			echo "<select class=\"headSelect\" id=\"layerCategories\" name=\"layerCategory\" onchange='updateSelect(\"layerSelect\", window[this.value]);'></select><br>";
			$sId='id="layerSelect"';
		}
		else
		{
			if (isset($inheritPosts['layerCategory']))
			{
				echo '<input type="hidden" name="layerCategory" value="'.$inheritPosts['layerCategory'].'">';
			}
			if ($target == 'map' || $target == 'group')
			{
				if ($target == 'group')
				{
					if (isset($inheritPosts['mapId']) || isset($inheritPosts['groupId']))
					{
						$sName='groupIds';
					}
					if (isset($inheritPosts['groupIds']))
					{
						$groupIdsArray=explode(',', $inheritPosts['groupIds']);
						if (!isset($inheritPosts['mapId']) && isset($groupIdsArray[0]))
						{
							$selected=$groupIdsArray[0];
						}
					}
				}
			}
		}
		$optionValues=array_merge(array(""),array_column(current($table), pkColumnOfTable($tableName)));
		if ($target == 'contact' || $target == 'origin')
		{
			$optionLabels=array_merge(array(""),array_column(current($table), 'name'));
			$optionValues=array_combine($optionValues, $optionLabels);
		}
		echo "<select $sId onchange='this.form.submit();' class='headSelect' name='$sName'>";
		printSelectOptions($optionValues, $selected);
		echo <<<HERE
						</select>
						<button type="submit" class="headButton" name="{$target}Button" value="get">Hämta</button>
					</div>
				</form><br>
				<form class="headForm" method="post">
					<div class="headFormDiv3">
						<input class="headInput" type="text" name="{$target}IdNew">
		HERE;
		printHiddenInputs($inheritPosts);
		echo <<<HERE
						<button type="submit" class="headButton" name="{$target}Button" value="create">Skapa</button>
					</div>
				</form>
			</div>
		HERE;
	}
	
?>
