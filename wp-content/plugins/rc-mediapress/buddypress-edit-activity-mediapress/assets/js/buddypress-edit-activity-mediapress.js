
function mpp_setup_uploader_file_types( mpp_uploader, type ) {

	if( !_mppData || !_mppData.types ) {
		return ;
	}

	if ( type === undefined  && _mppData.current_type !== undefined ) {
		type = _mppData.current_type;
	}
	//if type is still not defined, go back
	if ( type == undefined ) {
		return ;
	}

	//console.log(mpp_uploader);
	var settings = mpp_uploader.uploader.getOption('filters');

	settings.mime_types = [_mppData.types[type]];

	mpp_uploader.uploader.setOption('filters', settings );

	if( mpp_uploader.dropzone ) {
		jQuery( mpp_uploader.dropzone ).find('.mpp-uploader-allowed-file-type-info' ).html( _mppData.allowed_type_messages[type] );
	}
}

jQuery(document).ready(function( $ ) {

  // alertify.logPosition("bottom left");

  // console.log(BEAM.loading_gif);

  // console.log(_mppData);

  $('li.activity-item').append('<div class="activity-loading-edit"><div class="loading_gif" style="background:url(\'' + BEAM.loading_gif + '\') no-repeat center center">&nbsp;</div></div>');

  $('.mpp-media-holder .mpp-item-content:has(.me-cannotplay)').css({'height':'280px', 'max-height':'280px', 'max-width': '200px', 'width': '200px'});
  $('.mpp-media-holder .mpp-item-content:has(.me-cannotplay) .wp-video-shortcode').css({'width': '', 'height': ''});
  $('.mpp-media-holder .mpp-item-content .me-cannotplay').css({'width': '', 'height': ''});

  $('.beam_delete_media_btn').click(function() {

    var $aid = $(this).data('mpp-activity-id');
    var $mid = $(this).data('mpp-media-id');
    var $clicked = $(this);

    alertify
      .okBtn("Confirm")
      .cancelBtn("Cancel")
      .confirm("Estás seguro que quieres borrar esto archivo?", function() {

        var $elem = $('<input type="hidden" name="removedMedia" value="' + $mid + '" />');
        $elem.insertAfter('#frm_buddypress-edit-activity-mp input[name="activity_id"]');
        $clicked.parent().addClass('removed');

        alertify.log("The media will be deleted once you save your edition.");
      }, function() {
        //cancel
        alertify.log("Operation cancelled.");
      });

  });


  $('a.buddyboss_edit_activity').click(function() {

    var $clicked = $(this);

    setTimeout(function() {
      var $aid = $clicked.data('activity_id');
      $('.mpp-media-list a.beam_delete_media_btn[data-mpp-activity-id="' + $aid + '"]').show();
    }, 25 );

  });

});

function buddypress_edit_activity_mp_initiate(link) {
	if (jQuery(link).hasClass('action-save')) {
		buddypress_edit_activity_mp_save(link);
	} else {
		buddypress_edit_activity_mp_get(link);
	}
	return false;
}

