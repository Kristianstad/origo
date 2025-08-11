<?php

	// Uses manage functions: isFullTarget, targetTable, targetId, updatedFullTarget, appendUpdatedColumnsToSql, targetConfig, targetIdColumn

	// Takes a full target-array and an updatePosts-array and returns a sql-query string that updates
	// the database configuration (table) for the given target with values from the updatePosts
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
