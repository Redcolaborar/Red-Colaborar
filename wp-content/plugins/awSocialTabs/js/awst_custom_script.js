/* forntend javascript */
jQuery( document ).ready(function() {

    /* function to handle like button click events */
    jQuery('body').on('click', '.awst_like_btn > i', function(){
        var post_id   = jQuery(this).attr("data-post-id");
        var post_like = jQuery(this).attr("data-post-like");
        var objectType = jQuery(this).attr("data-object-type");

        var site_url = jQuery("meta[key='awst_site_url']").attr('value');
        var ajaxUrl  = site_url+"/wp-admin/admin-ajax.php";

        jQuery.ajax({
            type:"POST",
            url: ajaxUrl,
            data: {action:'awst_ajax_like',post_id:post_id, post_like:post_like, object_type: objectType},
            success:function(data){
                response = JSON.parse(data);

                if(response.status == 'success'){

                    $message = response.status;
                    $likcount = response.likecount;

                    if( jQuery('#awst_like_btn_'+post_id+' > i').hasClass('fa-thumbs-o-up')){
                        jQuery('#awst_like_btn_'+post_id+' > i').removeClass('fa-thumbs-o-up').addClass('fa-thumbs-up');
                    }else{
                        jQuery('#awst_like_btn_'+post_id+' > i').removeClass('fa-thumbs-up').addClass('fa-thumbs-o-up');
                    }
                    jQuery('.total_likes_'+post_id).text(response.likecount);
                }else{
                    /* error code block */
                    $message = response.message;
                    jQuery('#awMessageBlock').text($message).fadeIn(2000).fadeOut(2000);
                }
            }
        });
    });

    /* function to handle rating button click events */
    jQuery('.awst_rate_btn > i').on('click', function(){

        var post_id  = jQuery(this).attr("data-post-id");
        var rate_val = jQuery(this).attr("data-rate-id");

        var site_url = jQuery("meta[key='awst_site_url']").attr('value');
        var ajaxUrl  = site_url+"/wp-admin/admin-ajax.php";

        jQuery.ajax({
            type:"POST",
            url: ajaxUrl,
            data: {action:'awst_ajax_rating', post_id: post_id, rate_val: rate_val },
            success:function(data){

                response = JSON.parse(data);
                if(response.status == 'success'){

                    jQuery('.awst_rate_btn > i').removeClass('fa-star').addClass('fa-star-o');
                    for (i = 1; i <= rate_val; i++) {
                        var itemID = "#star"+i+" > i";
                        jQuery(itemID).removeClass('fa-star-o').addClass('fa-star');
                    }
                    var rating = response.rating;
                    jQuery('#average_rating').text(rating);
                }else{
                    $message = response.message;
                    jQuery('#awMessageBlock').text($message).fadeIn(2000).fadeOut(2000);
                }
            }
        });
    });

    /* function to handle review button click events */
    jQuery('.awst_rate_btn_review').on('click', function(){

        var post_id  = jQuery(this).attr("data-post-id");
        var review  = jQuery('#review_'+post_id).val();

        var site_url = jQuery("meta[key='awst_site_url']").attr('value');
        var ajaxUrl  = site_url+"/wp-admin/admin-ajax.php";

        jQuery.ajax({
            type:"POST",
            url: ajaxUrl,
            data: {action:'awst_ajax_review', post_id: post_id, review:review },
            success:function(data){
                response = JSON.parse(data);
                console.log(data);

                /*var message = '';
                message += '<li>';
                message +=     '<div class="review-content">';
                message +=        '<span style="float: left"><i class="fa fa-comment" aria-hidden="true"></i>'+response.review+'</span>';
                message +=        '<span style="float: right;"> <a href="#" class="awst_review_edit" data-item-id="'+response.review_id+'">Edit</a> &nbsp;|&nbsp; <a href="#" style="color: #FF0000" class="awst_review_delete" data-item-id="'+response.review_id+'">Delete</a></span>';
                message +=        '<div class="clear"></div>';
                message +=    '</div>';
                message +=    '<div class="review-detail">';
                message +=        '<span class="review-author"><i class="fa fa-user" aria-hidden="true"></i>'+response.user+'</span>';
                message +=        '<span class="review-date"><i class="fa fa-calendar" aria-hidden="true"></i>'+response.review_date+'</span>';
                message +=    '</div>';
                message += '</li>';*/

                var message = '';
                message += '<li>';
                message +=     '<div class="review-content">';
                message +=        '<span class="review_content_'+response.review_id+'" style="float: left"><i class="fa fa-comment" aria-hidden="true"></i>'+response.review+'</span>';
                message +=        '<span style="float: right;"> <a href="#" style="display: none" class="awst_review_edit" data-item-id="'+response.review_id+'">Save Changes</a> <a href="#" class="awst_review_edit_show" data-item-id="'+response.review_id+'">Edit</a> &nbsp;|&nbsp; <a href="#" style="color: #FF0000" class="awst_review_delete" data-item-id="'+response.review_id+'">Delete</a></span>';
                message +=        '<div class="clear"></div>';
                message +=        '<p><span class="edit_container" style="display: none"><textarea class="edit_review_box_'+response.review_id+'">'+response.review+'</textarea></span></p>';
                message +=    '</div>';
                message +=    '<div class="review-detail">';
                message +=        '<span class="review-author"><i class="fa fa-user" aria-hidden="true"></i>'+response.user+'</span>';
                message +=        '<span class="review-date"><i class="fa fa-calendar" aria-hidden="true"></i>'+response.review_date+'</span>';
                message +=    '</div>';
                message += '</li>';

                if(response.status == 'success'){
                    jQuery("#review-list ul").prepend(jQuery(message).hide().fadeIn(2000));
                    jQuery('#review_'+post_id).val("");
                }else if(response.status == 'error'){
                    jQuery('#review_'+post_id).val("");
                    alert(response.message);
                }

            }
        });
    });


    /*functionlity to delete reviews */
    jQuery('body').on('click', '.awst_review_delete', function(){
        var item = jQuery(this).attr('data-item-id');
        var element = jQuery(this);

        var site_url = jQuery("meta[key='awst_site_url']").attr('value');
        var ajaxUrl  = site_url+"/wp-admin/admin-ajax.php";

        jQuery.ajax({
            type:"POST",
            url: ajaxUrl,
            data: {action:'awst_ajax_review_delete', post_id: item},
            success:function(data){
                response = JSON.parse(data);
                if(response.status == 'success'){
                    jQuery(element).parent().parent().parent().fadeOut(500);
                }
            }
        });
        return false;
    });

    /*functionlity to reviews reviews */
    jQuery('body').on('click', '.awst_review_edit', function(){

        var element     = jQuery(this);
        var item        = jQuery(this).attr('data-item-id');
        var containerID = '.edit_review_box_'+item;
        var content     = jQuery(containerID).val();

        var review_content_item =  '.review_content_'+item;


        var site_url = jQuery("meta[key='awst_site_url']").attr('value');
        var ajaxUrl  = site_url+"/wp-admin/admin-ajax.php";

        jQuery.ajax({
            type:"POST",
            url: ajaxUrl,
            data: {action:'awst_ajax_review_edit', post_id: item, post_content: content},
            success:function(data){
                jQuery('.awst_review_edit').fadeOut(1000);
                jQuery('.edit_container').fadeOut(1000);
                response = JSON.parse(data);
                var htmlText = '<i aria-hidden="true" class="fa fa-comment"></i>'+content;
                jQuery(review_content_item).html(htmlText);

                console.log(review_content_item);
                console.log(htmlText);


            }
        });
        return false;
    });


    jQuery('body').on('click', '.awst_review_edit_show', function(){
        jQuery('.awst_review_edit').fadeIn(1000);
        jQuery('.edit_container').fadeIn(1000);
        return false;
    });




});

