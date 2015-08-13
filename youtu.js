jQuery(document).ready(function(){
	var checkall = jQuery('#allid');
	var option = jQuery('input[name="img_ids[]"]');
	checkall.click(function(){
		if(jQuery(this).attr('checked') == 'checked') {
			option.attr('checked', 'checked');
		} else {
			option.removeAttr('checked');
		}
	});
	function is_check(){
		var check = true;
		for(var i=0; i<option.length; i++){
			if(option.eq(i).attr('checked') != 'checked')
				check = false;
		}
		return check;
	}
	option.click(function(){
		if(is_check()) {
			checkall.attr('checked', 'checked');
		} else {
			checkall.removeAttr('checked');
		}
	});
	jQuery('tbody tr:odd').css('background-color', '#f6f6f6');
});