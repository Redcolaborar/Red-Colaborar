
jQuery( document ).ready( function () {
    jQuery( "#accordion" ).accordion( {
        heightStyle: "content",
        collapsible: true,
        change: function ( event, ui ) {
            jQuery( "#accordion" ).show()[0].scrollIntoView( true );
        }
    } );
    jQuery( ".ui-accordion-header" ).click( function () { console.log( "hey" ); jQuery( 'html,body' ).animate( { scrollTop: jQuery( ".ui-accordion-header" ).offset().top }, 0 ); } );
//    jQuery( '#accordion' ).bind( 'accordionactivate', function ( event, ui ) {
//        /* In here, ui.newHeader = the newly active header as a jQ object
//         ui.newContent = the newly active content area */
////jQuery('.ui-state-active').scrollTo(500);
//        var result = jQuery( ".ui-accordion-header-active" ).length;
////console.log(result);
//        if ( result ) {
//            //console.log(jQuery('.ui-accordion-header-active').offset().top);
//            var top = jQuery( '.ui-accordion-header-active' ).offset().top;
//            top -= 100;
//           // console.log( top );
//            jQuery( 'html, body' ).animate( {
//                scrollTop: top
//            }, 2000 );
//        }
//    } );
    jQuery('[name="category[]"]').select2({
        placeholder: "Select Category"
    });
    jQuery('[name="tag[]"]').select2({
        placeholder: "Select Tags"
    });
     jQuery('#wdm_add_tag').click(function(){
            var tag = jQuery('#wdm_tag').val();
            console.log(tag);
            if(tag == ""){
                // alert('Please Enter Tag');
                alert(wdm_data.wdm_empty_tag);
            }else{
                jQuery.ajax({
         type : "post",
         url : wdm_data.ajax_url,
         dataType:'JSON',
         data : {
            action: 'wdm_tag_add',
            tag: tag
        },
         success: function(response) {
        
            jQuery.each(response,function(i,val){
               switch(i){
                  case "error":
                     alert(val);
                     break;
                  case "success":
                      var pos = val.indexOf("$");
                      var tag_id = val.substring(0, pos);
                      var tag = val.substring((pos+1));
                    jQuery('[name="tag[]"]').append('<option value="'+tag_id+'" selected>'+tag+'</option>');
                    // alert('Tag added successfully');
                    alert(wdm_data.wdm_tag_added);
                    jQuery('#wdm_tag').val('');
                    jQuery('[name="tag[]"]').select2({
                    placeholder: "Select Tags"
                    });
                     break;
               }
               
               
            });
         
            
         }
           
        });
            }   
        
});

} );