function buddypress_edit_activity_mp_get(link) {
  jQuery('.buddyboss_edit_activity_cancel').trigger('click');

	$link = jQuery(link);
	$form = jQuery('#frm_buddypress-edit-activity-mp');
	$form_wrapper = $form.parent();

	$link.addClass('loading');

  $link.closest('li.activity-item').addClass('loading-edit');

	if ($link.hasClass('buddyboss_edit_activity_comment')) {
		B_E_A_.current_activity_org = $link.closest('[id^=acomment]').find(' > .acomment-content').html();

		//hide activity category if the item is a comment
		$form.find('.rc-edit-activity-category').hide();
	} else {
		B_E_A_.current_activity_org = $link.closest('.activity-content').find('.activity-inner').html();

		//show activity category if the item is NOT a comment
		$form.find('.rc-edit-activity-category').show();
	}

	var data = {
		'action': $form.find('input[name="action_get"]').val(),
		'buddypress_edit_activity_nonce': $form.find('input[name="buddypress_edit_activity_nonce"]').val(),
		'activity_id': $link.data('activity_id'),
	};

  jQuery('html, body').animate({
      scrollTop: $link.closest('.activity-content').find('.activity-header').offset().top - 150
  }, 300);

  jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: data,
		success: function(response) {
			response = jQuery.parseJSON(response);
			if (response.status) {

        $link.removeClass( 'loading' ).addClass( 'action-save' ).html( B_E_A_.button_text.save );
				$('.edit-icon[data-activity_id=' + $link.data('activity_id') + ']').hide();
				$('.save-icon[data-activity_id=' + $link.data('activity_id') + ']').show();

        //add cancel button before link
				// var $cancel_button_container = jQuery("<div class='bp-activity-menu-item'>"); //added
        // var $cancel_button = jQuery("<a href='#'>").addClass('bp-secondary-action buddyboss_edit_activity_cancel').html(B_E_A_.button_text.cancel);
        // $cancel_button.attr('data-activity_id', data['activity_id'] );
        //
				// $cancel_button_container.prepend( $cancel_button ); //added

        // $link.before($cancel_button);
				// $link.parent().before($cancel_button_container); //added

        // $cancel_button.attr('onclick', 'return buddypress_edit_activity_mp_cancel(this);');
        // $cancel_button.attr('data-target_type', 'activity');

        //editing activity
        var $activity_content = $link.closest('.activity-content').find('.activity-inner');
        var $mpp_container = $link.closest('.activity-content').find('.mpp-container');
        $activity_content.find('.rc-activity-col-content').hide().after($form_wrapper);

        $form.find('.mpp-upload-shortcode').before($mpp_container);
        // $cancel_button.addClass('button');

				$('.mediapress-edit-cancel[data-activity_id=' + $link.data('activity_id') + ']').show();

        // var $text_content = jQuery.trim( $activity_content.text() );

        //button delete media
        var $aid = $link.data('activity_id');
        jQuery('.mpp-media-list a.beam_delete_media_btn[data-mpp-activity-id="' + $aid + '"]').show();

        $form_wrapper.show();

        jQuery('html, body').animate({
            scrollTop: $link.closest('.activity-content').find('.activity-header').offset().top - 150
        }, 300);

        $form.find('input[name="activity_id"]').val(data.activity_id);
        var $gid = $form.find('.mpp-container .mpp-media-holder').first().data('mpp-gallery-id');

        var $type = "";
        var $photo = $form.find('.mpp-container').hasClass("rc-photo");
        var $doc = $form.find('.mpp-container').hasClass("rc-doc");
        var $video = $form.find('.mpp-container').hasClass("rc-video");
        var $audio = $form.find('.mpp-container').hasClass("rc-audio");

        if( $photo ) $type = "photo";
        if( $doc ) $type = "doc";
        if( $video ) $type = "video";
        if( $audio ) $type = "audio";

        //It is getting the activity uploader too, needs to be filteres to only shortcode

        jQuery('select#mpp-shortcode-upload-gallery-id').val($gid);
        jQuery('select#mpp-shortcode-upload-gallery-id').remove();
        jQuery('input#mpp-uploading-media-type').remove();

        jQuery('#mpp-upload-feedback-shortcode > #mpp-shortcode-upload-gallery-id').remove();
        jQuery('#mpp-upload-feedback-shortcode > input[name="mpp-uploading-media-type"]').remove();

        jQuery('#mpp-upload-feedback-shortcode').append(jQuery('<input type="hidden" id="mpp-shortcode-upload-gallery-id" name="mpp-shortcode-upload-gallery-id" value="' + $gid + '" />'));
        jQuery('#mpp-upload-feedback-shortcode').append(jQuery('<input type="hidden" name="mpp-uploading-media-type" class="mpp-uploading-media-type" value="' + $type + '">'));
        //

        // mpp_setup_uploader_file_types( mpp.shortcode_uploader, $type );

        // $form.find('textarea').val($text_content);
        $form.find('textarea').val(response.content);

				jQuery('#wds_redcolaborar_bp_display_the_activity_dropdown-mediapress').val(response.category);

        $link.closest('li.activity-item').removeClass('loading-edit');
				$link.closest('li.activity-item').removeClass('loading');

        jQuery('.beam_delete_media_btn').unbind('click');
        jQuery('.beam_delete_media_btn').click(function() {

          var $ = jQuery;

          var $aid = $(this).data('mpp-activity-id');
          var $mid = $(this).data('mpp-media-id');
          var $clicked = $(this);

					var $elem = $('<input type="hidden" name="removedMedia" value="' + $mid + '" />');
          $elem.insertAfter('#frm_buddypress-edit-activity-mp input[name="activity_id"]');
          $clicked.parent().addClass('removed');

          // alertify
          //   .okBtn("Confirmar")
          //   .cancelBtn("Cancelar")
          //   .confirm("Estás seguro que quieres borrar esto archivo?", function() {
					//
          //     var $elem = $('<input type="hidden" name="removedMedia" value="' + $mid + '" />');
          //     $elem.insertAfter('#frm_buddypress-edit-activity-mp input[name="activity_id"]');
          //     $clicked.parent().addClass('removed');
					//
          //     alertify.log("El archivo se eliminará una vez que haya guardado su edición.");
          //   }, function() {
          //     //cancel
          //     alertify.log("Operación cancelada.");
          //   });

        });

			}
		},
	});



}

