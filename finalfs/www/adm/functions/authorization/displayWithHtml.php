<?php

function displayWithHtml($content)
{
	displayHtmlHeader();
	echo $content;
	displayHtmlFooter();
	
	fastcgi_finish_request();
	exit(0);
}
