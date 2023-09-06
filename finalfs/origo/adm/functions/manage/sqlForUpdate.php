<?php

	require_once("./functions/manage/isFullTarget.php");
	require_once("./functions/manage/targetTable.php");
	require_once("./functions/manage/targetId.php");
	require_once("./functions/manage/updatedFullTarget.php");
	require_once("./functions/manage/appendUpdatedColumnsToSql.php");
	require_once("./functions/manage/targetConfig.php");
	require_once("./functions/manage/targetIdColumn.php");

	function sqlForUpdate($fullTarget, $updatePosts)
	{
		require("./constants/configSchema.php");
		if (isFullTarget($fullTarget))
		{
			$targetTable=targetTable($fullTarget);
			$targetId=targetId($fullTarget);
			$fullTarget=updatedFullTarget($fullTarget, $updatePosts);
			$sql="UPDATE $configSchema.$targetTable SET";
			$sql=appendUpdatedColumnsToSql(targetConfig($fullTarget), $sql);
			$targetIdColumn=targetIdColumn($fullTarget);
			$sql=$sql." WHERE $targetIdColumn = '".$targetId."'";
			return $sql;
		}
		else
		{
			die("sqlForUpdate($fullTarget, $updatePosts) failed!");
		}
	}

?>