function buddypress_edit_activity_mp_save(link) {
	$link = jQuery(link);
	$form = jQuery('#frm_buddypress-edit-activity-mp');
	$form_wrapper = $form.parent();

	$link.addClass('loading');

  $link.closest('li.activity-item').addClass('loading-edit');

	// jQuery('.buddyboss_edit_activity_cancel').remove();
  $link.removeClass('loading').removeClass('action-save').html(B_E_A_.button_text.edit);
	$('.edit-icon[data-activity_id=' + $link.data('activity_id') + ']').show();
	$('.save-icon[data-activity_id=' + $link.data('activity_id') + ']').hide();

  var $removedMedias = $form.find('input[name="removedMedia"]').toArray();

  var $rmo = [];
  for(var i = 0; i < $removedMedias.length; i++ ) {
    $rmo.push( jQuery( $removedMedias[i] ).attr('value') );
  }

	var data = {
		'action': $form.find('input[name="action_save"]').val(),
		'buddypress_edit_activity_nonce': $form.find('input[name="buddypress_edit_activity_nonce"]').val(),
		'activity_id': $link.data('activity_id'),
    'media_to_delete' : $rmo,
		'content': $form.find('textarea').val(),
		'category': jQuery('#wds_redcolaborar_bp_display_the_activity_dropdown-mediapress').val(),
	};

	$('#activity-' + $link.data('activity_id') + ' .bp-activity-menu-trigger').click();

  //prepare to use again
  $form.find('input[name="removedMedia"]').remove();

  //empty?
  if( (!data['content']) || /^\s*$/.test(data['content']) ) {
    alert("The activity content cannot be empty.");
    return;
  }

  var $updated = [];
  jQuery('.mpp-upload-shortcode .mpp-uploaded-media-item').each(function(index) {
    $updated.push(jQuery(this).data('mediaId').toString());
  });

  if( $updated.length ) {
    data['mpp-attached-media'] = $updated.join();
  }


  // console.log(data);

  jQuery('.mpp-upload-shortcode .mpp-uploaded-media-item').remove();
  jQuery('.mpp-upload-shortcode #mpp-loader-wrapper').hide();

	// console.log( "post " );

	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: data,
		success: function(response) {
			response = jQuery.parseJSON(response);
      // console.log(response);
			if (response.status) {
				$link.removeClass('loading').removeClass('action-save').html(B_E_A_.button_text.edit);
				$('.edit-icon[data-activity_id=' + $link.data('activity_id') + ']').show();
				$('.save-icon[data-activity_id=' + $link.data('activity_id') + ']').hide();

				if ($link.hasClass('buddyboss_edit_activity_comment')) {
					//editing comment
					$link.closest('[id^=acomment]').find(' > .acomment-content').html(response.content).show();
				} else {
					//editing activity
					// $link.closest('.activity-content').find('.activity-inner').html(response.content).show();

          if(response.activity_html) {

            if( $link.closest('.activity-content').has(':has(.mpp-audio-content)').length ) {
              window.location.reload();
            } else {

              $form_wrapper.hide();
              var $content_updates = jQuery(response.activity_html);
              var $updated_inner = $content_updates.find('.activity-inner  .rc-activity-col-content');
              var $updated_mp = $content_updates.find('.mpp-container');

              // console.warn($updated_mp);

              var $activity_content = $link.closest('.activity-content').find('.activity-inner .rc-activity-col-content');
              var $mpp_container = $link.closest('.activity-content').find('.mpp-container');

              // console.log("updated inner " + $updated_inner.html());
              $activity_content.html($updated_inner.html());

              var $reload = false;

              $mpp_container.remove();
              $mpp_container.html('');
              $updated_mp.each(function(index) {
                $mpp_container.append( jQuery(this).html() );

                if( jQuery(this).has(':has(.mpp-audio-content)').length ) {
                  $reload = true
                }
              });

              // if ($reload) {
              //   window.location.reload();
              //   return;
              // }

							window.location.reload();
							return;

              // $activity_content.after($mpp_container);

              //button delete media
              var $aid = $link.data('activity_id');
              jQuery('.mpp-media-list a.beam_delete_media_btn').hide();

              // $activity_content.after($mpp_container);
              $link.closest('.activity-content .rc-activity-col-content').append($link.closest('.activity-content .activity-meta'));
              $activity_content.show();

              jQuery('.mpp-media-holder .mpp-item-content:has(.me-cannotplay)').css({'height':'280px', 'max-height':'280px', 'max-width': '200px', 'width': '200px'});
              jQuery('.mpp-media-holder .mpp-item-content:has(.me-cannotplay) .wp-video-shortcode').css({'width': '', 'height': ''});
              jQuery('.mpp-media-holder .mpp-item-content .me-cannotplay').css({'width': '', 'height': ''});

							// console.log( $activity_content );

							$link.removeClass('loading');

						  $link.closest('li.activity-item').removeClass('loading-edit');

							$('.mediapress-edit-cancel[data-activity_id=' + $aid + ']').hide();
            }

          } else {
						window.location.reload();
						return;
					}

				}

			}
			if( response.media_deleted ) {
				jQuery('.mpp-media-holder.removed').fadeOut();
			}
		},
	});
}

