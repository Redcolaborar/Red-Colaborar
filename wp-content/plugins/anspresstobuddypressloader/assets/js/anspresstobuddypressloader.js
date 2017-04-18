jQuery(document).ready(function(){
	jQuery.ajax({
		method: "POST",
		url: APTOBPLOADER_AJAX_URL+'?action=anspresstobuddypressloader_load_questions',
		data: { aptobploader_term_id:APTOBPLOADER_TERM_ID },
		dataType: 'html'
	})
	.done(function( data ) {
		if(data != 0)
		{
			//show only the link after the AJAX successfully loaded
			jQuery('#item-nav ul.responsive-tabs:last-child').append('<li><a href="#aptobploader" id="aptobploader_preguntas" title="Haga clic para alternar preguntas">'+APTOBPLOADER_TEXT_QUESTIONS+'</a></li>');

			var activityStream = jQuery('#activity-stream').html();

			var html = '<div id="aptobploader">'+data+'</div>';

			//control inside events
			jQuery('#aptobploader_preguntas').toggle(
				function() 
				{				
					jQuery('#activity-stream').find('li').remove();
					jQuery('#activity-stream').html(html);	
				},
				function()
				{
					jQuery('#activity-stream').find('#aptobploader').remove();
					jQuery('#activity-stream').html(activityStream);
				}
			);				
		}
	});
});