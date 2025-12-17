function closeTopFrame() {
    window.parent.postMessage({}, window.location.origin);
}
