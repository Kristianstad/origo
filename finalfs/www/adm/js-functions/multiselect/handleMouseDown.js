/**
* Handles mouse down events and prevents default behavior if shift key is pressed.
* @param {MouseEvent} event - The mouse down event object.
* @returns {boolean} - Returns false if shift key is pressed, true otherwise.
*/
function handleMouseDown(event) {
	if (!(event instanceof MouseEvent)) {
		console.warn('Invalid event object provided to handleMouseDown');
		return true;
	}
	if (event.shiftKey) {
		console.log('Shift-click detected and prevented.');
		event.preventDefault();
		return false;
	}
	return true;
}
