function update(menu) {
    let selection = document.querySelector("#selection");
    let sortedValues = $(menu).attr('data-sorted-values') ? $(menu).attr('data-sorted-values').split(',') : [];
    let currentValues = $(menu).val() || []; // Get selected values or empty array
    let last_action = '';

    // console.log('update called', 'menu:', $(menu).prop('id'), 'val:', currentValues, 'initial sorted-values:', sortedValues);

    // Nothing selected
    if (currentValues.length === 0) {
        // console.log('Nothing selected case');
        sortedValues = []; // Clear array
        last_action = 'nothing selected';
        $(menu).find('option').removeAttr('selected'); // Clear all selected attributes
    } else {
        // Get the text of currently selected options
        let selectedTexts = $(menu)
            .find('option')
            .filter(':selected')
            .map(function () {
                return $(this).text();
            })
            .get();

        // console.log('Selected values:', currentValues, 'Selected texts:', selectedTexts);

        // Determine added and removed items
        let added = selectedTexts.filter(text => !sortedValues.includes(text));
        let removed = sortedValues.filter(text => !selectedTexts.includes(text));

        // Update last_action
        if (added.length > 0) {
            last_action = 'added: ' + added.join(', ');
        } else if (removed.length > 0) {
            last_action = 'removed: ' + removed.join(', ');
        }

        // Remove deselected items from sortedValues
        sortedValues = sortedValues.filter(text => selectedTexts.includes(text));

        // Append new selections to the end of sortedValues
        sortedValues = sortedValues.concat(added);

        // Ensure the selected attributes are in sync
        $(menu).find('option').each(function () {
            $(this).prop('selected', currentValues.includes($(this).val()));
        });
    }

    // Update the data attribute and UI
    let sortedValuesString = sortedValues.join(',');
    $(menu).attr('data-sorted-values', sortedValuesString);
    $('#selection').html(sortedValuesString);
    selection.value = sortedValuesString;

    // console.log('Final sorted-values:', sortedValuesString, 'Selection:', selection.value, 'last_action:', last_action);
}
