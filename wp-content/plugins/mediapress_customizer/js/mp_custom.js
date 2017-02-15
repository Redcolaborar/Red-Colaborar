jQuery(document).ready(function($) {

  var $clicked_before = false;

  $('#mpp-activity-upload-buttons a').click(function() {
    // console.log('Show');
    $('#latest_gallery_data_container').show();
    $clicked_before = true;
  });

  $('body').on('gallery_list_visible', function() {

    if( !$clicked_before )
      $('#latest_gallery_data_container').hide();
    else
      $('#latest_gallery_data_container').show();

    if( !($('select#mpp_gallery_assignment').length) )
      return;

    if( $('input[name="red_group_id"]').length ) {
      return;
    }

    var $group_options = $('select#mpp_gallery_assignment option[data-gallerytype="group"]');
    var $user_options = $('select#mpp_gallery_assignment option[data-gallerytype="user"]');
    // console.warn(MP);
    // console.log($group_options);
    $group_options.hide();
    $('select#mpp_gallery_assignment').val($($user_options[0]).attr('value'));
    if( MP.is_group == '1' ) {
      $group_options.show();
      $user_options.hide();
      $('select#mpp_gallery_assignment').val($($group_options[0]).attr('value'));
    }

    if($(this).val() == '0') {
      $group_options.hide();
      $user_options.show();

      if( $user_options.length ) {
        $('select#mpp_gallery_assignment').val($($user_options[0]).attr('value'));
      } else {
        $('select#mpp_gallery_assignment').val( 'create_new' );
        $('select#mpp_gallery_assignment').trigger('change');
      }

    } else {

      var $this_group_options = $('select#mpp_gallery_assignment option[data-gallerytype="group"][data-gallery_group_id="' + $(this).val() + '"]');

      $group_options.hide();
      $this_group_options.show();
      $user_options.hide();

      if( $this_group_options.length ) {
        $('select#mpp_gallery_assignment').val($($this_group_options[0]).attr('value'));
      } else {
        $('select#mpp_gallery_assignment').val( 'create_new' );
        $('select#mpp_gallery_assignment').trigger('change');
      }


      // jQuery('.gallery_title_text').remove();
    }

    $('select#whats-new-post-in').change( function() {

      jQuery('.gallery_title_text').remove();

      if($(this).val() == '0') {
        $group_options.hide();
        $user_options.show();

        if( $user_options.length ) {
          $('select#mpp_gallery_assignment').val($($user_options[0]).attr('value'));
        } else {
          $('select#mpp_gallery_assignment').val( 'create_new' );
          $('select#mpp_gallery_assignment').trigger('change');
        }

      } else {

        var $this_group_options = $('select#mpp_gallery_assignment option[data-gallerytype="group"][data-gallery_group_id="' + $(this).val() + '"]');

        $group_options.hide();
        $this_group_options.show();
        $user_options.hide();

        if( $this_group_options.length ) {
          $('select#mpp_gallery_assignment').val($($this_group_options[0]).attr('value'));
        } else {
          $('select#mpp_gallery_assignment').val( 'create_new' );
          $('select#mpp_gallery_assignment').trigger('change');
        }


        // jQuery('.gallery_title_text').remove();
      }
    });

  });



});
