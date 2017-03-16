jQuery(document).ready(function(){
	jQuery.ajax({
		method: "POST",
		url: APTOBPLOADER_AJAX_URL+'?action=anspresstobuddypressloader_load_questions',
		data: { aptobploader_term_id:APTOBPLOADER_TERM_ID },
		dataType: 'html'
	})
	.done(function( data ) {
		jQuery('#activity-stream').prepend(data);
	});
});