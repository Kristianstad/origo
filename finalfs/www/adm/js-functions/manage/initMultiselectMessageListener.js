function initMultiselectMessageListener() {
    function handleMessage(event) {
        if (event.origin !== window.location.origin) {
            console.warn('Ignored postMessage from untrusted origin:', event.origin);
            return;
        }
        if (typeof event.data !== 'object' || event.data === null) {
            console.warn('Ignored invalid postMessage data format');
            return;
        }
        const { targetId, value } = event.data;
        if (typeof targetId !== 'string' || targetId === '') {
            console.warn('Ignored postMessage missing valid targetId');
            return;
        }
        if (typeof value !== 'string') {
            console.warn('Ignored postMessage missing valid value');
            return;
        }
        const textarea = document.getElementById(targetId);
        if (textarea) {
            textarea.value = value;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
			const multiselectButton = document.getElementById(targetId + ":multiselect");
			let multiselectButtonValue = multiselectButton.getAttribute('value');
			multiselectButtonValue = multiselectButtonValue.replace(/^([^:]*::[^:]*).*$/, '$1:' + value);
			multiselectButton.setAttribute('value', multiselectButtonValue)
			toggleTopFrame('');
        } else {
            console.warn('Target textarea not found:', targetId);
        }
    }
    window.addEventListener('message', handleMessage);
}
