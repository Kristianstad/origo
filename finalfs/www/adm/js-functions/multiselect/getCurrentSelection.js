function getCurrentSelection() {
    const textarea = document.getElementById('selection');
    return textarea ? textarea.value.trim() : '';
}
