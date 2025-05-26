function update(menu) {
    if (!menu || !$(menu).length || !$('#selection').length) {
        console.error('Menu or selection element not found');
        return;
    }

    const $menu = $(menu);
    const $selection = $('#selection');
    
    // Initialize data attribute if undefined
    if (!$menu.attr('data-sorted-values')) {
        $menu.attr('data-sorted-values', '');
    }

    // Handle empty selection
    if (!$menu.val()?.length) {
        $menu.find('option').prop('selected', false);
        $menu.attr('data-sorted-values', '');
    } else {
        let values = $menu.attr('data-sorted-values').split(',').filter(Boolean);
        
        $menu.find('option').each(function() {
            const opt = $(this).text();
            const isSelected = $menu.val().includes(opt);
            
            if (isSelected) {
                $(this).prop('selected', true);
                if (!values.includes(opt)) {
                    values.push(opt);
                }
            } else {
                $(this).prop('selected', false);
                values = values.filter(v => v !== opt);
            }
        });
        
        $menu.attr('data-sorted-values', values.join(','));
    }

    const sortedValues = $menu.attr('data-sorted-values');
    $selection.val(sortedValues).html(sortedValues);
}
