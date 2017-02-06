jQuery(document).ready(function($) {
  $('.assign_gallery_container').hide();
  $('#mpp-activity-upload-buttons a').click(function() {
    $('.assign_gallery_container').show();
  });

  var $group_options = $('select#mpp_gallery_assignment option[data-gallerytype="group"]');
  var $user_options = $('select#mpp_gallery_assignment option[data-gallerytype="user"]');
  $group_options.hide();
  $('select#mpp_gallery_assignment').val($($user_options[0]).attr('value'));
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
