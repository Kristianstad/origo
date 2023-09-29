<?php

	require_once("./functions/pkColumnOfTable.php");
	require_once("./functions/manage/printSelectOptions.php");
	require_once("./functions/manage/printHiddenInputs.php");

	function printHeadForm($tableConfig, $inheritPosts)
	{
		require("./constants/keywordCategorized.php");
		$tableName=key($tableConfig);
		echo <<<HERE
			<div class="headFormDiv1">
				<form id="{$tableName}HeadForm" class="headForm" method="post">
					<div class="headFormDiv2">
		HERE;
		$sId='';
		$type=rtrim($tableName, 's');
		$sName=$type.'Id';
		$selected=null;
		if (isset($inheritPosts[$type.'Id']))
		{
			$selected=$inheritPosts[$type.'Id'];
		}
		if (in_array($tableName, $keywordCategorized))
		{
			echo "<select class=\"headSelect\" id=\"{$tableName}Categories\" name=\"{$tableName}Category\" onchange='updateSelect(\"{$type}Select\", window[\"{$tableName}\"+this.value]);'></select><br>";
			$sId='id="'.$type.'Select"';
		}
		else
		{
			if (isset($inheritPosts['layersCategory']))
			{
				echo '<input type="hidden" name="layersCategory" value="'.$inheritPosts['layersCategory'].'">';
			}
			if ($type == 'map' || $type == 'group')
			{
				if ($type == 'group')
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
		$optionValues=array_merge(array(""),array_column(current($tableConfig), pkColumnOfTable($tableName)));
		if ($type == 'contact' || $type == 'origin')
		{
			$optionLabels=array_merge(array(""),array_column(current($tableConfig), 'name'));
			$optionValues=array_combine($optionValues, $optionLabels);
		}
		echo "<select $sId onchange='this.form.submit();' class='headSelect' name='$sName'>";
		printSelectOptions($optionValues, $selected);
		echo <<<HERE
						</select>
						<button type="submit" class="headButton" name="{$type}Button" value="get">Hämta</button>
					</div>
				</form><br>
				<form class="headForm" method="post">
					<div class="headFormDiv3">
						<input class="headInput" type="text" name="{$type}IdNew">
		HERE;
		printHiddenInputs($inheritPosts);
		echo <<<HERE
						<button type="submit" class="headButton" name="{$type}Button" value="create">Skapa</button>
					</div>
				</form>
			</div>
		HERE;
	}

?>
