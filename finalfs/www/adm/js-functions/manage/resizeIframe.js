function resizeIframe(iframe) {
	if (!iframe || !iframe.contentWindow || !iframe.contentWindow.document.body) {
        return;
    }
    iframe.style.height = '1px';
    requestAnimationFrame(function() {
        const newHeight = iframe.contentWindow.document.body.parentElement.scrollHeight+1;
        iframe.style.height = newHeight + 'px';
    });
}
