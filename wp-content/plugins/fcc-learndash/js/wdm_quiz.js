jQuery(document).ready(function(){
 
//		var quiz_pro = jQuery("select[name=sfwd-quiz_quiz_pro]").val();
//		window['sfwd-quiz_quiz_pro'] = sfwd_data.quiz_pro;
//		jQuery("form#post").append("<div id='disable_advance_quiz_save'><input type='hidden' name='disable_advance_quiz_save' value='0'/></div>");
//		jQuery("select[name=sfwd-quiz_quiz_pro]").change();

		jQuery("select[name=sfwd-quiz_course]").change(function() {
                   // console.log(wdm_topic_data.advanced_quiz_preview_link);
				if(window['sfwd_quiz_lesson'] == undefined)
				window['sfwd_quiz_lesson'] = wdm_topic_data.wdm_selected_lesson_topic_id;
				console.log(window);
				jQuery("select[name=sfwd-quiz_lesson]").html('<option>Loading...</option>');

				var data = {
					'action': 'wdm_select_a_lesson_or_topic',
					'course_id': jQuery(this).val()
				};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data, function(json) {
					window['response'] = json;
					html  = '<option value="0">'+wdm_quiz_script_object.lesson_or_topic_string+'</option>';
					jQuery.each(json, function(key, value) {
						if(key != '' && key != '0')
						{
							selected = (key == window['sfwd_quiz_lesson'])? 'selected=selected': '';
							html += "<option value='" + key + "' "+ selected +">" + value + "</option>";				
						}
					});
					jQuery("select[name=sfwd-quiz_lesson]").html(html);
					//jQuery("select[name=sfwd-topic_lesson]").val(window['sfwd_topic_lesson']);
				}, "json");
		});
                jQuery("select[name=sfwd-quiz_course]").change();
		jQuery("#postimagediv").addClass("hidden_by_sfwd_lms_sfwd_module.js");
		jQuery("#postimagediv").hide(); //Hide the Featured Image Metabox  
                jQuery('#wdm_quiz_form').submit(function(){
                   jQuery('[name="prerequisiteList[]"] option').each(function(){
                                   jQuery(this).attr('selected','selected') ;
                                }); 
                });
    
    
});

function toggleVisibility(id) {
	var e = document.getElementById(id);
	if (e.style.display == 'block')
		e.style.display = 'none';
	else
		e.style.display = 'block';
}