jQuery(document).ready(function(){
	// console.log(wdm_topic_object.select_lesson_text);
    jQuery("[name='sfwd-topic_lesson_assignment_upload']").change(function(){
		checked = jQuery("[name=sfwd-topic_lesson_assignment_upload]:checked").length;
		if(checked) {
			jQuery("#sfwd-topic_auto_approve_assignment").show();
			jQuery('#sfwd-topic_lesson_assignment_points_enabled').show();
		}
		else {
			jQuery("#sfwd-topic_auto_approve_assignment").hide();
			jQuery('#sfwd-topic_lesson_assignment_points_enabled').hide();
		}
	});
	if(jQuery("[name='sfwd-topic_lesson_assignment_upload']"))
	jQuery("[name='sfwd-topic_lesson_assignment_upload']").change();


	jQuery("[name='sfwd-topic_assignment_points_enabled']").change(function(){
    assgn_points_checked = jQuery("[name='sfwd-topic_assignment_points_enabled']:checked").length;
    if (assgn_points_checked) {
      jQuery('#sfwd-topic_assignment_points_amount').show();
    } else {
      jQuery('#sfwd-topic_assignment_points_amount').hide();
    }
   });

   if (jQuery("[name='sfwd-topic_assignment_points_enabled']:checked").length > 0) {
    jQuery('#sfwd-topic_assignment_points_amount').show();
   };


		jQuery("select[name=sfwd-topic_course]").change(function() {
				if(window['sfwd_topic_lesson'] == undefined)
				window['sfwd_topic_lesson'] = jQuery("select[name=sfwd-topic_lesson]").val();

				jQuery("select[name=sfwd-topic_lesson]").html('<option>Loading...</option>');

				var data = {
					'action': 'wdm_select_a_lesson',
					'course_id': jQuery(this).val()
				};

				$course_id = jQuery(this).val();

				// console.log(wdm_topic_data.admin_url);
				// console.log(data);
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(wdm_topic_data.admin_url, data, function(json) {
					window['response'] = json;
					html  = '<option value="0">'+wdm_topic_object.select_lesson_text+'</option>';
					jQuery.each(json, function(key, value) {
						if(key != '' && key != '0')
						{
							selected = (key == window['sfwd_topic_lesson'])? 'selected=selected': '';
							html += "<option value='" + key + "' "+ selected +">" + value + "</option>";				
						}
					});
					jQuery("select[name=sfwd-topic_lesson]").html(html);
					//jQuery("select[name=sfwd-topic_lesson]").val(window['sfwd_topic_lesson']);
				}, "json");
		});
                jQuery("select[name=sfwd-topic_course]").change();
                jQuery('*').on('hover',function(){
           // console.log('asdasd');
        jQuery('.media-menu a:nth-child(5)').remove();

        });
    
});

function toggleVisibility(id) {
	var e = document.getElementById(id);
	if (e.style.display == 'block')
		e.style.display = 'none';
	else
		e.style.display = 'block';
}