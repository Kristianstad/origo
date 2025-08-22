<?php

	function printNewsSubjects($username, $userNews)
	{
		if (!empty($userNews))
		{
			echo '<table>';
			foreach ($userNews as $aNews)
			{
				if (!in_array($username, $aNews['deletes']))
				{
					echo '<tr><td><li><a href="?action=load&newId='.urlencode($aNews['new_id']).'&return=text">';
					if (!in_array($username, $aNews['reads']))
					{
						echo '<b>';
						printNews($username, $aNews, array('abstract'));
						echo '</b>';
					}
					else
					{
						printNews($username, $aNews, array('abstract'));
					}
					echo '</a></li></td><td><a href="?action=delete&newId='.urlencode($aNews['new_id']).'"><img src="./images/list_remove.png" alt="Radera" title="Radera"></a></td></tr>';
				}
			}
			echo '</table>';
		}
		else
		{
			echo 'Det finns inga nyheter.';
		}
	}

?>