// custom redcolab

jQuery(document).ready(function ($) {

	$('.activity .awst_like_user_list').each(function (idx) {
		// move list of people who liked a post to after the like button container
		$(this).parent().after($(this));
	});

	$('.awst_like .total_like').on('click', function () {
		$(this).parent().parent().parent().find(".awst_like_user_list").first().slideToggle();
	});

	var addOrRemoveOwnLike = function addOrRemoveOwnLike() {

		// console.log( REDCOLAB );

		if (REDCOLAB.user_logged_in == "0") return;

		var isLiked = $(this).find('i').hasClass('fa-thumbs-up');
		var postId = $(this).find('i').data('post-id');

		// console.log( isLiked );
		// console.log( postId );

		if (isLiked === true) {

			// console.log( $( 'li#activity-' + postId ).find('.awst_like_user_list a:contains("@' + REDCOLAB.user_username + '")') );

			$('li#activity-' + postId).find('.activity-content .awst_like_user_list a:contains("@' + REDCOLAB.user_username + '")').remove();
		} else {
			//add to list
			var new_like = $('<a href="' + REDCOLAB.user_profile_url + '">@' + REDCOLAB.user_username + '</a>');

			$('li#activity-' + postId).find('.activity-content .awst_like_user_list').prepend(new_like);

			// console.log( $( 'li#activity-' + postId ).find('.awst_like_user_list') );
		}
	};

	var addOrRemoveOwnLikeActivityComment = function addOrRemoveOwnLikeActivityComment() {

		// console.log( REDCOLAB );

		if (REDCOLAB.user_logged_in == "0") return;

		var isLiked = $(this).find('i').hasClass('fa-thumbs-up');
		var postId = $(this).find('i').data('post-id');

		console.log( isLiked );
		console.log( postId );

		if (isLiked === true) {

			console.log( $( 'li#acomment-' + postId ).find('.awst_like_user_list a:contains("@' + REDCOLAB.user_username + '")').first() );

			$('li#acomment-' + postId).find('.awst_like_user_list a:contains("@' + REDCOLAB.user_username + '")').first().remove();
		} else {
			//add to list
			var new_like = $('<a href="' + REDCOLAB.user_profile_url + '">@' + REDCOLAB.user_username + '</a>');

			$('li#acomment-' + postId).find('.awst_like_user_list').first().prepend(new_like);

			console.log( $( 'li#acomment-' + postId ).find('.awst_like_user_list').first() );
		}
	};

	$('.activity-content .awst_like_btn').bind('click', addOrRemoveOwnLike);
	$('.activity-comments .awst_like_btn').bind('click', addOrRemoveOwnLikeActivityComment);

	$('.load-more a').click(function () {
		window.setTimeout(function () {
			$('.activity .bp-activity-container .awst_like_user_list').each(function (idx) {
				// move list of people who liked a post to after the like button container
				$(this).parent().after($(this));
			});
			$('.awst_like .total_like').off('click');
			$('.awst_like .total_like').on('click', function () {
				$(this).parent().parent().parent().find(".awst_like_user_list").slideToggle();
			});

			$('.activity-content .awst_like_btn').unbind('click', addOrRemoveOwnLike);
			$('.activity-content .awst_like_btn').bind('click', addOrRemoveOwnLike);

			$('.activity-comments .awst_like_btn').unbind('click', addOrRemoveOwnLikeActivityComment);
			$('.activity-comments .awst_like_btn').bind('click', addOrRemoveOwnLikeActivityComment);

		}, 10000);
		window.setTimeout(function () {
			$('.activity .bp-activity-container .awst_like_user_list').each(function (idx) {
				// move list of people who liked a post to after the like button container
				$(this).parent().after($(this));
			});
			$('.awst_like .total_like').off('click');
			$('.awst_like .total_like').on('click', function () {
				$(this).parent().parent().parent().find(".awst_like_user_list").slideToggle();
			});

			$('.activity-content .awst_like_btn').unbind('click', addOrRemoveOwnLike);
			$('.activity-content .awst_like_btn').bind('click', addOrRemoveOwnLike);

			$('.activity-comments .awst_like_btn').unbind('click', addOrRemoveOwnLikeActivityComment);
			$('.activity-comments .awst_like_btn').bind('click', addOrRemoveOwnLikeActivityComment);

		}, 20000);
		window.setTimeout(function () {
			$('.activity .bp-activity-container .awst_like_user_list').each(function (idx) {
				// move list of people who liked a post to after the like button container
				$(this).parent().after($(this));
			});
			$('.awst_like .total_like').off('click');
			$('.awst_like .total_like').on('click', function () {
				$(this).parent().parent().parent().find(".awst_like_user_list").slideToggle();
			});

			$('.activity-content .awst_like_btn').unbind('click', addOrRemoveOwnLike);
			$('.activity-content .awst_like_btn').bind('click', addOrRemoveOwnLike);

			$('.activity-comments .awst_like_btn').unbind('click', addOrRemoveOwnLikeActivityComment);
			$('.activity-comments .awst_like_btn').bind('click', addOrRemoveOwnLikeActivityComment);

		}, 30000);
		window.setTimeout(function () {
			$('.activity .bp-activity-container .awst_like_user_list').each(function (idx) {
				// move list of people who liked a post to after the like button container
				$(this).parent().after($(this));
			});
			$('.awst_like .total_like').off('click');
			$('.awst_like .total_like').on('click', function () {
				$(this).parent().parent().parent().find(".awst_like_user_list").slideToggle();
			});

			$('.activity-content .awst_like_btn').unbind('click', addOrRemoveOwnLike);
			$('.activity-content .awst_like_btn').bind('click', addOrRemoveOwnLike);

			$('.activity-comments .awst_like_btn').unbind('click', addOrRemoveOwnLikeActivityComment);
			$('.activity-comments .awst_like_btn').bind('click', addOrRemoveOwnLikeActivityComment);

		}, 40000);
	});
});
