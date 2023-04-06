<?php

	function printViewSwitcher($view)
	{
		require("./constants/views.php");
		if (empty($view))
		{
			$view=key($views);
		}
		echo "<form>";
		foreach ($views as $k => $v)
		{
			echo "<input type='radio' name='view' value='$k' onchange='this.form.submit();'";
			if ($k == $view)
			{
				echo " checked='checked'";
			}
			echo ">$k";
		}
		echo "</form>";
	}
	
?>
