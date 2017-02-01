 jQuery(document).ready(function(){
   jQuery("[name='sfwd-lessons_lesson_assignment_upload']").change(function(){
		checked = jQuery("[name=sfwd-lessons_lesson_assignment_upload]:checked").length;
		if(checked) {
			jQuery("#sfwd-lessons_auto_approve_assignment").show();
      jQuery("#sfwd-lessons_lesson_assignment_points_enabled").show();
      //
		}
		else {
			jQuery("#sfwd-lessons_auto_approve_assignment").hide();
      jQuery("#sfwd-lessons_lesson_assignment_points_enabled").hide();
		}
	});

   jQuery("[name='sfwd-lessons_lesson_assignment_points_enabled']").change(function(){
    assgn_points_checked = jQuery("[name='sfwd-lessons_lesson_assignment_points_enabled']:checked").length;
    if (assgn_points_checked) {
      jQuery('#sfwd-lessons_lesson_assignment_points_amount').show();
    } else {
      jQuery('#sfwd-lessons_lesson_assignment_points_amount').hide();
    }
   });

   if (jQuery("[name='sfwd-lessons_lesson_assignment_points_enabled']:checked").length > 0) {
    jQuery('#sfwd-lessons_lesson_assignment_points_amount').show();
   };

	if(jQuery("[name='sfwd-lessons_lesson_assignment_upload']"))
	jQuery("[name='sfwd-lessons_lesson_assignment_upload']").change();
    load_datepicker();	
    
    function load_datepicker(){
        jQuery( "input[name='sfwd-lessons_visible_after_specific_date']" ).datepicker({
			changeMonth: true,
			changeYear: true,
            dateFormat : 'MM d, yy',
            onSelect: function(dateText, inst) {
                 jQuery("input[name='sfwd-lessons_visible_after']").val('0');
             jQuery("input[name='sfwd-lessons_visible_after']").prop('disabled', true);
             }
		});       
        
        jQuery("input[name='sfwd-lessons_visible_after_specific_date']").blur(function() {
            var specific_data = jQuery("input[name='sfwd-lessons_visible_after_specific_date']").val();
            if( specific_data != '') {
            jQuery("input[name='sfwd-lessons_visible_after']").val('0');
           jQuery("input[name='sfwd-lessons_visible_after']").attr("disabled", "disabled");
           }else {
             jQuery("input[name='sfwd-lessons_visible_after']").removeAttr("disabled");
           }
        });
        jQuery("input[name='sfwd-lessons_visible_after']").click(function() {
             var specific_data = jQuery("input[name='sfwd-lessons_visible_after_specific_date']").val();
            if( specific_data != '') {
            jQuery("input[name='sfwd-lessons_visible_after']").val('0');
           jQuery("input[name='sfwd-lessons_visible_after']").attr("disabled", "disabled");
           }else {
             jQuery("input[name='sfwd-lessons_visible_after']").removeAttr("disabled");
           }
            });
        
    }
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