function preservePageScroll() 
{
	/* Save scroll position at the time of form submission */
	document.querySelectorAll('form').forEach(form => {
		form.addEventListener('submit', () => {
			sessionStorage.setItem('scrollPosition', window.scrollY);
		});
	});

	/* Restore scroll position after page load */
	window.addEventListener('load', () => {
		const scrollPosition = sessionStorage.getItem('scrollPosition');
		if (scrollPosition) {
			window.scrollTo(0, parseInt(scrollPosition));
			sessionStorage.removeItem('scrollPosition');
		}
	});
}
