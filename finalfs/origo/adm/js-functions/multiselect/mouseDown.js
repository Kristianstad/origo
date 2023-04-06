function mouseDown(e)
{
	var shiftPressed=0;
	var evt = e?e:window.event;
	if (parseInt(navigator.appVersion)>3)
	{
		if (document.layers && navigator.appName=="Netscape")
		{
			shiftPressed=(evt.modifiers-0>3);
		}
		else
		{
			shiftPressed=evt.shiftKey;
		}
		if (shiftPressed)
		{
			/*alert ('Shift-click is disabled.')*/
			return false;
		}
	}
	return true;
}
