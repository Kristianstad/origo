<?php

	function defineFileConstant($key, $value)
	{
		require("./constants/webRoot.php");
		file_put_contents("$webRoot/php/constants/$key.php", "<?php\ndefine('$key', ".var_export($value, true).');');
		define($key, $value);
	}

	function includeFileConstant($key)
	{
		include "/origo/php/constants/$key.php";
	}

?>
