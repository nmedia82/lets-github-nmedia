/*
 * NOTE: all actions are prefixed by plugin shortnam_action_name
 */

jQuery(function(){
	

});



function get_option(key){
	
	/*
	 * TODO: change plugin shortname
	 */
	var keyprefix = 'nm_opw';
	
	key = keyprefix + key;
	
	var req_option = '';
	
	jQuery.each(nm_opw_vars.settings, function(k, option){
		
		//console.log(k);
		
		if (k == key)
			req_option = option;		
	});
	
	//console.log(req_option);
	return req_option;
	
}