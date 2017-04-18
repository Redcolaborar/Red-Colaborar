jQuery(document).ready(function () {
   load_more_galleries();
   jQuery(".whats-new-post-in-red-choosen-data").chosen();
   if (jQuery("#gallery_title_text_id")[0]){
        jQuery('#whats-new-post-in-box').show();
   }
});


function getSelectedGallery(id_data)
{
    var selected_gal = id_data.value ;
    if(selected_gal == 'create_new')
    {
        var text_title = '<div class="gallery_title_text"><div class="new_gallery_label">Enter Gallery Title</div><div class="gallery_title_input"><input type="text" id="gallery_title_text_id" name="gallery_title_text" placeholder="Enter New Gallery Title"></div></div>';
        jQuery('.assign_gallery_container').after(text_title);
        jQuery('#whats-new-post-in-box').show();
        //jQuery('#whats-new-post-in-custom-added').remove();
        jQuery('.whats-new-post-in-custom-added').remove();

    }
    else
    {
        getGoupIdByGalleryIdJS(selected_gal);
        jQuery('.gallery_title_text').remove();
        // jQuery('#whats-new-post-in-box').hide();               
    }

}

function makeEnterVideoLinkCallback()
{

    //jQuery("#mpp-uploaded-media-list-activity ul div.mpp-uploaded-media-item").remove();
    //jQuery("#mpp-upload-dropzone-activity").remove();

    var get_status_data = jQuery(".get_status_data").html();
    var get_tags_dropdown_data = jQuery("#post_tags_for_media").val();
    if (! jQuery(".red_video_field_wrapper")[0]){
            var html_content = '' ;
            html_content += '<div class="red_video_field_wrapper">'+
                    '<div class="add_more_button_container">'+
                        '<a href="javascript:void(0);" class="red_add__more_button" title="Add field">Add More</a>'+
                    '</div>'+
                '<div class="red_enter_video_link_container">'+
                    '<div class="add_video_link_container">'+
                        '<textarea name="add_video_link[]" placeholder="Add Video Embedded Code"></textarea>'+
                    '</div>'+
                    '<div class="add_video_link_container">'+
                        '<select name="mpp-media-status-video[]">'+get_status_data+'</select>'+
                    '</div>'+
                    '<div class="add_video_title_container">'+
                        '<input type="text" name="mpp-media-title-video[]" placeholder="Enter File Title"/>'+
                    '</div>'+
                    '<div class="tags_for_mediapress_customizer">'+
//                        '<select class="mpp-media-associated-tags-class" data-placeholder="Choose a Tags..." multiple style="width:350px;" tabindex="4" name="mpp-media-tags-embedded[]">'+get_tags_dropdown_data+'</select>'+

                    '<input id="add_tags_with_media_0" type="text" placeholder="Choose a Tags..." name="mpp-media-tags-embedded[]" class="demo-default input-tags-for-media" value='+get_tags_dropdown_data+'>'+
                    '</div>'+
                '</div>'+
            '</div>';
            jQuery('#whats-new-options').after(html_content) ;
    }

    var maxField = 10;
    var addButton = jQuery('.red_add__more_button');
    var wrapper = jQuery('.red_video_field_wrapper');
    var x = 1;



    jQuery(addButton).click(function(){
        if(x < maxField){ //Check maximum number of input fields
            x++; //Increment field counter
            var id_for_tags = 'add_tags_with_media_'+x ;
            var fieldHTML = '<div class="red_enter_video_link_container">'+
                           '<a href="javascript:void(0);" class="remove_button" title="Remove field">'+
                               'Remove'+
                           '</a>'+
                       '<div class="add_video_link_container">'+
                       '<textarea name="add_video_link[]" placeholder="Add Video Embedded Code"></textarea>'+
                       '</div>'+
                       '<div class="add_video_link_container">'+
                           '<select name="mpp-media-status-video[]">'+get_status_data+'</select>'+
                       '</div>'+
                       '<div class="add_video_title_container">'+
                           '<input type="text" name="mpp-media-title-video[]" placeholder="Enter File Title"/>'+
                       '</div>'+
                       '<div class="tags_for_mediapress_customizer">'+
//                         '<select class="mpp-media-associated-tags-class" data-placeholder="Choose a Tags..." multiple style="width:350px;" tabindex="4" name="mpp-media-tags-embedded[]">'+get_tags_dropdown_data+'</select>'+
                       '<input type="text" id='+id_for_tags+' placeholder="Choose a Tags..."  name="mpp-media-tags-embedded[]" class="demo-default input-tags-for-media" value='+get_tags_dropdown_data+'>'+
                   '</div>'+
                   '</div>';
            jQuery(wrapper).append(fieldHTML); // Add field html

            jQuery("#add_tags_with_media_"+x).selectize({
                persist: false,
                createOnBlur: true,
                create: true,
                removeOption : true,
                plugins: ['remove_button'],
                items: null,
            });
        }
    });
    jQuery(wrapper).on('click', '.remove_button', function(e){ //Once remove button is clicked
        e.preventDefault();
        jQuery(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });
    //jQuery(".mpp-media-associated-tags-class").chosen();
    jQuery('#add_tags_with_media_0').selectize({
        persist: false,
        createOnBlur: true,
        create: true,
        removeOption : true,
        plugins: ['remove_button'],
        items: null,
    });
    // Chosen touch support.

}
