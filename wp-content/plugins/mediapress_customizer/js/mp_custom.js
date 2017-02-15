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

    $('select#whats-new-post-in').change( function() {
      if($(this).val() == '0') {
        $group_options.hide();
        $user_options.show();
        $('select#mpp_gallery_assignment').val($($user_options[0]).attr('value'));
      } else {
        $group_options.show();
        $user_options.hide();
        $('select#mpp_gallery_assignment').val($($group_options[0]).attr('value'));
      }
    });

  });



});
