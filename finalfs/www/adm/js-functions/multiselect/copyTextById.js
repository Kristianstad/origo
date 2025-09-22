function copyTextById(id)
{
	/* Get the text field */
	var copyText = document.getElementById(id);

	/* Select the text field */
	copyText.select();
	copyText.setSelectionRange(0, 99999); /* For mobile devices */

	/* Copy the text inside the text field
	NOTE! Does only work for https */
	navigator.clipboard.writeText(copyText.value);
}
