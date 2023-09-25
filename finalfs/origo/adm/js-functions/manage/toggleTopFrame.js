function toggleTopFrame(type)
{
	var x = document.getElementById("topFrame");
	if (x.style.display === "none")
	{
		x.style.display = "block";
		window.scrollTo(0, 0);
	}
	else if (topFrame === type)
	{
		x.style.display = "none";
	}
	else
	{
		window.scrollTo(0, 0);
	}
	topFrame = type;
}
