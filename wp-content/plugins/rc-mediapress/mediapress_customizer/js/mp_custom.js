jQuery(document).ready(function($) {

  // var $clicked_before = false;

  $('#mpp-activity-upload-buttons a').click(function() {
    // console.log('Show');
    $('#latest_gallery_data_container').show();
    // $clicked_before = true;

    // $('#whats-new-form').attr('data-upload_type', $(this).data('media-type') );

    var $group_id = $('#whats-new-form').attr('data-group_id');
    $group_id = ( $group_id == undefined ) ? '0' : $group_id;

    //default to my profile if in main activity
    // if( $('select#whats-new-post-in').length ) {
    //   $('#whats-new-form').attr('data-group_id', $('select#whats-new-post-in').val() );
    $('#whats-new-form').attr('data-group_id', '0' );
    // }

    if( !( $('select#mpp_gallery_assignment').length ) ) return;

    // console.log( $group_id );

    $('select#mpp_gallery_assignment option[value!="create_new"]').hide();
    $('select#mpp_gallery_assignment option[data-gallery_group_id="' + $group_id + '"]').show();

    // var $option_hidden = $('select#mpp_gallery_assignment option[style*="none"]');
    var $option_all = $('select#mpp_gallery_assignment option');

    jQuery('.gallery_title_text').remove();

    if( $option_all.not('[style*="none"]').length ) {
      var $first = $( $option_all.not('[style*="none"]')[0] );
      $('select#mpp_gallery_assignment').val( $first.attr('value') );
      if( $first.attr('value') == "create_new" ) {
        $('select#mpp_gallery_assignment').trigger('change');
      }
    }

  });

  $('#whats-new-form').on('group_chosen', function() {

    //if in a group activity
    if( $('input[name="whats-new-post-in"]').length && !$('select#whats-new-post-in').length ) {
      $('#whats-new-form').attr('data-group_id', $('input[name="whats-new-post-in"]').val() );
    }

    // if( !$clicked_before )
    //   $('#latest_gallery_data_container').hide();
    // else {
    //   $('#latest_gallery_data_container').show();

      // check form attribute if the icon was clicked before
      if( !($('select#mpp_gallery_assignment').length) )
        return;

      // var $media_type = $('#whats-new-form').data('upload_type');

      $('select#mpp_gallery_assignment option[value!="create_new"]').hide();
      $('select#mpp_gallery_assignment option').show();

      // var $option_hidden = $('select#mpp_gallery_assignment option[style*="none"]');
      var $option_all = $('select#mpp_gallery_assignment option');

      if( $option_all.not('[style*="none"]').length ) {
        var $first = $( $option_all.not('[style*="none"]')[0] );
        $('select#mpp_gallery_assignment').val( $first.attr('value') );
      }

    // } // clicked before else

    if( !($('select#mpp_gallery_assignment').length) )
      return;

    // Disabled because of angular forms
    // if( $('input[name="whats-new-post-in"]').length ) {
    //   return;
    // }

    // var $media_type = $('#whats-new-form').attr('data-upload_type');
    // var $options_not_media_type = $('select#mpp_gallery_assignment option[data-gallery_media_type!="' + $media_type + '"]').not('[value="create_new"]');

    var $group_options = $('select#mpp_gallery_assignment option[data-gallerytype="group"]');
    var $user_options = $('select#mpp_gallery_assignment option[data-gallerytype="user"]');
    // var $group_options_media = $('select#mpp_gallery_assignment option[data-gallerytype="group"][data-gallery_media_type="' + $media_type + '"]');
    // var $user_options_media = $('select#mpp_gallery_assignment option[data-gallerytype="user"][data-gallery_media_type="' + $media_type + '"]');

    // if( !$('select#whats-new-post-in').length && $('body.activity.my-activity') ) {
    //
    //   //show only user galleries
    //   $group_options.remove();
    //   $user_options.show();
    //
    //   // $options_not_media_type.hide();
    //
    //   return;
    // }

    // console.warn(MP);
    // console.log($group_options);
    $group_options.hide();
    $('select#mpp_gallery_assignment').val($($user_options[0]).attr('value'));
    if( MP.is_group == '1' ) {
      $group_options.show();
      $user_options.hide();

      // $options_not_media_type.hide();
      if( $group_options.length ) {
        $('select#mpp_gallery_assignment').val($($group_options[0]).attr('value'));
      } else {
        $('select#mpp_gallery_assignment').val( 'create_new' );
        $('select#mpp_gallery_assignment').trigger('change');
      }
    }

    // console.log($(this));

    // if( !$('select#whats-new-post-in').length ) {
    //
    //   //show only user galleries
    //   $group_options.hide();
    //   $user_options.show();
    //
    //   // $options_not_media_type.hide();
    //
    //   return;
    // }

    var $sel_post_in = $('#whats-new-post-in');

    if($sel_post_in.val() == '0') {
      $group_options.hide();
      $user_options.show();

      // $options_not_media_type.hide();

      if( $user_options.length ) {
        $('select#mpp_gallery_assignment').val($($user_options[0]).attr('value'));
      } else {
        $('select#mpp_gallery_assignment').val( 'create_new' );
        $('select#mpp_gallery_assignment').trigger('change');
      }

    } else {

      var $this_group_options = $('select#mpp_gallery_assignment option[data-gallery_group_id="' + $sel_post_in.val() + '"]');

      $group_options.hide();
      $this_group_options.show();
      $user_options.hide();

      // $options_not_media_type.hide();

      if( $this_group_options.length ) {
        $('select#mpp_gallery_assignment').val($($this_group_options[0]).attr('value'));
      } else {
        $('select#mpp_gallery_assignment').val( 'create_new' );
        $('select#mpp_gallery_assignment').trigger('change');
      }

      // jQuery('.gallery_title_text').remove();
    }

    $('#whats-new-post-in').change( function() {

      // $media_type = $('#whats-new-form').attr('data-upload_type');
      // $options_not_media_type = $('select#mpp_gallery_assignment option[data-gallery_media_type!="' + $media_type + '"]').not('[value="create_new"]');

      // $group_options_media = $('select#mpp_gallery_assignment option[data-gallerytype="group"][data-gallery_media_type="' + $media_type + '"]');
      // $user_options_media = $('select#mpp_gallery_assignment option[data-gallerytype="user"][data-gallery_media_type="' + $media_type + '"]');
      $group_options_media = $('select#mpp_gallery_assignment option[data-gallerytype="group"]');
      $user_options_media = $('select#mpp_gallery_assignment option[data-gallerytype="user"]');


      jQuery('.gallery_title_text').remove();

      $('#whats-new-form').attr('data-group_id', $(this).val() );

      // console.log($('#whats-new-form'));

      if($(this).val() == '0') {
        $group_options.hide();
        $user_options.show();

        // console.warn( $user_options_media );

        // $options_not_media_type.hide();

        if( $user_options.length ) {
          $('select#mpp_gallery_assignment').val($($user_options[0]).attr('value'));
        } else {
          $('select#mpp_gallery_assignment').val( 'create_new' );
          // $('select#mpp_gallery_assignment').trigger('change');
        }

      } else {

        var $this_group_options = $('select#mpp_gallery_assignment option[data-gallery_group_id="' + $(this).val() + '"]');

        // console.log( $this_group_options );

        $group_options.hide();
        $this_group_options.show();
        $user_options.hide();

        // $options_not_media_type.hide();

        if( $this_group_options.length ) {
          $('select#mpp_gallery_assignment').val($($this_group_options[0]).attr('value'));
        } else {
          $('select#mpp_gallery_assignment').val( 'create_new' );
          // $('select#mpp_gallery_assignment').trigger('change');
        }

        // jQuery('.gallery_title_text').remove();
      }
    });

  });

});
