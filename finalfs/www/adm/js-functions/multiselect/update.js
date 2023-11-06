function update(menu)
{
	let selection = document.querySelector("#selection");
	var last_action = '';
	var qty = 0;

	// nothing selected
	if ($(menu).val() == null) 
	{
		$.each($(menu).find('option'), function(i) {
			qty = 0;
			last_action = "nothing selected";
			$(this).removeAttr('selected');
			$(menu).attr('data-sorted-values', '');
		});
	} 
	// at least 1 item selected
	else 
	{
		$.each($(menu).find('option'), function(i) {
			var vals = $(menu).val().join(' ');
			var opt = $(this).text();
			qty = $(menu).val().length;
			if (vals.indexOf(opt) > -1) 
			{
				// most recent selection
				if ($(this).attr('selected') != 'selected') 
				{
					last_action = "added: " + opt;
					$(menu).attr('data-sorted-values', $(menu).attr('data-sorted-values') + $(this).text() + ' ');
					$(this).attr('selected', 'selected');
				}
			} 
			else 
			{
				// most recent deletion
				if ($(this).attr('selected') == 'selected') 
				{
					last_action = "removed: " + opt;
					var string = $(menu).attr('data-sorted-values').replace(new RegExp(opt + ',', 'g'), '');
					$(menu).attr('data-sorted-values', string);
					$(this).removeAttr('selected');
				}
			}
		});
	}
	$(menu).attr('data-sorted-values', $(menu).attr('data-sorted-values').replace(' ', ','));
	$('#selection').html($(menu).attr('data-sorted-values').slice(0, -1));
	selection.value=$(menu).attr('data-sorted-values').slice(0, -1);
}
