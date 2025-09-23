function selectOptionsByValues(selectId, optionValues) {
    const select = document.getElementById(selectId);
    if (!select || !select.multiple) return false;
    
    const values = optionValues.split(',').map(val => val.trim());
    Array.from(select.options).forEach(option => {
        option.selected = values.includes(option.value);
    });
    return true;
}
