jQuery(document).ready(function(){
  jQuery("select[name=sfwd-courses_course_price_type]").change(function(){
		var price_type = 	jQuery("select[name=sfwd-courses_course_price_type]").val();
		if(price_type == "open" || price_type == "free" || price_type == "closed") {
			jQuery("input[name=sfwd-courses_course_price]").val('');
			jQuery("#sfwd-courses_course_price").hide();
		}
		else
			jQuery("#sfwd-courses_course_price").show();

		if(price_type == "closed") 
			jQuery("#sfwd-courses_custom_button_url").show();
		else
			jQuery("#sfwd-courses_custom_button_url").hide();
			
		if(price_type == "subscribe") {
			jQuery("#sfwd-courses_course_price_billing_cycle").show();
			/*jQuery("#sfwd-courses_course_no_of_cycles").show();
			jQuery("#sfwd-courses_course_remove_access_on_subscription_end").show();*/
		}
		else {
			jQuery("#sfwd-courses_course_price_billing_cycle").hide();
			/*jQuery("#sfwd-courses_course_no_of_cycles").hide();
			jQuery("#sfwd-courses_course_remove_access_on_subscription_end").hide(); */
		}
	});
        jQuery("select[name=sfwd-courses_course_price_type]").change();
	jQuery("input[name=sfwd-courses_expire_access]").change( function() {
		if(jQuery("input[name=sfwd-courses_expire_access]:checked").val() == undefined) {
			jQuery("#sfwd-courses_expire_access_days").hide();
			jQuery("#sfwd-courses_expire_access_delete_progress").hide();
		}
		else
		{
			jQuery("#sfwd-courses_expire_access_days").show();
			jQuery("#sfwd-courses_expire_access_delete_progress").show();	
		}
	} );
	jQuery("input[name=sfwd-courses_expire_access]").change();
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