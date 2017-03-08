
jQuery(document).ready(function( $ ) {

  alertify.logPosition("bottom left");

  // console.log(BEAM.loading_gif);

  $('li.activity-item').append('<div class="activity-loading-edit"><div class="loading_gif" style="background:url(\'' + BEAM.loading_gif + '\') no-repeat center center">&nbsp;</div></div>');

  $('.mpp-media-holder .mpp-item-content:has(.me-cannotplay)').css({'height':'280px', 'max-height':'280px', 'max-width': '200px', 'width': '200px'});
  $('.mpp-media-holder .mpp-item-content:has(.me-cannotplay) .wp-video-shortcode').css({'width': '', 'height': ''});
  $('.mpp-media-holder .mpp-item-content .me-cannotplay').css({'width': '', 'height': ''});

  // $('.beam_delete_media_btn').click(function() {
  //
  //   var $aid = $(this).data('mpp-activity-id');
  //   var $mid = $(this).data('mpp-media-id');
  //   var $clicked = $(this);
  //
  //   alertify
  //     .okBtn("Confirm")
  //     .cancelBtn("Cancel")
  //     .confirm("Estás seguro que quieres borrar esto archivo?", function() {
  //
  //       var $elem = $('<input type="hidden" name="removedMedia" value="' + $mid + '" />');
  //       $elem.insertAfter('#frm_buddypress-edit-activity-mp input[name="activity_id"]');
  //       $clicked.parent().addClass('removed');
  //
  //       alertify.log("The media will be deleted once you save your edition.");
  //     }, function() {
  //       //cancel
  //       alertify.log("Operation cancelled.");
  //     });
  //
  // });


  // $('a.buddyboss_edit_activity').click(function() {
  //
  //   var $clicked = $(this);
  //
  //   setTimeout(function() {
  //     var $aid = $clicked.data('activity_id');
  //     $('.mpp-media-list a.beam_delete_media_btn[data-mpp-activity-id="' + $aid + '"]').show();
  //   }, 5000);
  //
  // });

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
	} else {
		B_E_A_.current_activity_org = $link.closest('.activity-content').find('.activity-inner').html();
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

        $link.removeClass('loading').addClass('action-save').html(B_E_A_.button_text.save);

        //add cancel button before link
        var $cancel_button = jQuery("<a href='#'>").addClass('bp-secondary-action buddyboss_edit_activity_cancel').html(B_E_A_.button_text.cancel);
        $cancel_button.attr('data-activity_id', data['activity_id'] );
        $link.before($cancel_button);
        $cancel_button.attr('onclick', 'return buddypress_edit_activity_mp_cancel(this);');
        $cancel_button.attr('data-target_type', 'activity');

        //editing activity
        var $activity_content = $link.closest('.activity-content').find('.activity-inner');
        var $mpp_container = $link.closest('.activity-content').find('.mpp-container');
        $activity_content.hide().after($form_wrapper);
        $form.find('.mpp-upload-shortcode').before($mpp_container);
        $cancel_button.addClass('button');

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

        // jQuery('select#mpp-shortcode-upload-gallery-id').val($gid);
        // jQuery('select#mpp-shortcode-upload-gallery-id').remove();
        // jQuery('select#mpp-shortcode-upload-gallery-id').remove();
        // jQuery('#mpp-upload-dropzone-shortcode').append(jQuery('<input type="hidden" id="mpp-shortcode-upload-gallery-id" name="mpp-shortcode-upload-gallery-id" value="' + $gid + '" />'));
        // jQuery('#mpp-upload-dropzone-shortcode').append(jQuery('<input type="hidden" id="mpp-shortcode-upload-gallery-id" name="mpp-shortcode-upload-gallery-id" value="' + $gid + '" />'));
        // <input type="hidden" name="mpp-uploading-media-type" class="mpp-uploading-media-type" value="video">

        // $form.find('textarea').val($text_content);
        $form.find('textarea').val(response.content);

        $link.closest('li.activity-item').removeClass('loading-edit');

        jQuery('.beam_delete_media_btn').unbind('click');
        jQuery('.beam_delete_media_btn').click(function() {

          var $ = jQuery;

          var $aid = $(this).data('mpp-activity-id');
          var $mid = $(this).data('mpp-media-id');
          var $clicked = $(this);

          alertify
            .okBtn("Confirmar")
            .cancelBtn("Cancelar")
            .confirm("Estás seguro que quieres borrar esto archivo?", function() {

              var $elem = $('<input type="hidden" name="removedMedia" value="' + $mid + '" />');
              $elem.insertAfter('#frm_buddypress-edit-activity-mp input[name="activity_id"]');
              $clicked.parent().addClass('removed');

              alertify.log("El archivo se eliminará una vez que haya guardado su edición.");
            }, function() {
              //cancel
              alertify.log("Operación cancelada.");
            });

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

	jQuery('.buddyboss_edit_activity_cancel').remove();
  $link.removeClass('loading').removeClass('action-save').html(B_E_A_.button_text.edit);

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
		'content': $form.find('textarea').val()
	};

  //prepare to use again
  $form.find('input[name="removedMedia"]').remove();

  //empty?
  if( (!data['content']) || /^\s*$/.test(data['content']) ) {
    alertify.log("The activity content cannot be empty.");
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

	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: data,
		success: function(response) {
			response = jQuery.parseJSON(response);
      // console.log(response);
			if (response.status) {
				$link.removeClass('loading').removeClass('action-save').html(B_E_A_.button_text.edit);

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
              var $updated_inner = $content_updates.find('.activity-inner');
              var $updated_mp = $content_updates.find('.mpp-container');

              // console.warn($updated_mp);

              var $activity_content = $link.closest('.activity-content').find('.activity-inner');
              var $mpp_container = $link.closest('.activity-content').find('.mpp-container');

              // console.log("updated inner " + $updated_inner.html());
              $activity_content.html($updated_inner.html());

              var $reload = false;

              // $mpp_container.remove();
              $mpp_container.html('');
              $updated_mp.each(function(index) {
                $mpp_container.append( jQuery(this).html() );

                if( jQuery(this).has(':has(.mpp-audio-content)').length ) {
                  $reload = true
                }
              });

              if ($reload) {
                window.location.reload();
                return;
              }

              $activity_content.after($mpp_container);

              //button delete media
              var $aid = $link.data('activity_id');
              jQuery('.mpp-media-list a.beam_delete_media_btn').hide();

              // $activity_content.after($mpp_container);
              $link.closest('.activity-content').append($link.closest('.activity-content .activity-meta'));
              $activity_content.show();

              jQuery('.mpp-media-holder .mpp-item-content:has(.me-cannotplay)').css({'height':'280px', 'max-height':'280px', 'max-width': '200px', 'width': '200px'});
              jQuery('.mpp-media-holder .mpp-item-content:has(.me-cannotplay) .wp-video-shortcode').css({'width': '', 'height': ''});
              jQuery('.mpp-media-holder .mpp-item-content .me-cannotplay').css({'width': '', 'height': ''});

              $link.closest('li.activity-item').removeClass('loading-edit');
            }

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

	$save_button.removeClass('action-save').html(B_E_A_.button_text.edit);

  var $activity_content = $cancel_button.closest('.activity-content').find('.activity-inner');
  var $mpp_container = $form.find('.mpp-container');
  $activity_content.after($mpp_container);

  //button delete media
  var $aid = $cancel_button.data('activity_id');
  jQuery('.mpp-media-list a.beam_delete_media_btn[data-mpp-activity-id="' + $aid + '"]').hide();
  jQuery('.mpp-media-holder.removed').removeClass('removed');
  $form.find('input[name="removedMedia"]').remove();

	$form_wrapper.hide();
	jQuery('body').append($form_wrapper);
	$cancel_button.remove();

	return false;
}