function buddypress_edit_activity_mp_cancel(cancel_button) {
	var $cancel_button = jQuery(cancel_button);
	var $form = jQuery('#frm_buddypress-edit-activity-mp');
	var $form_wrapper = $form.parent();
	var $save_button = '';

	//editing activity
	$cancel_button.closest('.activity-content').find('.activity-inner').html(B_E_A_.current_activity_org).show();
	$save_button = $cancel_button.closest('.activity-content').find('.buddyboss_edit_activity.action-save');

	var $aid = $cancel_button.data('activity_id');

	$save_button.removeClass('action-save').html(B_E_A_.button_text.edit);
	$('.edit-icon[data-activity_id=' + $aid + ']').show();
	$('.save-icon[data-activity_id=' + $aid + ']').hide();

  var $activity_content = $cancel_button.closest('.activity-content').find('.activity-inner');
  var $mpp_container = $form.find('.mpp-container');
  $activity_content.after($mpp_container);

  //button delete media

  jQuery('.mpp-media-list a.beam_delete_media_btn[data-mpp-activity-id="' + $aid + '"]').hide();
  jQuery('.mpp-media-holder.removed').removeClass('removed');
  $form.find('input[name="removedMedia"]').remove();
	// $mpp_container.remove();

	$form_wrapper.hide();
	jQuery('body').append($form_wrapper);
	// $cancel_button.remove();
	$('.mediapress-edit-cancel[data-activity_id=' + $aid + ']').hide();

	return false;
}
