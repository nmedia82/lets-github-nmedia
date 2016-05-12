jQuery(function($){

	$('#nm_opw-tabs').tabs();
	
	//submitting form foreach setting/tabs
	$(".nm-admin-form").submit(function(event){
		event.preventDefault();
		
		$(".nm-saving-settings").html('<img src="'+nm_opw_vars.doing+'" />');
		var form_data = $(this).serialize();
		//console.log(form_data);
		$.post(ajaxurl, form_data, function(resp){
			
			//console.log(resp);
			$(".nm-saving-settings").html(resp);
		});
	});
	
	
	 /* =========== wpColorPicker =============== */
    $('.wp-color-field').wpColorPicker();
    /* =========== wpColorPicker =============== */
    
    
    /* =========== Chosen plugin =============== */
    var chosen_options = {	no_results_text: "Sorry, try other option!",
    						width: "100%"};
    $(".the_chosen").chosen(chosen_options);
    $(".shortcode_ids").chosen(chosen_options);
    /* =========== Chosen plugin =============== */
    
    
    /* =========== media upload =============== */
    $(".nm-media-upload").on('click', function(e){
    	//e.preventDefault();
    	
    	var send_attachment_bkp = wp.media.editor.send.attachment;
    	var the_parent = $(this).closest('td');
    	
    	//tb_show('Upload a Image', 'media-upload.php?referer=media_page&type=image&TB_iframe=true&post_id=0', false);
    	wp.media.editor.send.attachment = function(props, attachment)
		{
			var fileurl = attachment.url;
			
			if(fileurl){
	        	var image_box 	 = '<br><img width="75" src="'+fileurl+'">';
	        	
	        	the_parent.find('input:text').val(fileurl);
	        	the_parent.find('.the-thumb').html(image_box);
	        	//console.log(fileurl);
			}
			
			wp.media.editor.send.attachment = send_attachment_bkp;
		}
		
		wp.media.editor.open();
		return false;
    });
    
    $(".remove-media").on('click', function(e){
    	var the_parent = $(this).closest('td');
    	
    	the_parent.find('input:text').val('');
    	the_parent.find('.the-thumb').html('');
    });
    /* =========== media upload =============== */
	
	
});

