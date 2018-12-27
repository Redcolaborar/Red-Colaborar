jQuery(document).ready(function ($) {

	$('.activity .awst_like_user_list').each(function (idx) {
		// move list of people who liked a post to after the like button container
		$(this).parent().after($(this));
	});

	$('.awst_like .total_like').on('click', function () {
		$(this).parent().parent().parent().find(".awst_like_user_list").slideToggle();
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
