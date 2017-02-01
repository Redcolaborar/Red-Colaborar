jQuery(document).ready(function(){
    jQuery( "#wdm_start_date" ).datepicker({
        changeMonth: true,
      changeYear: true
    });
    jQuery( "#wdm_end_date" ).datepicker({
        changeMonth: true,
      changeYear: true
    }); 
    
    jQuery('#wdm_submit').click(function(){
       var start_date = new Date(jQuery('#wdm_start_date').val());
       var end_date = new Date(jQuery('#wdm_end_date').val());
      // console.log(start_date+'   '+end_date);
       if(start_date > end_date){
            alert('Start date cannot be greater than end date');
            return false;    
       }
       
    });
});