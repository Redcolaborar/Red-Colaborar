'use strict';

/**
 * [...] Activity Menu.
 *
 * @since   NEXT
 * @package Red Colaborar
 */

/* globals jQuery */
if (!window.hasOwnProperty('wdsBPActivityMenu')) {

	/**
  * Activity Menu.
  *
  * @since           NEXT
  * @return {Object} Public object.
  */
	window.wdsBPActivityMenu = function ($, pub) {

		/**
   * Clicking the [...] button.
   *
   * @author Aubrey Portwood
   * @since  NEXT
   *
   * @param  {Object} e Event object.
   */
		function click(e) {

			// Get the button.
			var $target = $(e.target).closest('.bp-activity-menu-trigger');

			// Find the menu near the button.
			var $menu = $target.siblings('.bp-activity-menu');

			// Toggle the menu.
			$menu.fadeToggle();
		}

		// Delegate any clicks on body .bp-activity-menu-trigger.
		$('body').on('click.wdsBPActivityMenu', '.bp-activity-menu-trigger', click);

		return pub; // Return public things.
	}(jQuery, {});
} // End if().
'use strict';

/* globals console, jQuery */
if (!window.hasOwnProperty('wdsActivityPost')) {

	/**
  * Activity post.
  */
	window.wdsActivityPost = function ($, pub) {
		$(document).ready(function () {
			var $body = $('body');
			var $dropZone = $('#mpp-upload-dropzone-activity');
			var $whatsNew = $('#whats-new');
			var $uploadButton = $('#mpp-all-upload');
			var $selectFile = $('#mpp-upload-media-button-activity');

			// Are we disabling this JS temporarily via ?disableWdsActivityPost.
			var disabled = 1 === window.location.search.indexOf('disableWdsActivityPost');
			if (disabled) {
				return;
			}

			if (window.innerWidth < 900) {

				// Bail on mobile.
				return;
			}

			// Requirements.
			if ($whatsNew.length && $dropZone.length && $uploadButton.length) {

				// Tell SASS to do it's thing!
				$body.addClass('activity-post-js-ready');

				// Make sure the dropzone is activated the first time!
				$uploadButton.click();

				// When the upload camera icon is clicked from here on...
				$uploadButton.on('click', function () {

					// Click the "Select File" button in the dropzone that's hidden.
					$selectFile.click();
				});
			} else {

				// Bail, the required elements we need aren't there.
				return;
			}

			/**
    * Activate the dropzone placement.
    *
    * This places the dropzone over the posting area when files
    * are dragged over it.
    *
    * @author Aubrey Portwood
    * @since  Friday, 11 24, 2017
    *
    * @param  {Object} e Event object.
    */
			function activateDropzonePlacement(e) {
				var dt = e.originalEvent.dataTransfer;
				var files = dt.types && (dt.types.indexOf ? -1 !== dt.types.indexOf('Files') : dt.types.contains('Files'));

				if (files && $dropZone.length) {
					$dropZone.addClass('active');
				}
			}

			/**
    * Deactivate the dropzone placement.
    *
    * This makes the dropzone placement totally hidden.
    *
    * @author Aubrey Portwood
    * @since  Friday, 11 24, 2017
    */
			function deactivateDropzonePlacement() {
				$dropZone.removeClass('active');
			}

			/**
    * Focus on the post textarea.
    *
    * This sets the cursor in the posting textarea
    * and should activate the other items, etc.
    *
    * @author Aubrey Portwood
    * @since  Friday, 11 24, 2017
    */
			function focusOnPost() {
				$whatsNew.focus();
			}

			// When I drag something over the post textarea activate the dropzone over it.
			$whatsNew.on('dragover', activateDropzonePlacement);

			// Focus on the post textarea when something is dropped on the dropzone.
			$dropZone.on('drop.post', focusOnPost);

			// Deactivate the dropzone placement when something is dropped on the dropzone.
			$dropZone.on('drop.deactivateDropzonePlacement', deactivateDropzonePlacement);

			// Deactivate the dropzone placement when something leaves the window.
			$(window).on('mouseleave', deactivateDropzonePlacement);
		});return pub; // Return public things.
	}(jQuery, {});
} // End if().
'use strict';

/**
 * Back to Top Button Script.
 */
window.wdsBackToTop = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			body: $('html, body'),
			window: $(window),
			backToTopSelector: $('.back-to-top-button')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.window.on('scroll', app.doBackToTopButton);
		app.$c.backToTopSelector.on('click', app.doBackToTop);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.backToTopSelector.length;
	};

	// Show/Hide Back to Top Button.
	app.doBackToTopButton = function () {

		var scroll = app.$c.window.scrollTop();

		if (250 < scroll) {
			app.$c.backToTopSelector.fadeIn(200);
		} else {
			app.$c.backToTopSelector.fadeOut(200);
		}
	};

	// Scroll back to top on click.
	app.doBackToTop = function (e) {

		e.preventDefault();

		app.$c.body.animate({ scrollTop: 0 }, 700);
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsBackToTop);
'use strict';

/**
 * Hide the BuddyPress Comments after they have been displayed.
 *
 * @author Corey Collins
 */
window.ShowHideBPComments = {};
(function (window, $, app) {

	// Constructor
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things
	app.cache = function () {
		app.$c = {
			window: $(window),
			commentsContainer: $('.activity-comments'),
			trigger: $('.show-hide-comments-trigger'),
			bpShowCommentsLink: $('li.show-all > a')
		};
	};

	// Combine all events
	app.bindEvents = function () {

		// Listen for a click on our trigger.
		app.$c.trigger.on('click', app.showHideComments);

		// Listen for a click on BP's trigger.
		app.$c.bpShowCommentsLink.on('click', app.bpShowHideComments);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.commentsContainer.length;
	};

	// Show/Hide the comments on click.
	app.bpShowHideComments = function () {

		var parentUL = $(this).closest('ul'),
		    showHideTrigger = parentUL.siblings('.show-hide-comments-trigger');

		// Make our hide/show button visible.
		parentUL.addClass('bp-visible');
		parentUL.siblings('.show-hide-comments-trigger').addClass('is-visible');

		// Loop through all of the sibling LIs.
		$(this).parent('li').siblings('li').each(function () {

			// If an LI is set to display: none, give it a class we can use.
			if ('none' === $(this).css('display')) {
				$(this).addClass('comment-to-hide'); // Give it a class so we can do things with CSS.
				$(this).removeAttr('style'); // Remove the inline styles.
			}
		});

		// Count the number of LIs, now that the Show All link has been removed.
		var commentCount = $(this).closest('.activity-comments').find('li').length - 1;

		// Add the comment count to our trigger data attribute.
		showHideTrigger.attr('data-comment-count', commentCount);

		// Append our number to our link.
		showHideTrigger.attr('data-show-text', showHideTrigger.attr('data-show-text') + ' (' + commentCount + ')');
	};

	// Show/Hide the comments on click.
	app.showHideComments = function () {

		var trigger = $(this);

		// Loop through all of the sibling LIs.
		trigger.siblings('ul').toggleClass('hide-extra-comments');

		// If comments are hidden, change the trigger text.
		if (trigger.siblings('ul').hasClass('hide-extra-comments')) {
			trigger.text(trigger.attr('data-show-text'));
		} else {
			trigger.text(trigger.attr('data-hide-text'));
		}
	};

	// Engage
	$(app.init);
})(window, jQuery, window.ShowHideBPComments);
'use strict';

/**
 * Datepicker IE11 Script.
 */
window.datePickerObject = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.doDatePicker();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			body: $('body'),
			filters: $('#wds-recolaborar-sidebar-filters'),
			dateBeforePicker: $('#date-before'),
			dateAfterPicker: $('#date-after')
		};
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.filters.length;
	};

	// Some function.
	app.doDatePicker = function () {

		var dateFormat = {
			dateFormat: 'yy-mm-dd'
		};

		app.$c.dateBeforePicker.datepicker(dateFormat);

		app.$c.dateAfterPicker.datepicker(dateFormat);
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.datePickerObject);
'use strict';

/**
 * Force Post Category Script.
 */
window.forcePostCategoryObject = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			whatsNewContainer: $('#whats-new'),
			forcePostCategorySelector: $('.edit-category-activity'), // select
			submitButton: $('#whats-new-submit input')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.forcePostCategorySelector.on('change', app.doForcePostCategory);

		// Force input button to stay disabled until js below.
		app.$c.whatsNewContainer.focus(function () {
			app.$c.submitButton.prop('disabled', true);

			// Retain enabled button if selectedIndex is anything other than default.
			if (0 !== app.$c.forcePostCategorySelector[0].selectedIndex) {
				app.$c.submitButton.prop('disabled', false);
			}
		});
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.forcePostCategorySelector.length;
	};

	// Get Selection and Show/Hide Button if no selection made.
	app.doForcePostCategory = function () {

		var selection = this.selectedIndex;

		app.$c.submitButton.prop('disabled', true);

		if (0 !== selection) {
			app.$c.submitButton.prop('disabled', false);
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.forcePostCategoryObject);
'use strict';

/**
 * File google-translate-button.js
 *
 * Hide the button when we scroll to the bottom of the page so it doesn't interfere with the footer..
 */
window.WDSGoogleTranslateButton = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		app.bindEvents();
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			body: $('body'),
			window: $(window),
			googleContainer: $('.goog-te-gadget'),
			googleElement: $('#google_translate_element')
		};
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.googleContainer.length;
	};

	// Combine all events.
	app.bindEvents = function () {

		// Hide contact buttons on scroll to bottom.
		app.$c.window.on('scroll', app.hideContainer);
	};

	// Hide Contact Buttons if nearly the bottom.
	app.hideContainer = function () {

		var scrollTop = app.$c.window.scrollTop() + 200;

		if (200 < scrollTop) {
			app.$c.googleContainer.fadeOut();
			app.$c.googleElement.fadeOut();
		} else {
			app.$c.googleContainer.fadeIn();
			app.$c.googleElement.fadeIn();
		}
	};

	$(app.init);
})(window, jQuery, window.WDSGoogleTranslateButton);
'use strict';

/**
 * Show/Hide the Search Form in the header.
 *
 * @author Corey Collins
 */
window.ShowHideSearchForm = {};
(function (window, $, app) {

	// Constructor
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things
	app.cache = function () {
		app.$c = {
			window: $(window),
			body: $('body'),
			headerSearchForm: $('.site-header-action .cta-button')
		};
	};

	// Combine all events
	app.bindEvents = function () {
		app.$c.headerSearchForm.on('keyup touchstart click', app.showHideSearchForm);
		app.$c.body.on('keyup touchstart click', app.hideSearchForm);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.headerSearchForm.length;
	};

	// Adds the toggle class for the search form.
	app.showHideSearchForm = function () {
		app.$c.body.toggleClass('search-form-visible');
	};

	// Hides the search form if we click outside of its container.
	app.hideSearchForm = function (event) {

		if (!$(event.target).parents('div').hasClass('site-header-action')) {
			app.$c.body.removeClass('search-form-visible');
		}
	};

	// Engage
	$(app.init);
})(window, jQuery, window.ShowHideSearchForm);
'use strict';

/**
 * File hero-carousel.js
 *
 * Create a carousel if we have more than one hero slide.
 */
window.wdsHeroCarousel = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			heroCarousel: $('.carousel')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.window.on('load', app.doSlick);
		app.$c.window.on('load', app.doFirstAnimation);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.heroCarousel.length;
	};

	// Animate the first slide on window load.
	app.doFirstAnimation = function () {

		// Get the first slide content area and animation attribute.
		var firstSlide = app.$c.heroCarousel.find('[data-slick-index=0]'),
		    firstSlideContent = firstSlide.find('.hero-content'),
		    firstAnimation = firstSlideContent.attr('data-animation');

		// Add the animation class to the first slide.
		firstSlideContent.addClass(firstAnimation);
	};

	// Animate the slide content.
	app.doAnimation = function () {
		var slides = $('.slide'),
		    activeSlide = $('.slick-current'),
		    activeContent = activeSlide.find('.hero-content'),


		// This is a string like so: 'animated someCssClass'.
		animationClass = activeContent.attr('data-animation'),
		    splitAnimation = animationClass.split(' '),


		// This is the 'animated' class.
		animationTrigger = splitAnimation[0];

		// Go through each slide to see if we've already set animation classes.
		slides.each(function () {
			var slideContent = $(this).find('.hero-content');

			// If we've set animation classes on a slide, remove them.
			if (slideContent.hasClass('animated')) {

				// Get the last class, which is the animate.css class.
				var lastClass = slideContent.attr('class').split(' ').pop();

				// Remove both animation classes.
				slideContent.removeClass(lastClass).removeClass(animationTrigger);
			}
		});

		// Add animation classes after slide is in view.
		activeContent.addClass(animationClass);
	};

	// Allow background videos to autoplay.
	app.playBackgroundVideos = function () {

		// Get all the videos in our slides object.
		$('video').each(function () {

			// Let them autoplay. TODO: Possibly change this later to only play the visible slide video.
			this.play();
		});
	};

	// Kick off Slick.
	app.doSlick = function () {
		app.$c.heroCarousel.on('init', app.playBackgroundVideos);

		app.$c.heroCarousel.slick({
			autoplay: true,
			autoplaySpeed: 5000,
			arrows: false,
			dots: false,
			focusOnSelect: true,
			waitForAnimate: true
		});

		app.$c.heroCarousel.on('afterChange', app.doAnimation);
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsHeroCarousel);
'use strict';

/**
 * File js-enabled.js
 *
 * If Javascript is enabled, replace the <body> class "no-js".
 */
document.body.className = document.body.className.replace('no-js', 'js');
'use strict';

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

			$('li#activity-' + postId).find('.awst_like_user_list a:contains("@' + REDCOLAB.user_username + '")').remove();
		} else {
			//add to list
			var new_like = $('<a href="' + REDCOLAB.user_profile_url + '">@' + REDCOLAB.user_username + '</a>');

			$('li#activity-' + postId).find('.awst_like_user_list').prepend(new_like);

			// console.log( $( 'li#activity-' + postId ).find('.awst_like_user_list') );
		}
	};

	$('.awst_like_btn').bind('click', addOrRemoveOwnLike);

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

			$('.awst_like_btn').unbind('click', addOrRemoveOwnLike);
			$('.awst_like_btn').bind('click', addOrRemoveOwnLike);
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

			$('.awst_like_btn').unbind('click', addOrRemoveOwnLike);
			$('.awst_like_btn').bind('click', addOrRemoveOwnLike);
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

			$('.awst_like_btn').unbind('click', addOrRemoveOwnLike);
			$('.awst_like_btn').bind('click', addOrRemoveOwnLike);
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

			$('.awst_like_btn').unbind('click', addOrRemoveOwnLike);
			$('.awst_like_btn').bind('click', addOrRemoveOwnLike);
		}, 40000);
	});
});
'use strict';

/**
 * MediaPress Upload Script.
 */
window.WDS_mediaPress_Object = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			mediaPressSelector: $('#whats-new-options'),
			mediaUploader: $('#mpp-upload-dropzone-activity.mpp-dropzone')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.mediaPressSelector.bind('DOMSubtreeModified', app.doMediaPress);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.mediaPressSelector.length;
	};

	// Hide MediaPress if parent dialoge is clicked.
	app.doMediaPress = function () {

		var active = $(this);

		if (app.$c.mediaPressSelector.attr('style').indexOf('none') === -1) {
			app.$c.mediaUploader.hide();
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.WDS_mediaPress_Object);
'use strict';

/**
 * File: mobile-menu.js
 *
 * Create an accordion style dropdown.
 */
window.wdsMobileMenu = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			body: $('body'),
			window: $(window),
			subMenuContainer: $('.mobile-menu .sub-menu, .utility-navigation .sub-menu'),
			subSubMenuContainer: $('.mobile-menu .sub-menu .sub-menu'),
			subMenuParentItem: $('.mobile-menu li.menu-item-has-children, .utility-navigation li.menu-item-has-children'),
			offCanvasContainer: $('.off-canvas-container')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.window.on('load', app.addDownArrow);
		app.$c.subMenuParentItem.on('click', app.toggleSubmenu);
		app.$c.subMenuParentItem.on('transitionend', app.resetSubMenu);
		app.$c.offCanvasContainer.on('transitionend', app.forceCloseSubmenus);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.subMenuContainer.length;
	};

	// Reset the submenus after it's done closing.
	app.resetSubMenu = function () {

		// When the list item is done transitioning in height,
		// remove the classes from the submenu so it is ready to toggle again.
		if ($(this).is('li.menu-item-has-children') && !$(this).hasClass('is-visible')) {
			$(this).find('ul.sub-menu').removeClass('slideOutLeft is-visible');
		}
	};

	// Slide out the submenu items.
	app.slideOutSubMenus = function (el) {

		// If this item's parent is visible and this is not, bail.
		if (el.parent().hasClass('is-visible') && !el.hasClass('is-visible')) {
			return;
		}

		// If this item's parent is visible and this item is visible, hide its submenu then bail.
		if (el.parent().hasClass('is-visible') && el.hasClass('is-visible')) {
			el.removeClass('is-visible').find('.sub-menu').removeClass('slideInLeft').addClass('slideOutLeft');
			return;
		}

		app.$c.subMenuContainer.each(function () {

			// Only try to close submenus that are actually open.
			if ($(this).hasClass('slideInLeft')) {

				// Close the parent list item, and set the corresponding button aria to false.
				$(this).parent().removeClass('is-visible').find('.parent-indicator').attr('aria-expanded', false);

				// Slide out the submenu.
				$(this).removeClass('slideInLeft').addClass('slideOutLeft');
			}
		});
	};

	// Add the down arrow to submenu parents.
	app.addDownArrow = function () {
		app.$c.subMenuParentItem.prepend('<button type="button" aria-expanded="false" class="parent-indicator" aria-label="Open submenu"><span class="down-arrow"></span></button>');
	};

	// Deal with the submenu.
	app.toggleSubmenu = function (e) {

		var el = $(this),
		    // The menu element which was clicked on.
		subMenu = el.children('ul.sub-menu'),
		    // The nearest submenu.
		$target = $(e.target); // the element that's actually being clicked (child of the li that triggered the click event).

		// Figure out if we're clicking the button or its arrow child,
		// if so, we can just open or close the menu and bail.
		if ($target.hasClass('down-arrow') || $target.hasClass('parent-indicator')) {

			// First, collapse any already opened submenus.
			app.slideOutSubMenus(el);

			if (!subMenu.hasClass('is-visible')) {

				// Open the submenu.
				app.openSubmenu(el, subMenu);
			}

			return false;
		}
	};

	// Open a submenu.
	app.openSubmenu = function (parent, subMenu) {

		// Expand the list menu item, and set the corresponding button aria to true.
		parent.addClass('is-visible').find('.parent-indicator').attr('aria-expanded', true);

		// Slide the menu in.
		subMenu.addClass('is-visible animated slideInLeft');
	};

	// Force close all the submenus when the main menu container is closed.
	app.forceCloseSubmenus = function () {

		// The transitionend event triggers on open and on close, need to make sure we only do this on close.
		if (!$(this).hasClass('is-visible')) {
			app.$c.subMenuParentItem.removeClass('is-visible').find('.parent-indicator').attr('aria-expanded', false);
			app.$c.subMenuContainer.removeClass('is-visible slideInLeft');
			app.$c.body.css('overflow', 'visible');
			app.$c.body.unbind('touchstart');
		}

		if ($(this).hasClass('is-visible')) {
			app.$c.body.css('overflow', 'hidden');
			app.$c.body.bind('touchstart', function (e) {
				if (!$(e.target).parents('.off-canvas-container, .off-canvas-screen, .site-header')[0]) {
					e.preventDefault();
				}
			});
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsMobileMenu);
'use strict';

/**
 * File modal.js
 *
 * Deal with multiple modals and their media.
 */
window.wdsModal = {};
(function (window, $, app) {

	var $modalToggle = void 0,
	    $focusableChildren = void 0,
	    $player = void 0,
	    $tag = document.createElement('script'),
	    $firstScriptTag = document.getElementsByTagName('script')[0],
	    YT = void 0;

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			$firstScriptTag.parentNode.insertBefore($tag, $firstScriptTag);
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			'body': $('body')
		};
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return $('.modal-trigger').length;
	};

	// Combine all events.
	app.bindEvents = function () {

		// Trigger a modal to open.
		app.$c.body.on('click touchstart', '.modal-trigger', app.openModal);

		// Trigger the close button to close the modal.
		app.$c.body.on('click touchstart', '.close', app.closeModal);

		// Allow the user to close the modal by hitting the esc key.
		app.$c.body.on('keydown', app.escKeyClose);

		// Allow the user to close the modal by clicking outside of the modal.
		app.$c.body.on('click touchstart', 'div.modal-open', app.closeModalByClick);

		// Listen to tabs, trap keyboard if we need to
		app.$c.body.on('keydown', app.trapKeyboardMaybe);
	};

	// Open the modal.
	app.openModal = function () {

		// Store the modal toggle element
		$modalToggle = $(this);

		// Figure out which modal we're opening and store the object.
		var $modal = $($(this).data('target'));

		// Display the modal.
		$modal.addClass('modal-open');

		// Add body class.
		app.$c.body.addClass('modal-open');

		// Find the focusable children of the modal.
		// This list may be incomplete, really wish jQuery had the :focusable pseudo like jQuery UI does.
		// For more about :input see: https://api.jquery.com/input-selector/
		$focusableChildren = $modal.find('a, :input, [tabindex]');

		// Ideally, there is always one (the close button), but you never know.
		if (0 < $focusableChildren.length) {

			// Shift focus to the first focusable element.
			$focusableChildren[0].focus();
		}
	};

	// Close the modal.
	app.closeModal = function () {

		// Figure the opened modal we're closing and store the object.
		var $modal = $($('div.modal-open .close').data('target')),


		// Find the iframe in the $modal object.
		$iframe = $modal.find('iframe');

		// Only do this if there are any iframes.
		if ($iframe.length) {

			// Get the iframe src URL.
			var url = $iframe.attr('src');

			// Removing/Readding the URL will effectively break the YouTube API.
			// So let's not do that when the iframe URL contains the enablejsapi parameter.
			if (!url.includes('enablejsapi=1')) {

				// Remove the source URL, then add it back, so the video can be played again later.
				$iframe.attr('src', '').attr('src', url);
			} else {

				// Use the YouTube API to stop the video.
				$player.stopVideo();
			}
		}

		// Finally, hide the modal.
		$modal.removeClass('modal-open');

		// Remove the body class.
		app.$c.body.removeClass('modal-open');

		// Revert focus back to toggle element
		$modalToggle.focus();
	};

	// Close if "esc" key is pressed.
	app.escKeyClose = function (event) {
		if (27 === event.keyCode) {
			app.closeModal();
		}
	};

	// Close if the user clicks outside of the modal
	app.closeModalByClick = function (event) {

		// If the parent container is NOT the modal dialog container, close the modal
		if (!$(event.target).parents('div').hasClass('modal-dialog')) {
			app.closeModal();
		}
	};

	// Trap the keyboard into a modal when one is active.
	app.trapKeyboardMaybe = function (event) {

		// We only need to do stuff when the modal is open and tab is pressed.
		if (9 === event.which && 0 < $('.modal-open').length) {
			var $focused = $(':focus'),
			    focusIndex = $focusableChildren.index($focused);

			if (0 === focusIndex && event.shiftKey) {

				// If this is the first focusable element, and shift is held when pressing tab, go back to last focusable element.
				$focusableChildren[$focusableChildren.length - 1].focus();
				event.preventDefault();
			} else if (!event.shiftKey && focusIndex === $focusableChildren.length - 1) {

				// If this is the last focusable element, and shift is not held, go back to the first focusable element.
				$focusableChildren[0].focus();
				event.preventDefault();
			}
		}
	};

	// Hook into YouTube <iframe>.
	app.onYouTubeIframeAPIReady = function () {
		var $modal = $('div.modal'),
		    $iframeid = $modal.find('iframe').attr('id');

		$player = new YT.Player($iframeid, {
			events: {
				'onReady': app.onPlayerReady,
				'onStateChange': app.onPlayerStateChange
			}
		});
	};

	// Do something on player ready.
	app.onPlayerReady = function () {};

	// Do something on player state change.
	app.onPlayerStateChange = function () {

		// Set focus to the first focusable element inside of the modal the player is in.
		$(event.target.a).parents('.modal').find('a, :input, [tabindex]').first().focus();
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsModal);
'use strict';

/**
 * File: navigation-primary.js
 *
 * Helpers for the primary navigation.
 */
window.wdsPrimaryNavigation = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			subMenuContainer: $('.main-navigation .sub-menu'),
			subMenuParentItem: $('.main-navigation li.menu-item-has-children'),
			siteHeader: $('.site-header')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.window.on('load', app.addDownArrow);
		app.$c.subMenuParentItem.find('a').on('focusin focusout', app.toggleFocus);
		app.$c.window.on('scroll', app.scrollHelper);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.subMenuContainer.length;
	};

	// Add the down arrow to submenu parents.
	app.addDownArrow = function () {
		app.$c.subMenuParentItem.find('> a').append('<span class="caret-down" aria-hidden="true"></span>');
	};

	// Toggle the focus class on the link parent.
	app.toggleFocus = function () {
		$(this).parents('li.menu-item-has-children').toggleClass('focus');
	};

	// Add helper to condense nav on scroll for desktop
	app.scrollHelper = function () {
		var width = app.$c.window.width(),
		    scroll = app.$c.window.scrollTop();

		if ('900' <= width) {

			if ('25' <= scroll) {
				app.$c.siteHeader.addClass('scrolling');
			} else {
				app.$c.siteHeader.removeClass('scrolling');
			}
		} else {
			app.$c.siteHeader.removeClass('scrolling');
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsPrimaryNavigation);
'use strict';

/**
 * File: off-canvas.js
 *
 * Help deal with the off-canvas mobile menu.
 */
window.wdsoffCanvas = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			body: $('body'),
			offCanvasClose: $('.off-canvas-close'),
			offCanvasContainer: $('.off-canvas-container'),
			offCanvasOpen: $('.off-canvas-open'),
			offCanvasScreen: $('.off-canvas-screen')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.body.on('keydown', app.escKeyClose);
		app.$c.offCanvasClose.on('click', app.closeoffCanvas);
		app.$c.offCanvasOpen.on('click', app.toggleoffCanvas);
		app.$c.offCanvasScreen.on('click', app.closeoffCanvas);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.offCanvasContainer.length;
	};

	// To show or not to show?
	app.toggleoffCanvas = function () {

		if ('true' === $(this).attr('aria-expanded')) {
			app.closeoffCanvas();
		} else {
			app.openoffCanvas();
		}
	};

	// Show that drawer!
	app.openoffCanvas = function () {
		app.$c.offCanvasContainer.addClass('is-visible');
		app.$c.offCanvasOpen.addClass('is-visible');
		app.$c.offCanvasScreen.addClass('is-visible');

		app.$c.offCanvasOpen.attr('aria-expanded', true);
		app.$c.offCanvasContainer.attr('aria-hidden', false);

		app.$c.offCanvasContainer.find('button').first().focus();
	};

	// Close that drawer!
	app.closeoffCanvas = function () {
		app.$c.offCanvasContainer.removeClass('is-visible');
		app.$c.offCanvasOpen.removeClass('is-visible');
		app.$c.offCanvasScreen.removeClass('is-visible');

		app.$c.offCanvasOpen.attr('aria-expanded', false);
		app.$c.offCanvasContainer.attr('aria-hidden', true);

		app.$c.offCanvasOpen.focus();
	};

	// Close drawer if "esc" key is pressed.
	app.escKeyClose = function (event) {
		if (27 === event.keyCode) {
			app.closeoffCanvas();
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsoffCanvas);
'use strict';

/**
 * Open Sidebar Activity Filter Script.
 */
window.WDSOpenSidebarObject = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			openSidebarContainer: $('#wds-recolaborar-sidebar-filters'),
			openSidebarSelector: $('#search-field')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		$(document).on('click', app.doCloseSidebar);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.openSidebarContainer.length;
	};

	app.doCloseSidebar = function (e) {
		if (!app.$c.openSidebarContainer.is(e.target) && 0 === app.$c.openSidebarContainer.has(e.target).length) {
			app.$c.openSidebarContainer.removeClass('open-sidebar');
		} else {
			app.$c.openSidebarContainer.addClass('open-sidebar');
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.WDSOpenSidebarObject);
'use strict';

/**
 * Profile Bar Upload Script.
 */
window.WDSProfileBarObject = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			profileBarSelector: $('.button-dropdown'),
			checkBox: $('.dropdown-open')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.profileBarSelector.on('click', app.doProfileMenu);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.profileBarSelector.length;
	};

	// Menu
	app.doProfileMenu = function () {

		if (app.$c.profileBarSelector.hasClass('menu-open')) {
			app.$c.profileBarSelector.removeClass('menu-open');
		} else {
			app.$c.profileBarSelector.addClass('menu-open');
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.WDSProfileBarObject);
'use strict';

/**
 * File skip-link-focus-fix.js.
 *
 * Helps with accessibility for keyboard only users.
 *
 * Learn more: https://git.io/vWdr2
 */
(function () {
	var isWebkit = -1 < navigator.userAgent.toLowerCase().indexOf('webkit'),
	    isOpera = -1 < navigator.userAgent.toLowerCase().indexOf('opera'),
	    isIe = -1 < navigator.userAgent.toLowerCase().indexOf('msie');

	if ((isWebkit || isOpera || isIe) && document.getElementById && window.addEventListener) {
		window.addEventListener('hashchange', function () {
			var id = location.hash.substring(1),
			    element;

			if (!/^[A-z0-9_-]+$/.test(id)) {
				return;
			}

			element = document.getElementById(id);

			if (element) {
				if (!/^(?:a|select|input|button|textarea)$/i.test(element.tagName)) {
					element.tabIndex = -1;
				}

				element.focus();
			}
		}, false);
	}
})();
'use strict';

/**
 * File window-ready.js
 *
 * Add a "ready" class to <body> when window is ready.
 */
window.wdsWindowReady = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();
		app.bindEvents();
	};

	// Cache document elements.
	app.cache = function () {
		app.$c = {
			'window': $(window),
			'body': $(document.body)
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.window.load(app.addBodyClass);
	};

	// Add a class to <body>.
	app.addBodyClass = function () {
		app.$c.body.addClass('ready');
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsWindowReady);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFjdGl2aXR5LW1lbnUuanMiLCJhY3Rpdml0eS1wb3N0LmpzIiwiYmFjay10by10b3AuanMiLCJidWRkeXByZXNzLWhpZGUtY29tbWVudHMuanMiLCJkYXRlcGlja2VyLmpzIiwiZm9yY2UtcG9zdC1jYXRlZ29yeS5qcyIsImdvb2dsZS10cmFuc2xhdGUtYnV0dG9uLmpzIiwiaGVhZGVyLWJ1dHRvbi5qcyIsImhlcm8tY2Fyb3VzZWwuanMiLCJqcy1lbmFibGVkLmpzIiwibGlrZXMuanMiLCJtZWRpYXByZXNzLmpzIiwibW9iaWxlLW1lbnUuanMiLCJtb2RhbC5qcyIsIm5hdmlnYXRpb24tcHJpbWFyeS5qcyIsIm9mZi1jYW52YXMuanMiLCJvcGVuLXNpZGViYXIuanMiLCJwcm9maWxlLWJhci5qcyIsInNraXAtbGluay1mb2N1cy1maXguanMiLCJ3aW5kb3ctcmVhZHkuanMiXSwibmFtZXMiOlsid2luZG93IiwiaGFzT3duUHJvcGVydHkiLCJ3ZHNCUEFjdGl2aXR5TWVudSIsIiQiLCJwdWIiLCJjbGljayIsImUiLCIkdGFyZ2V0IiwidGFyZ2V0IiwiY2xvc2VzdCIsIiRtZW51Iiwic2libGluZ3MiLCJmYWRlVG9nZ2xlIiwib24iLCJqUXVlcnkiLCJ3ZHNBY3Rpdml0eVBvc3QiLCJkb2N1bWVudCIsInJlYWR5IiwiJGJvZHkiLCIkZHJvcFpvbmUiLCIkd2hhdHNOZXciLCIkdXBsb2FkQnV0dG9uIiwiJHNlbGVjdEZpbGUiLCJkaXNhYmxlZCIsImxvY2F0aW9uIiwic2VhcmNoIiwiaW5kZXhPZiIsImlubmVyV2lkdGgiLCJsZW5ndGgiLCJhZGRDbGFzcyIsImFjdGl2YXRlRHJvcHpvbmVQbGFjZW1lbnQiLCJkdCIsIm9yaWdpbmFsRXZlbnQiLCJkYXRhVHJhbnNmZXIiLCJmaWxlcyIsInR5cGVzIiwiY29udGFpbnMiLCJkZWFjdGl2YXRlRHJvcHpvbmVQbGFjZW1lbnQiLCJyZW1vdmVDbGFzcyIsImZvY3VzT25Qb3N0IiwiZm9jdXMiLCJ3ZHNCYWNrVG9Ub3AiLCJhcHAiLCJpbml0IiwiY2FjaGUiLCJtZWV0c1JlcXVpcmVtZW50cyIsImJpbmRFdmVudHMiLCIkYyIsImJvZHkiLCJiYWNrVG9Ub3BTZWxlY3RvciIsImRvQmFja1RvVG9wQnV0dG9uIiwiZG9CYWNrVG9Ub3AiLCJzY3JvbGwiLCJzY3JvbGxUb3AiLCJmYWRlSW4iLCJmYWRlT3V0IiwicHJldmVudERlZmF1bHQiLCJhbmltYXRlIiwiU2hvd0hpZGVCUENvbW1lbnRzIiwiY29tbWVudHNDb250YWluZXIiLCJ0cmlnZ2VyIiwiYnBTaG93Q29tbWVudHNMaW5rIiwic2hvd0hpZGVDb21tZW50cyIsImJwU2hvd0hpZGVDb21tZW50cyIsInBhcmVudFVMIiwic2hvd0hpZGVUcmlnZ2VyIiwicGFyZW50IiwiZWFjaCIsImNzcyIsInJlbW92ZUF0dHIiLCJjb21tZW50Q291bnQiLCJmaW5kIiwiYXR0ciIsInRvZ2dsZUNsYXNzIiwiaGFzQ2xhc3MiLCJ0ZXh0IiwiZGF0ZVBpY2tlck9iamVjdCIsImRvRGF0ZVBpY2tlciIsImZpbHRlcnMiLCJkYXRlQmVmb3JlUGlja2VyIiwiZGF0ZUFmdGVyUGlja2VyIiwiZGF0ZUZvcm1hdCIsImRhdGVwaWNrZXIiLCJmb3JjZVBvc3RDYXRlZ29yeU9iamVjdCIsIndoYXRzTmV3Q29udGFpbmVyIiwiZm9yY2VQb3N0Q2F0ZWdvcnlTZWxlY3RvciIsInN1Ym1pdEJ1dHRvbiIsImRvRm9yY2VQb3N0Q2F0ZWdvcnkiLCJwcm9wIiwic2VsZWN0ZWRJbmRleCIsInNlbGVjdGlvbiIsIldEU0dvb2dsZVRyYW5zbGF0ZUJ1dHRvbiIsImdvb2dsZUNvbnRhaW5lciIsImdvb2dsZUVsZW1lbnQiLCJoaWRlQ29udGFpbmVyIiwiU2hvd0hpZGVTZWFyY2hGb3JtIiwiaGVhZGVyU2VhcmNoRm9ybSIsInNob3dIaWRlU2VhcmNoRm9ybSIsImhpZGVTZWFyY2hGb3JtIiwiZXZlbnQiLCJwYXJlbnRzIiwid2RzSGVyb0Nhcm91c2VsIiwiaGVyb0Nhcm91c2VsIiwiZG9TbGljayIsImRvRmlyc3RBbmltYXRpb24iLCJmaXJzdFNsaWRlIiwiZmlyc3RTbGlkZUNvbnRlbnQiLCJmaXJzdEFuaW1hdGlvbiIsImRvQW5pbWF0aW9uIiwic2xpZGVzIiwiYWN0aXZlU2xpZGUiLCJhY3RpdmVDb250ZW50IiwiYW5pbWF0aW9uQ2xhc3MiLCJzcGxpdEFuaW1hdGlvbiIsInNwbGl0IiwiYW5pbWF0aW9uVHJpZ2dlciIsInNsaWRlQ29udGVudCIsImxhc3RDbGFzcyIsInBvcCIsInBsYXlCYWNrZ3JvdW5kVmlkZW9zIiwicGxheSIsInNsaWNrIiwiYXV0b3BsYXkiLCJhdXRvcGxheVNwZWVkIiwiYXJyb3dzIiwiZG90cyIsImZvY3VzT25TZWxlY3QiLCJ3YWl0Rm9yQW5pbWF0ZSIsImNsYXNzTmFtZSIsInJlcGxhY2UiLCJpZHgiLCJhZnRlciIsInNsaWRlVG9nZ2xlIiwiYWRkT3JSZW1vdmVPd25MaWtlIiwiUkVEQ09MQUIiLCJ1c2VyX2xvZ2dlZF9pbiIsImlzTGlrZWQiLCJwb3N0SWQiLCJkYXRhIiwidXNlcl91c2VybmFtZSIsInJlbW92ZSIsIm5ld19saWtlIiwidXNlcl9wcm9maWxlX3VybCIsInByZXBlbmQiLCJiaW5kIiwic2V0VGltZW91dCIsIm9mZiIsInVuYmluZCIsIldEU19tZWRpYVByZXNzX09iamVjdCIsIm1lZGlhUHJlc3NTZWxlY3RvciIsIm1lZGlhVXBsb2FkZXIiLCJkb01lZGlhUHJlc3MiLCJhY3RpdmUiLCJoaWRlIiwid2RzTW9iaWxlTWVudSIsInN1Yk1lbnVDb250YWluZXIiLCJzdWJTdWJNZW51Q29udGFpbmVyIiwic3ViTWVudVBhcmVudEl0ZW0iLCJvZmZDYW52YXNDb250YWluZXIiLCJhZGREb3duQXJyb3ciLCJ0b2dnbGVTdWJtZW51IiwicmVzZXRTdWJNZW51IiwiZm9yY2VDbG9zZVN1Ym1lbnVzIiwiaXMiLCJzbGlkZU91dFN1Yk1lbnVzIiwiZWwiLCJzdWJNZW51IiwiY2hpbGRyZW4iLCJvcGVuU3VibWVudSIsIndkc01vZGFsIiwiJG1vZGFsVG9nZ2xlIiwiJGZvY3VzYWJsZUNoaWxkcmVuIiwiJHBsYXllciIsIiR0YWciLCJjcmVhdGVFbGVtZW50IiwiJGZpcnN0U2NyaXB0VGFnIiwiZ2V0RWxlbWVudHNCeVRhZ05hbWUiLCJZVCIsInBhcmVudE5vZGUiLCJpbnNlcnRCZWZvcmUiLCJvcGVuTW9kYWwiLCJjbG9zZU1vZGFsIiwiZXNjS2V5Q2xvc2UiLCJjbG9zZU1vZGFsQnlDbGljayIsInRyYXBLZXlib2FyZE1heWJlIiwiJG1vZGFsIiwiJGlmcmFtZSIsInVybCIsImluY2x1ZGVzIiwic3RvcFZpZGVvIiwia2V5Q29kZSIsIndoaWNoIiwiJGZvY3VzZWQiLCJmb2N1c0luZGV4IiwiaW5kZXgiLCJzaGlmdEtleSIsIm9uWW91VHViZUlmcmFtZUFQSVJlYWR5IiwiJGlmcmFtZWlkIiwiUGxheWVyIiwiZXZlbnRzIiwib25QbGF5ZXJSZWFkeSIsIm9uUGxheWVyU3RhdGVDaGFuZ2UiLCJhIiwiZmlyc3QiLCJ3ZHNQcmltYXJ5TmF2aWdhdGlvbiIsInNpdGVIZWFkZXIiLCJ0b2dnbGVGb2N1cyIsInNjcm9sbEhlbHBlciIsImFwcGVuZCIsIndpZHRoIiwid2Rzb2ZmQ2FudmFzIiwib2ZmQ2FudmFzQ2xvc2UiLCJvZmZDYW52YXNPcGVuIiwib2ZmQ2FudmFzU2NyZWVuIiwiY2xvc2VvZmZDYW52YXMiLCJ0b2dnbGVvZmZDYW52YXMiLCJvcGVub2ZmQ2FudmFzIiwiV0RTT3BlblNpZGViYXJPYmplY3QiLCJvcGVuU2lkZWJhckNvbnRhaW5lciIsIm9wZW5TaWRlYmFyU2VsZWN0b3IiLCJkb0Nsb3NlU2lkZWJhciIsImhhcyIsIldEU1Byb2ZpbGVCYXJPYmplY3QiLCJwcm9maWxlQmFyU2VsZWN0b3IiLCJjaGVja0JveCIsImRvUHJvZmlsZU1lbnUiLCJpc1dlYmtpdCIsIm5hdmlnYXRvciIsInVzZXJBZ2VudCIsInRvTG93ZXJDYXNlIiwiaXNPcGVyYSIsImlzSWUiLCJnZXRFbGVtZW50QnlJZCIsImFkZEV2ZW50TGlzdGVuZXIiLCJpZCIsImhhc2giLCJzdWJzdHJpbmciLCJlbGVtZW50IiwidGVzdCIsInRhZ05hbWUiLCJ0YWJJbmRleCIsIndkc1dpbmRvd1JlYWR5IiwibG9hZCIsImFkZEJvZHlDbGFzcyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7OztBQU9BO0FBQ0EsSUFBSyxDQUFFQSxPQUFPQyxjQUFQLENBQXVCLG1CQUF2QixDQUFQLEVBQXNEOztBQUVyRDs7Ozs7O0FBTUFELFFBQU9FLGlCQUFQLEdBQTZCLFVBQVVDLENBQVYsRUFBYUMsR0FBYixFQUFtQjs7QUFFL0M7Ozs7Ozs7O0FBUUEsV0FBU0MsS0FBVCxDQUFnQkMsQ0FBaEIsRUFBb0I7O0FBRW5CO0FBQ0EsT0FBTUMsVUFBVUosRUFBR0csRUFBRUUsTUFBTCxFQUFjQyxPQUFkLENBQXVCLDJCQUF2QixDQUFoQjs7QUFFQTtBQUNBLE9BQU1DLFFBQVFILFFBQVFJLFFBQVIsQ0FBa0IsbUJBQWxCLENBQWQ7O0FBRUE7QUFDQUQsU0FBTUUsVUFBTjtBQUNBOztBQUVEO0FBQ0FULElBQUcsTUFBSCxFQUFZVSxFQUFaLENBQWdCLHlCQUFoQixFQUEyQywyQkFBM0MsRUFBd0VSLEtBQXhFOztBQUVBLFNBQU9ELEdBQVAsQ0F6QitDLENBeUJuQztBQUNaLEVBMUI0QixDQTBCekJVLE1BMUJ5QixFQTBCakIsRUExQmlCLENBQTdCO0FBMkJBLEVBQUM7OztBQzNDRjtBQUNBLElBQUssQ0FBRWQsT0FBT0MsY0FBUCxDQUF1QixpQkFBdkIsQ0FBUCxFQUFvRDs7QUFFbkQ7OztBQUdBRCxRQUFPZSxlQUFQLEdBQTJCLFVBQVVaLENBQVYsRUFBYUMsR0FBYixFQUFtQjtBQUM3Q0QsSUFBR2EsUUFBSCxFQUFjQyxLQUFkLENBQXFCLFlBQVc7QUFDL0IsT0FBSUMsUUFBUWYsRUFBRyxNQUFILENBQVo7QUFDQSxPQUFJZ0IsWUFBWWhCLEVBQUcsK0JBQUgsQ0FBaEI7QUFDQSxPQUFJaUIsWUFBWWpCLEVBQUcsWUFBSCxDQUFoQjtBQUNBLE9BQUlrQixnQkFBZ0JsQixFQUFHLGlCQUFILENBQXBCO0FBQ0EsT0FB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QUFDN0J6TSxNQUFJSyxFQUFKLENBQU9DLElBQVAsQ0FBWW5CLFFBQVosQ0FBc0IsT0FBdEI7QUFDQSxFQUZEOztBQUlBO0FBQ0ExQixHQUFHdUMsSUFBSUMsSUFBUDtBQUNBLENBNUJDLEVBNEJDM0MsTUE1QkQsRUE0QlNjLE1BNUJULEVBNEJpQmQsT0FBT2lQLGNBNUJ4QixDQUFGIiwiZmlsZSI6InByb2plY3QuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIFsuLi5dIEFjdGl2aXR5IE1lbnUuXG4gKlxuICogQHNpbmNlICAgTkVYVFxuICogQHBhY2thZ2UgUmVkIENvbGFib3JhclxuICovXG5cbi8qIGdsb2JhbHMgalF1ZXJ5ICovXG5pZiAoICEgd2luZG93Lmhhc093blByb3BlcnR5KCAnd2RzQlBBY3Rpdml0eU1lbnUnICkgKSB7XG5cblx0LyoqXG5cdCAqIEFjdGl2aXR5IE1lbnUuXG5cdCAqXG5cdCAqIEBzaW5jZSAgICAgICAgICAgTkVYVFxuXHQgKiBAcmV0dXJuIHtPYmplY3R9IFB1YmxpYyBvYmplY3QuXG5cdCAqL1xuXHR3aW5kb3cud2RzQlBBY3Rpdml0eU1lbnUgPSAoIGZ1bmN0aW9uKCAkLCBwdWIgKSB7XG5cblx0XHQvKipcblx0XHQgKiBDbGlja2luZyB0aGUgWy4uLl0gYnV0dG9uLlxuXHRcdCAqXG5cdFx0ICogQGF1dGhvciBBdWJyZXkgUG9ydHdvb2Rcblx0XHQgKiBAc2luY2UgIE5FWFRcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSAge09iamVjdH0gZSBFdmVudCBvYmplY3QuXG5cdFx0ICovXG5cdFx0ZnVuY3Rpb24gY2xpY2soIGUgKSB7XG5cblx0XHRcdC8vIEdldCB0aGUgYnV0dG9uLlxuXHRcdFx0Y29uc3QgJHRhcmdldCA9ICQoIGUudGFyZ2V0ICkuY2xvc2VzdCggJy5icC1hY3Rpdml0eS1tZW51LXRyaWdnZXInICk7XG5cblx0XHRcdC8vIEZpbmQgdGhlIG1lbnUgbmVhciB0aGUgYnV0dG9uLlxuXHRcdFx0Y29uc3QgJG1lbnUgPSAkdGFyZ2V0LnNpYmxpbmdzKCAnLmJwLWFjdGl2aXR5LW1lbnUnICk7XG5cblx0XHRcdC8vIFRvZ2dsZSB0aGUgbWVudS5cblx0XHRcdCRtZW51LmZhZGVUb2dnbGUoKTtcblx0XHR9XG5cblx0XHQvLyBEZWxlZ2F0ZSBhbnkgY2xpY2tzIG9uIGJvZHkgLmJwLWFjdGl2aXR5LW1lbnUtdHJpZ2dlci5cblx0XHQkKCAnYm9keScgKS5vbiggJ2NsaWNrLndkc0JQQWN0aXZpdHlNZW51JywgJy5icC1hY3Rpdml0eS1tZW51LXRyaWdnZXInLCBjbGljayApO1xuXG5cdFx0cmV0dXJuIHB1YjsgLy8gUmV0dXJuIHB1YmxpYyB0aGluZ3MuXG5cdH0gKCBqUXVlcnksIHt9ICkgKTtcbn0gLy8gRW5kIGlmKCkuXG4iLCIvKiBnbG9iYWxzIGNvbnNvbGUsIGpRdWVyeSAqL1xuaWYgKCAhIHdpbmRvdy5oYXNPd25Qcm9wZXJ0eSggJ3dkc0FjdGl2aXR5UG9zdCcgKSApIHtcblxuXHQvKipcblx0ICogQWN0aXZpdHkgcG9zdC5cblx0ICovXG5cdHdpbmRvdy53ZHNBY3Rpdml0eVBvc3QgPSAoIGZ1bmN0aW9uKCAkLCBwdWIgKSB7XG5cdFx0JCggZG9jdW1lbnQgKS5yZWFkeSggZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJGJvZHkgPSAkKCAnYm9keScgKTtcblx0XHRcdHZhciAkZHJvcFpvbmUgPSAkKCAnI21wcC11cGxvYWQtZHJvcHpvbmUtYWN0aXZpdHknICk7XG5cdFx0XHR2YXIgJHdoYXRzTmV3ID0gJCggJyN3aGF0cy1uZXcnICk7XG5cdFx0XHR2YXIgJHVwbG9hZEJ1dHRvbiA9ICQoICcjbXBwLWFsbC11cGxvYWQnICk7XG5cdFx0XHR2YXIgJHNlbGVjdEZpbGUgPSAkKCAnI21wcC11cGxvYWQtbWVkaWEtYnV0dG9uLWFjdGl2aXR5JyApO1xuXG5cdFx0XHQvLyBBcmUgd2UgZGlzYWJsaW5nIHRoaXMgSlMgdGVtcG9yYXJpbHkgdmlhID9kaXNhYmxlV2RzQWN0aXZpdHlQb3N0LlxuXHRcdFx0dmFyIGRpc2FibGVkID0gKCAxID09PSB3aW5kb3cubG9jYXRpb24uc2VhcmNoLmluZGV4T2YoICdkaXNhYmxlV2RzQWN0aXZpdHlQb3N0JyApICk7XG5cdFx0XHRpZiAoIGRpc2FibGVkICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGlmICggd2luZG93LmlubmVyV2lkdGggPCA5MDAgKSB7XG5cblx0XHRcdFx0Ly8gQmFpbCBvbiBtb2JpbGUuXG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblxuXHRcdFx0Ly8gUmVxdWlyZW1lbnRzLlxuXHRcdFx0aWYgKCAkd2hhdHNOZXcubGVuZ3RoICYmICRkcm9wWm9uZS5sZW5ndGggJiYgJHVwbG9hZEJ1dHRvbi5sZW5ndGggKSB7XG5cblx0XHRcdFx0Ly8gVGVsbCBTQVNTIHRvIGRvIGl0J3MgdGhpbmchXG5cdFx0XHRcdCRib2R5LmFkZENsYXNzKCAnYWN0aXZpdHktcG9zdC1qcy1yZWFkeScgKTtcblxuXHRcdFx0XHQvLyBNYWtlIHN1cmUgdGhlIGRyb3B6b25lIGlzIGFjdGl2YXRlZCB0aGUgZmlyc3QgdGltZSFcblx0XHRcdFx0JHVwbG9hZEJ1dHRvbi5jbGljaygpO1xuXG5cdFx0XHRcdC8vIFdoZW4gdGhlIHVwbG9hZCBjYW1lcmEgaWNvbiBpcyBjbGlja2VkIGZyb20gaGVyZSBvbi4uLlxuXHRcdFx0XHQkdXBsb2FkQnV0dG9uLm9uKCAnY2xpY2snLCBmdW5jdGlvbigpIHtcblxuXHRcdFx0XHRcdC8vIENsaWNrIHRoZSBcIlNlbGVjdCBGaWxlXCIgYnV0dG9uIGluIHRoZSBkcm9wem9uZSB0aGF0J3MgaGlkZGVuLlxuXHRcdFx0XHRcdCRzZWxlY3RGaWxlLmNsaWNrKCk7XG5cdFx0XHRcdH0gKTtcblx0XHRcdH0gZWxzZSB7XG5cblx0XHRcdFx0Ly8gQmFpbCwgdGhlIHJlcXVpcmVkIGVsZW1lbnRzIHdlIG5lZWQgYXJlbid0IHRoZXJlLlxuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdC8qKlxuXHRcdFx0ICogQWN0aXZhdGUgdGhlIGRyb3B6b25lIHBsYWNlbWVudC5cblx0XHRcdCAqXG5cdFx0XHQgKiBUaGlzIHBsYWNlcyB0aGUgZHJvcHpvbmUgb3ZlciB0aGUgcG9zdGluZyBhcmVhIHdoZW4gZmlsZXNcblx0XHRcdCAqIGFyZSBkcmFnZ2VkIG92ZXIgaXQuXG5cdFx0XHQgKlxuXHRcdFx0ICogQGF1dGhvciBBdWJyZXkgUG9ydHdvb2Rcblx0XHRcdCAqIEBzaW5jZSAgRnJpZGF5LCAxMSAyNCwgMjAxN1xuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSAge09iamVjdH0gZSBFdmVudCBvYmplY3QuXG5cdFx0XHQgKi9cblx0XHRcdGZ1bmN0aW9uIGFjdGl2YXRlRHJvcHpvbmVQbGFjZW1lbnQoIGUgKSB7XG5cdFx0XHRcdHZhciBkdCA9IGUub3JpZ2luYWxFdmVudC5kYXRhVHJhbnNmZXI7XG5cdFx0XHRcdHZhciBmaWxlcyA9IGR0LnR5cGVzICYmICggZHQudHlwZXMuaW5kZXhPZiA/IC0xICE9PSBkdC50eXBlcy5pbmRleE9mKCAnRmlsZXMnICkgOiBkdC50eXBlcy5jb250YWlucyggJ0ZpbGVzJyApICk7XG5cblx0XHRcdFx0aWYgKCBmaWxlcyAmJiAkZHJvcFpvbmUubGVuZ3RoICkge1xuXHRcdFx0XHRcdCRkcm9wWm9uZS5hZGRDbGFzcyggJ2FjdGl2ZScgKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIERlYWN0aXZhdGUgdGhlIGRyb3B6b25lIHBsYWNlbWVudC5cblx0XHRcdCAqXG5cdFx0XHQgKiBUaGlzIG1ha2VzIHRoZSBkcm9wem9uZSBwbGFjZW1lbnQgdG90YWxseSBoaWRkZW4uXG5cdFx0XHQgKlxuXHRcdFx0ICogQGF1dGhvciBBdWJyZXkgUG9ydHdvb2Rcblx0XHRcdCAqIEBzaW5jZSAgRnJpZGF5LCAxMSAyNCwgMjAxN1xuXHRcdFx0ICovXG5cdFx0XHRmdW5jdGlvbiBkZWFjdGl2YXRlRHJvcHpvbmVQbGFjZW1lbnQoKSB7XG5cdFx0XHRcdCRkcm9wWm9uZS5yZW1vdmVDbGFzcyggJ2FjdGl2ZScgKTtcblx0XHRcdH1cblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGb2N1cyBvbiB0aGUgcG9zdCB0ZXh0YXJlYS5cblx0XHRcdCAqXG5cdFx0XHQgKiBUaGlzIHNldHMgdGhlIGN1cnNvciBpbiB0aGUgcG9zdGluZyB0ZXh0YXJlYVxuXHRcdFx0ICogYW5kIHNob3VsZCBhY3RpdmF0ZSB0aGUgb3RoZXIgaXRlbXMsIGV0Yy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAYXV0aG9yIEF1YnJleSBQb3J0d29vZFxuXHRcdFx0ICogQHNpbmNlICBGcmlkYXksIDExIDI0LCAyMDE3XG5cdFx0XHQgKi9cblx0XHRcdGZ1bmN0aW9uIGZvY3VzT25Qb3N0KCkge1xuXHRcdFx0XHQkd2hhdHNOZXcuZm9jdXMoKTtcblx0XHRcdH1cblxuXHRcdFx0Ly8gV2hlbiBJIGRyYWcgc29tZXRoaW5nIG92ZXIgdGhlIHBvc3QgdGV4dGFyZWEgYWN0aXZhdGUgdGhlIGRyb3B6b25lIG92ZXIgaXQuXG5cdFx0XHQkd2hhdHNOZXcub24oICdkcmFnb3ZlcicsIGFjdGl2YXRlRHJvcHpvbmVQbGFjZW1lbnQgKTtcblxuXHRcdFx0Ly8gRm9jdXMgb24gdGhlIHBvc3QgdGV4dGFyZWEgd2hlbiBzb21ldGhpbmcgaXMgZHJvcHBlZCBvbiB0aGUgZHJvcHpvbmUuXG5cdFx0XHQkZHJvcFpvbmUub24oICdkcm9wLnBvc3QnLCBmb2N1c09uUG9zdCApO1xuXG5cdFx0XHQvLyBEZWFjdGl2YXRlIHRoZSBkcm9wem9uZSBwbGFjZW1lbnQgd2hlbiBzb21ldGhpbmcgaXMgZHJvcHBlZCBvbiB0aGUgZHJvcHpvbmUuXG5cdFx0XHQkZHJvcFpvbmUub24oICdkcm9wLmRlYWN0aXZhdGVEcm9wem9uZVBsYWNlbWVudCcsIGRlYWN0aXZhdGVEcm9wem9uZVBsYWNlbWVudCApO1xuXG5cdFx0XHQvLyBEZWFjdGl2YXRlIHRoZSBkcm9wem9uZSBwbGFjZW1lbnQgd2hlbiBzb21ldGhpbmcgbGVhdmVzIHRoZSB3aW5kb3cuXG5cdFx0XHQkKCB3aW5kb3cgKS5vbiggJ21vdXNlbGVhdmUnLCBkZWFjdGl2YXRlRHJvcHpvbmVQbGFjZW1lbnQgKTtcblxuXHRcdH0gKTsgcmV0dXJuIHB1YjsgLy8gUmV0dXJuIHB1YmxpYyB0aGluZ3MuXG5cdH0gKCBqUXVlcnksIHt9ICkgKTtcbn0gLy8gRW5kIGlmKCkuXG4iLCIvKipcbiAqIEJhY2sgdG8gVG9wIEJ1dHRvbiBTY3JpcHQuXG4gKi9cbndpbmRvdy53ZHNCYWNrVG9Ub3AgPSB7fTtcbiggZnVuY3Rpb24oIHdpbmRvdywgJCwgYXBwICkge1xuXG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0aWYgKCBhcHAubWVldHNSZXF1aXJlbWVudHMoKSApIHtcblx0XHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHRib2R5OiAkKCAnaHRtbCwgYm9keScgKSxcblx0XHRcdHdpbmRvdzogJCggd2luZG93ICksXG5cdFx0XHRiYWNrVG9Ub3BTZWxlY3RvcjogJCggJy5iYWNrLXRvLXRvcC1idXR0b24nIClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMud2luZG93Lm9uKCAnc2Nyb2xsJywgYXBwLmRvQmFja1RvVG9wQnV0dG9uICk7XG5cdFx0YXBwLiRjLmJhY2tUb1RvcFNlbGVjdG9yLm9uKCAnY2xpY2snLCBhcHAuZG9CYWNrVG9Ub3AgKTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMuYmFja1RvVG9wU2VsZWN0b3IubGVuZ3RoO1xuXHR9O1xuXG5cdC8vIFNob3cvSGlkZSBCYWNrIHRvIFRvcCBCdXR0b24uXG5cdGFwcC5kb0JhY2tUb1RvcEJ1dHRvbiA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0bGV0IHNjcm9sbCA9IGFwcC4kYy53aW5kb3cuc2Nyb2xsVG9wKCk7XG5cblx0XHRpZiAoIDI1MCA8IHNjcm9sbCApIHtcblx0XHRcdGFwcC4kYy5iYWNrVG9Ub3BTZWxlY3Rvci5mYWRlSW4oIDIwMCApO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHRhcHAuJGMuYmFja1RvVG9wU2VsZWN0b3IuZmFkZU91dCggMjAwICk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIFNjcm9sbCBiYWNrIHRvIHRvcCBvbiBjbGljay5cblx0YXBwLmRvQmFja1RvVG9wID0gZnVuY3Rpb24oIGUgKSB7XG5cblx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cblx0XHRhcHAuJGMuYm9keS5hbmltYXRlKCB7IHNjcm9sbFRvcDogMCB9LCA3MDAgKTtcblx0fTtcblxuXHQvLyBFbmdhZ2UhXG5cdCQoIGFwcC5pbml0ICk7XG5cbn0gKCB3aW5kb3csIGpRdWVyeSwgd2luZG93Lndkc0JhY2tUb1RvcCApICk7XG4iLCIvKipcbiAqIEhpZGUgdGhlIEJ1ZGR5UHJlc3MgQ29tbWVudHMgYWZ0ZXIgdGhleSBoYXZlIGJlZW4gZGlzcGxheWVkLlxuICpcbiAqIEBhdXRob3IgQ29yZXkgQ29sbGluc1xuICovXG53aW5kb3cuU2hvd0hpZGVCUENvbW1lbnRzID0ge307XG4oIGZ1bmN0aW9uKCB3aW5kb3csICQsIGFwcCApIHtcblxuXHQvLyBDb25zdHJ1Y3RvclxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0aWYgKCBhcHAubWVldHNSZXF1aXJlbWVudHMoKSApIHtcblx0XHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzXG5cdGFwcC5jYWNoZSA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYyA9IHtcblx0XHRcdHdpbmRvdzogJCggd2luZG93ICksXG5cdFx0XHRjb21tZW50c0NvbnRhaW5lcjogJCggJy5hY3Rpdml0eS1jb21tZW50cycgKSxcblx0XHRcdHRyaWdnZXI6ICQoICcuc2hvdy1oaWRlLWNvbW1lbnRzLXRyaWdnZXInICksXG5cdFx0XHRicFNob3dDb21tZW50c0xpbms6ICQoICdsaS5zaG93LWFsbCA+IGEnIClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50c1xuXHRhcHAuYmluZEV2ZW50cyA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0Ly8gTGlzdGVuIGZvciBhIGNsaWNrIG9uIG91ciB0cmlnZ2VyLlxuXHRcdGFwcC4kYy50cmlnZ2VyLm9uKCAnY2xpY2snLCBhcHAuc2hvd0hpZGVDb21tZW50cyApO1xuXG5cdFx0Ly8gTGlzdGVuIGZvciBhIGNsaWNrIG9uIEJQJ3MgdHJpZ2dlci5cblx0XHRhcHAuJGMuYnBTaG93Q29tbWVudHNMaW5rLm9uKCAnY2xpY2snLCBhcHAuYnBTaG93SGlkZUNvbW1lbnRzICk7XG5cdH07XG5cblx0Ly8gRG8gd2UgbWVldCB0aGUgcmVxdWlyZW1lbnRzP1xuXHRhcHAubWVldHNSZXF1aXJlbWVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRyZXR1cm4gYXBwLiRjLmNvbW1lbnRzQ29udGFpbmVyLmxlbmd0aDtcblx0fTtcblxuXHQvLyBTaG93L0hpZGUgdGhlIGNvbW1lbnRzIG9uIGNsaWNrLlxuXHRhcHAuYnBTaG93SGlkZUNvbW1lbnRzID0gZnVuY3Rpb24oKSB7XG5cblx0XHRsZXQgcGFyZW50VUwgPSAkKCB0aGlzICkuY2xvc2VzdCggJ3VsJyApLFxuXHRcdFx0c2hvd0hpZGVUcmlnZ2VyID0gcGFyZW50VUwuc2libGluZ3MoICcuc2hvdy1oaWRlLWNvbW1lbnRzLXRyaWdnZXInICk7XG5cblx0XHQvLyBNYWtlIG91ciBoaWRlL3Nob3cgYnV0dG9uIHZpc2libGUuXG5cdFx0cGFyZW50VUwuYWRkQ2xhc3MoICdicC12aXNpYmxlJyApO1xuXHRcdHBhcmVudFVMLnNpYmxpbmdzKCAnLnNob3ctaGlkZS1jb21tZW50cy10cmlnZ2VyJyApLmFkZENsYXNzKCAnaXMtdmlzaWJsZScgKTtcblxuXHRcdC8vIExvb3AgdGhyb3VnaCBhbGwgb2YgdGhlIHNpYmxpbmcgTElzLlxuXHRcdCQoIHRoaXMgKS5wYXJlbnQoICdsaScgKS5zaWJsaW5ncyggJ2xpJyApLmVhY2goIGZ1bmN0aW9uKCkge1xuXG5cdFx0XHQvLyBJZiBhbiBMSSBpcyBzZXQgdG8gZGlzcGxheTogbm9uZSwgZ2l2ZSBpdCBhIGNsYXNzIHdlIGNhbiB1c2UuXG5cdFx0XHRpZiAoICdub25lJyA9PT0gJCggdGhpcyApLmNzcyggJ2Rpc3BsYXknICkgKSB7XG5cdFx0XHRcdCQoIHRoaXMgKS5hZGRDbGFzcyggJ2NvbW1lbnQtdG8taGlkZScgKTsgLy8gR2l2ZSBpdCBhIGNsYXNzIHNvIHdlIGNhbiBkbyB0aGluZ3Mgd2l0aCBDU1MuXG5cdFx0XHRcdCQoIHRoaXMgKS5yZW1vdmVBdHRyKCAnc3R5bGUnICk7IC8vIFJlbW92ZSB0aGUgaW5saW5lIHN0eWxlcy5cblx0XHRcdH1cblx0XHR9ICk7XG5cblx0XHQvLyBDb3VudCB0aGUgbnVtYmVyIG9mIExJcywgbm93IHRoYXQgdGhlIFNob3cgQWxsIGxpbmsgaGFzIGJlZW4gcmVtb3ZlZC5cblx0XHRjb25zdCBjb21tZW50Q291bnQgPSAkKCB0aGlzICkuY2xvc2VzdCggJy5hY3Rpdml0eS1jb21tZW50cycgKS5maW5kKCAnbGknICkubGVuZ3RoIC0gMTtcblxuXHRcdC8vIEFkZCB0aGUgY29tbWVudCBjb3VudCB0byBvdXIgdHJpZ2dlciBkYXRhIGF0dHJpYnV0ZS5cblx0XHRzaG93SGlkZVRyaWdnZXIuYXR0ciggJ2RhdGEtY29tbWVudC1jb3VudCcsIGNvbW1lbnRDb3VudCApO1xuXG5cdFx0Ly8gQXBwZW5kIG91ciBudW1iZXIgdG8gb3VyIGxpbmsuXG5cdFx0c2hvd0hpZGVUcmlnZ2VyLmF0dHIoICdkYXRhLXNob3ctdGV4dCcsIHNob3dIaWRlVHJpZ2dlci5hdHRyKCAnZGF0YS1zaG93LXRleHQnICkgKyAnICgnICsgY29tbWVudENvdW50ICsgJyknICk7XG5cdH07XG5cblx0Ly8gU2hvdy9IaWRlIHRoZSBjb21tZW50cyBvbiBjbGljay5cblx0YXBwLnNob3dIaWRlQ29tbWVudHMgPSBmdW5jdGlvbigpIHtcblxuXHRcdGxldCB0cmlnZ2VyID0gJCggdGhpcyApO1xuXG5cdFx0Ly8gTG9vcCB0aHJvdWdoIGFsbCBvZiB0aGUgc2libGluZyBMSXMuXG5cdFx0dHJpZ2dlci5zaWJsaW5ncyggJ3VsJyApLnRvZ2dsZUNsYXNzKCAnaGlkZS1leHRyYS1jb21tZW50cycgKTtcblxuXHRcdC8vIElmIGNvbW1lbnRzIGFyZSBoaWRkZW4sIGNoYW5nZSB0aGUgdHJpZ2dlciB0ZXh0LlxuXHRcdGlmICggdHJpZ2dlci5zaWJsaW5ncyggJ3VsJyApLmhhc0NsYXNzKCAnaGlkZS1leHRyYS1jb21tZW50cycgKSApIHtcblx0XHRcdHRyaWdnZXIudGV4dCggdHJpZ2dlci5hdHRyKCAnZGF0YS1zaG93LXRleHQnICkgKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0dHJpZ2dlci50ZXh0KCB0cmlnZ2VyLmF0dHIoICdkYXRhLWhpZGUtdGV4dCcgKSApO1xuXHRcdH1cblx0fTtcblxuXHQvLyBFbmdhZ2Vcblx0JCggYXBwLmluaXQgKTtcblxufSggd2luZG93LCBqUXVlcnksIHdpbmRvdy5TaG93SGlkZUJQQ29tbWVudHMgKSApO1xuIiwiLyoqXG4gKiBEYXRlcGlja2VyIElFMTEgU2NyaXB0LlxuICovXG53aW5kb3cuZGF0ZVBpY2tlck9iamVjdCA9IHt9O1xuKCBmdW5jdGlvbiggd2luZG93LCAkLCBhcHAgKSB7XG5cblx0Ly8gQ29uc3RydWN0b3IuXG5cdGFwcC5pbml0ID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLmNhY2hlKCk7XG5cblx0XHRpZiAoIGFwcC5tZWV0c1JlcXVpcmVtZW50cygpICkge1xuXHRcdFx0YXBwLmRvRGF0ZVBpY2tlcigpO1xuXHRcdH1cblx0fTtcblxuXHQvLyBDYWNoZSBhbGwgdGhlIHRoaW5ncy5cblx0YXBwLmNhY2hlID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjID0ge1xuXHRcdFx0d2luZG93OiAkKCB3aW5kb3cgKSxcblx0XHRcdGJvZHk6ICQoICdib2R5JyApLFxuXHRcdFx0ZmlsdGVyczogJCggJyN3ZHMtcmVjb2xhYm9yYXItc2lkZWJhci1maWx0ZXJzJyApLFxuXHRcdFx0ZGF0ZUJlZm9yZVBpY2tlcjogJCggJyNkYXRlLWJlZm9yZScgKSxcblx0XHRcdGRhdGVBZnRlclBpY2tlcjogJCggJyNkYXRlLWFmdGVyJyApXG5cdFx0fTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMuZmlsdGVycy5sZW5ndGg7XG5cdH07XG5cblx0Ly8gU29tZSBmdW5jdGlvbi5cblx0YXBwLmRvRGF0ZVBpY2tlciA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0dmFyIGRhdGVGb3JtYXQgPSB7XG5cdFx0XHRkYXRlRm9ybWF0OiAneXktbW0tZGQnXG5cdFx0fTtcblxuXHRcdGFwcC4kYy5kYXRlQmVmb3JlUGlja2VyLmRhdGVwaWNrZXIoIGRhdGVGb3JtYXQgKTtcblxuXHRcdGFwcC4kYy5kYXRlQWZ0ZXJQaWNrZXIuZGF0ZXBpY2tlciggZGF0ZUZvcm1hdCApO1xuXHR9O1xuXG5cdC8vIEVuZ2FnZSFcblx0JCggYXBwLmluaXQgKTtcblxufSAoIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cuZGF0ZVBpY2tlck9iamVjdCApICk7XG4iLCIvKipcbiAqIEZvcmNlIFBvc3QgQ2F0ZWdvcnkgU2NyaXB0LlxuICovXG53aW5kb3cuZm9yY2VQb3N0Q2F0ZWdvcnlPYmplY3QgPSB7fTtcbiggZnVuY3Rpb24oIHdpbmRvdywgJCwgYXBwICkge1xuXG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0aWYgKCBhcHAubWVldHNSZXF1aXJlbWVudHMoKSApIHtcblx0XHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHR3aW5kb3c6ICQoIHdpbmRvdyApLFxuXHRcdFx0d2hhdHNOZXdDb250YWluZXI6ICQoICcjd2hhdHMtbmV3JyApLFxuXHRcdFx0Zm9yY2VQb3N0Q2F0ZWdvcnlTZWxlY3RvcjogJCggJy5lZGl0LWNhdGVnb3J5LWFjdGl2aXR5JyApLCAvLyBzZWxlY3Rcblx0XHRcdHN1Ym1pdEJ1dHRvbjogJCggJyN3aGF0cy1uZXctc3VibWl0IGlucHV0JyApXG5cdFx0fTtcblx0fTtcblxuXHQvLyBDb21iaW5lIGFsbCBldmVudHMuXG5cdGFwcC5iaW5kRXZlbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjLmZvcmNlUG9zdENhdGVnb3J5U2VsZWN0b3Iub24oICdjaGFuZ2UnLCBhcHAuZG9Gb3JjZVBvc3RDYXRlZ29yeSApO1xuXG5cdFx0Ly8gRm9yY2UgaW5wdXQgYnV0dG9uIHRvIHN0YXkgZGlzYWJsZWQgdW50aWwganMgYmVsb3cuXG5cdFx0YXBwLiRjLndoYXRzTmV3Q29udGFpbmVyLmZvY3VzKCBmdW5jdGlvbigpIHtcblx0XHRcdGFwcC4kYy5zdWJtaXRCdXR0b24ucHJvcCggJ2Rpc2FibGVkJywgdHJ1ZSApO1xuXG5cdFx0XHQvLyBSZXRhaW4gZW5hYmxlZCBidXR0b24gaWYgc2VsZWN0ZWRJbmRleCBpcyBhbnl0aGluZyBvdGhlciB0aGFuIGRlZmF1bHQuXG5cdFx0XHRpZiAoIDAgIT09IGFwcC4kYy5mb3JjZVBvc3RDYXRlZ29yeVNlbGVjdG9yWzBdLnNlbGVjdGVkSW5kZXggKSB7XG5cdFx0XHRcdGFwcC4kYy5zdWJtaXRCdXR0b24ucHJvcCggJ2Rpc2FibGVkJywgZmFsc2UgKTtcblx0XHRcdH1cblx0XHR9ICk7XG5cdH07XG5cblx0Ly8gRG8gd2UgbWVldCB0aGUgcmVxdWlyZW1lbnRzP1xuXHRhcHAubWVldHNSZXF1aXJlbWVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRyZXR1cm4gYXBwLiRjLmZvcmNlUG9zdENhdGVnb3J5U2VsZWN0b3IubGVuZ3RoO1xuXHR9O1xuXG5cdC8vIEdldCBTZWxlY3Rpb24gYW5kIFNob3cvSGlkZSBCdXR0b24gaWYgbm8gc2VsZWN0aW9uIG1hZGUuXG5cdGFwcC5kb0ZvcmNlUG9zdENhdGVnb3J5ID0gZnVuY3Rpb24oKSB7XG5cblx0XHRsZXQgc2VsZWN0aW9uID0gdGhpcy5zZWxlY3RlZEluZGV4O1xuXG5cdFx0YXBwLiRjLnN1Ym1pdEJ1dHRvbi5wcm9wKCAnZGlzYWJsZWQnLCB0cnVlICk7XG5cbiAgICAgICAgaWYgKCAwICE9PSBzZWxlY3Rpb24gKSB7XG5cdFx0XHRhcHAuJGMuc3VibWl0QnV0dG9uLnByb3AoICdkaXNhYmxlZCcsIGZhbHNlICk7XG4gICAgICAgIH1cblx0fTtcblxuXHQvLyBFbmdhZ2UhXG5cdCQoIGFwcC5pbml0ICk7XG5cbn0gKCB3aW5kb3csIGpRdWVyeSwgd2luZG93LmZvcmNlUG9zdENhdGVnb3J5T2JqZWN0ICkgKTtcbiIsIi8qKlxuICogRmlsZSBnb29nbGUtdHJhbnNsYXRlLWJ1dHRvbi5qc1xuICpcbiAqIEhpZGUgdGhlIGJ1dHRvbiB3aGVuIHdlIHNjcm9sbCB0byB0aGUgYm90dG9tIG9mIHRoZSBwYWdlIHNvIGl0IGRvZXNuJ3QgaW50ZXJmZXJlIHdpdGggdGhlIGZvb3Rlci4uXG4gKi9cbndpbmRvdy5XRFNHb29nbGVUcmFuc2xhdGVCdXR0b24gPSB7fTtcbiggZnVuY3Rpb24oIHdpbmRvdywgJCwgYXBwICkge1xuXG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0YXBwLmJpbmRFdmVudHMoKTtcblx0fTtcblxuXHQvLyBDYWNoZSBhbGwgdGhlIHRoaW5ncy5cblx0YXBwLmNhY2hlID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjID0ge1xuXHRcdFx0Ym9keTogJCggJ2JvZHknICksXG5cdFx0XHR3aW5kb3c6ICQoIHdpbmRvdyApLFxuXHRcdFx0Z29vZ2xlQ29udGFpbmVyOiAkKCAnLmdvb2ctdGUtZ2FkZ2V0JyApLFxuXHRcdFx0Z29vZ2xlRWxlbWVudDogJCggJyNnb29nbGVfdHJhbnNsYXRlX2VsZW1lbnQnIClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIERvIHdlIG1lZXQgdGhlIHJlcXVpcmVtZW50cz9cblx0YXBwLm1lZXRzUmVxdWlyZW1lbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0cmV0dXJuIGFwcC4kYy5nb29nbGVDb250YWluZXIubGVuZ3RoO1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblxuXHRcdC8vIEhpZGUgY29udGFjdCBidXR0b25zIG9uIHNjcm9sbCB0byBib3R0b20uXG5cdFx0YXBwLiRjLndpbmRvdy5vbiggJ3Njcm9sbCcsIGFwcC5oaWRlQ29udGFpbmVyICk7XG5cdH07XG5cblx0Ly8gSGlkZSBDb250YWN0IEJ1dHRvbnMgaWYgbmVhcmx5IHRoZSBib3R0b20uXG5cdGFwcC5oaWRlQ29udGFpbmVyID0gZnVuY3Rpb24oKSB7XG5cblx0XHRsZXQgc2Nyb2xsVG9wID0gYXBwLiRjLndpbmRvdy5zY3JvbGxUb3AoKSArIDIwMDtcblxuXHRcdGlmICggMjAwIDwgc2Nyb2xsVG9wICkge1xuXHRcdFx0YXBwLiRjLmdvb2dsZUNvbnRhaW5lci5mYWRlT3V0KCk7XG5cdFx0XHRhcHAuJGMuZ29vZ2xlRWxlbWVudC5mYWRlT3V0KCk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGFwcC4kYy5nb29nbGVDb250YWluZXIuZmFkZUluKCk7XG5cdFx0XHRhcHAuJGMuZ29vZ2xlRWxlbWVudC5mYWRlSW4oKTtcblx0XHR9XG5cdH07XG5cblx0JCggYXBwLmluaXQgKTtcbn0oIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cuV0RTR29vZ2xlVHJhbnNsYXRlQnV0dG9uICkgKTtcbiIsIi8qKlxuICogU2hvdy9IaWRlIHRoZSBTZWFyY2ggRm9ybSBpbiB0aGUgaGVhZGVyLlxuICpcbiAqIEBhdXRob3IgQ29yZXkgQ29sbGluc1xuICovXG53aW5kb3cuU2hvd0hpZGVTZWFyY2hGb3JtID0ge307XG4oIGZ1bmN0aW9uKCB3aW5kb3csICQsIGFwcCApIHtcblxuXHQvLyBDb25zdHJ1Y3RvclxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0aWYgKCBhcHAubWVldHNSZXF1aXJlbWVudHMoKSApIHtcblx0XHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzXG5cdGFwcC5jYWNoZSA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYyA9IHtcblx0XHRcdHdpbmRvdzogJCggd2luZG93ICksXG5cdFx0XHRib2R5OiAkKCAnYm9keScgKSxcblx0XHRcdGhlYWRlclNlYXJjaEZvcm06ICQoICcuc2l0ZS1oZWFkZXItYWN0aW9uIC5jdGEtYnV0dG9uJyApXG5cdFx0fTtcblx0fTtcblxuXHQvLyBDb21iaW5lIGFsbCBldmVudHNcblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMuaGVhZGVyU2VhcmNoRm9ybS5vbiggJ2tleXVwIHRvdWNoc3RhcnQgY2xpY2snLCBhcHAuc2hvd0hpZGVTZWFyY2hGb3JtICk7XG5cdFx0YXBwLiRjLmJvZHkub24oICdrZXl1cCB0b3VjaHN0YXJ0IGNsaWNrJywgYXBwLmhpZGVTZWFyY2hGb3JtICk7XG5cdH07XG5cblx0Ly8gRG8gd2UgbWVldCB0aGUgcmVxdWlyZW1lbnRzP1xuXHRhcHAubWVldHNSZXF1aXJlbWVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRyZXR1cm4gYXBwLiRjLmhlYWRlclNlYXJjaEZvcm0ubGVuZ3RoO1xuXHR9O1xuXG5cdC8vIEFkZHMgdGhlIHRvZ2dsZSBjbGFzcyBmb3IgdGhlIHNlYXJjaCBmb3JtLlxuXHRhcHAuc2hvd0hpZGVTZWFyY2hGb3JtID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjLmJvZHkudG9nZ2xlQ2xhc3MoICdzZWFyY2gtZm9ybS12aXNpYmxlJyApO1xuXHR9O1xuXG5cdC8vIEhpZGVzIHRoZSBzZWFyY2ggZm9ybSBpZiB3ZSBjbGljayBvdXRzaWRlIG9mIGl0cyBjb250YWluZXIuXG5cdGFwcC5oaWRlU2VhcmNoRm9ybSA9IGZ1bmN0aW9uKCBldmVudCApIHtcblxuXHRcdGlmICggISAkKCBldmVudC50YXJnZXQgKS5wYXJlbnRzKCAnZGl2JyApLmhhc0NsYXNzKCAnc2l0ZS1oZWFkZXItYWN0aW9uJyApICkge1xuXHRcdFx0YXBwLiRjLmJvZHkucmVtb3ZlQ2xhc3MoICdzZWFyY2gtZm9ybS12aXNpYmxlJyApO1xuXHRcdH1cblx0fTtcblxuXHQvLyBFbmdhZ2Vcblx0JCggYXBwLmluaXQgKTtcblxufSAoIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cuU2hvd0hpZGVTZWFyY2hGb3JtICkgKTtcbiIsIi8qKlxuICogRmlsZSBoZXJvLWNhcm91c2VsLmpzXG4gKlxuICogQ3JlYXRlIGEgY2Fyb3VzZWwgaWYgd2UgaGF2ZSBtb3JlIHRoYW4gb25lIGhlcm8gc2xpZGUuXG4gKi9cbndpbmRvdy53ZHNIZXJvQ2Fyb3VzZWwgPSB7fTtcbiggZnVuY3Rpb24oIHdpbmRvdywgJCwgYXBwICkge1xuXG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0aWYgKCBhcHAubWVldHNSZXF1aXJlbWVudHMoKSApIHtcblx0XHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHR3aW5kb3c6ICQoIHdpbmRvdyApLFxuXHRcdFx0aGVyb0Nhcm91c2VsOiAkKCAnLmNhcm91c2VsJyApXG5cdFx0fTtcblx0fTtcblxuXHQvLyBDb21iaW5lIGFsbCBldmVudHMuXG5cdGFwcC5iaW5kRXZlbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjLndpbmRvdy5vbiggJ2xvYWQnLCBhcHAuZG9TbGljayApO1xuXHRcdGFwcC4kYy53aW5kb3cub24oICdsb2FkJywgYXBwLmRvRmlyc3RBbmltYXRpb24gKTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMuaGVyb0Nhcm91c2VsLmxlbmd0aDtcblx0fTtcblxuXHQvLyBBbmltYXRlIHRoZSBmaXJzdCBzbGlkZSBvbiB3aW5kb3cgbG9hZC5cblx0YXBwLmRvRmlyc3RBbmltYXRpb24gPSBmdW5jdGlvbigpIHtcblxuXHRcdC8vIEdldCB0aGUgZmlyc3Qgc2xpZGUgY29udGVudCBhcmVhIGFuZCBhbmltYXRpb24gYXR0cmlidXRlLlxuXHRcdGxldCBmaXJzdFNsaWRlID0gYXBwLiRjLmhlcm9DYXJvdXNlbC5maW5kKCAnW2RhdGEtc2xpY2staW5kZXg9MF0nICksXG5cdFx0XHRmaXJzdFNsaWRlQ29udGVudCA9IGZpcnN0U2xpZGUuZmluZCggJy5oZXJvLWNvbnRlbnQnICksXG5cdFx0XHRmaXJzdEFuaW1hdGlvbiA9IGZpcnN0U2xpZGVDb250ZW50LmF0dHIoICdkYXRhLWFuaW1hdGlvbicgKTtcblxuXHRcdC8vIEFkZCB0aGUgYW5pbWF0aW9uIGNsYXNzIHRvIHRoZSBmaXJzdCBzbGlkZS5cblx0XHRmaXJzdFNsaWRlQ29udGVudC5hZGRDbGFzcyggZmlyc3RBbmltYXRpb24gKTtcblx0fTtcblxuXHQvLyBBbmltYXRlIHRoZSBzbGlkZSBjb250ZW50LlxuXHRhcHAuZG9BbmltYXRpb24gPSBmdW5jdGlvbigpIHtcblx0XHRsZXQgc2xpZGVzID0gJCggJy5zbGlkZScgKSxcblx0XHRcdGFjdGl2ZVNsaWRlID0gJCggJy5zbGljay1jdXJyZW50JyApLFxuXHRcdFx0YWN0aXZlQ29udGVudCA9IGFjdGl2ZVNsaWRlLmZpbmQoICcuaGVyby1jb250ZW50JyApLFxuXG5cdFx0XHQvLyBUaGlzIGlzIGEgc3RyaW5nIGxpa2Ugc286ICdhbmltYXRlZCBzb21lQ3NzQ2xhc3MnLlxuXHRcdFx0YW5pbWF0aW9uQ2xhc3MgPSBhY3RpdmVDb250ZW50LmF0dHIoICdkYXRhLWFuaW1hdGlvbicgKSxcblx0XHRcdHNwbGl0QW5pbWF0aW9uID0gYW5pbWF0aW9uQ2xhc3Muc3BsaXQoICcgJyApLFxuXG5cdFx0XHQvLyBUaGlzIGlzIHRoZSAnYW5pbWF0ZWQnIGNsYXNzLlxuXHRcdFx0YW5pbWF0aW9uVHJpZ2dlciA9IHNwbGl0QW5pbWF0aW9uWzBdO1xuXG5cdFx0Ly8gR28gdGhyb3VnaCBlYWNoIHNsaWRlIHRvIHNlZSBpZiB3ZSd2ZSBhbHJlYWR5IHNldCBhbmltYXRpb24gY2xhc3Nlcy5cblx0XHRzbGlkZXMuZWFjaCggZnVuY3Rpb24oKSB7XG5cdFx0XHRsZXQgc2xpZGVDb250ZW50ID0gJCggdGhpcyApLmZpbmQoICcuaGVyby1jb250ZW50JyApO1xuXG5cdFx0XHQvLyBJZiB3ZSd2ZSBzZXQgYW5pbWF0aW9uIGNsYXNzZXMgb24gYSBzbGlkZSwgcmVtb3ZlIHRoZW0uXG5cdFx0XHRpZiAoIHNsaWRlQ29udGVudC5oYXNDbGFzcyggJ2FuaW1hdGVkJyApICkge1xuXG5cdFx0XHRcdC8vIEdldCB0aGUgbGFzdCBjbGFzcywgd2hpY2ggaXMgdGhlIGFuaW1hdGUuY3NzIGNsYXNzLlxuXHRcdFx0XHRsZXQgbGFzdENsYXNzID0gc2xpZGVDb250ZW50XG5cdFx0XHRcdFx0LmF0dHIoICdjbGFzcycgKVxuXHRcdFx0XHRcdC5zcGxpdCggJyAnIClcblx0XHRcdFx0XHQucG9wKCk7XG5cblx0XHRcdFx0Ly8gUmVtb3ZlIGJvdGggYW5pbWF0aW9uIGNsYXNzZXMuXG5cdFx0XHRcdHNsaWRlQ29udGVudC5yZW1vdmVDbGFzcyggbGFzdENsYXNzICkucmVtb3ZlQ2xhc3MoIGFuaW1hdGlvblRyaWdnZXIgKTtcblx0XHRcdH1cblx0XHR9ICk7XG5cblx0XHQvLyBBZGQgYW5pbWF0aW9uIGNsYXNzZXMgYWZ0ZXIgc2xpZGUgaXMgaW4gdmlldy5cblx0XHRhY3RpdmVDb250ZW50LmFkZENsYXNzKCBhbmltYXRpb25DbGFzcyApO1xuXHR9O1xuXG5cdC8vIEFsbG93IGJhY2tncm91bmQgdmlkZW9zIHRvIGF1dG9wbGF5LlxuXHRhcHAucGxheUJhY2tncm91bmRWaWRlb3MgPSBmdW5jdGlvbigpIHtcblxuXHRcdC8vIEdldCBhbGwgdGhlIHZpZGVvcyBpbiBvdXIgc2xpZGVzIG9iamVjdC5cblx0XHQkKCAndmlkZW8nICkuZWFjaCggZnVuY3Rpb24oKSB7XG5cblx0XHRcdC8vIExldCB0aGVtIGF1dG9wbGF5LiBUT0RPOiBQb3NzaWJseSBjaGFuZ2UgdGhpcyBsYXRlciB0byBvbmx5IHBsYXkgdGhlIHZpc2libGUgc2xpZGUgdmlkZW8uXG5cdFx0XHR0aGlzLnBsYXkoKTtcblx0XHR9ICk7XG5cdH07XG5cblx0Ly8gS2ljayBvZmYgU2xpY2suXG5cdGFwcC5kb1NsaWNrID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjLmhlcm9DYXJvdXNlbC5vbiggJ2luaXQnLCBhcHAucGxheUJhY2tncm91bmRWaWRlb3MgKTtcblxuXHRcdGFwcC4kYy5oZXJvQ2Fyb3VzZWwuc2xpY2soIHtcblx0XHRcdGF1dG9wbGF5OiB0cnVlLFxuXHRcdFx0YXV0b3BsYXlTcGVlZDogNTAwMCxcblx0XHRcdGFycm93czogZmFsc2UsXG5cdFx0XHRkb3RzOiBmYWxzZSxcblx0XHRcdGZvY3VzT25TZWxlY3Q6IHRydWUsXG5cdFx0XHR3YWl0Rm9yQW5pbWF0ZTogdHJ1ZVxuXHRcdH0gKTtcblxuXHRcdGFwcC4kYy5oZXJvQ2Fyb3VzZWwub24oICdhZnRlckNoYW5nZScsIGFwcC5kb0FuaW1hdGlvbiApO1xuXHR9O1xuXG5cdC8vIEVuZ2FnZSFcblx0JCggYXBwLmluaXQgKTtcbn0gKCB3aW5kb3csIGpRdWVyeSwgd2luZG93Lndkc0hlcm9DYXJvdXNlbCApICk7XG4iLCIvKipcbiAqIEZpbGUganMtZW5hYmxlZC5qc1xuICpcbiAqIElmIEphdmFzY3JpcHQgaXMgZW5hYmxlZCwgcmVwbGFjZSB0aGUgPGJvZHk+IGNsYXNzIFwibm8tanNcIi5cbiAqL1xuZG9jdW1lbnQuYm9keS5jbGFzc05hbWUgPSBkb2N1bWVudC5ib2R5LmNsYXNzTmFtZS5yZXBsYWNlKCAnbm8tanMnLCAnanMnICk7XG4iLCJcbmpRdWVyeSggZG9jdW1lbnQgKS5yZWFkeSggZnVuY3Rpb24oICQgKSB7XG5cblx0JCgnLmFjdGl2aXR5IC5hd3N0X2xpa2VfdXNlcl9saXN0JykuZWFjaCggZnVuY3Rpb24oIGlkeCApIHtcblx0XHQvLyBtb3ZlIGxpc3Qgb2YgcGVvcGxlIHdobyBsaWtlZCBhIHBvc3QgdG8gYWZ0ZXIgdGhlIGxpa2UgYnV0dG9uIGNvbnRhaW5lclxuXHRcdCQodGhpcykucGFyZW50KCkuYWZ0ZXIoICQodGhpcykgKTtcblx0fSk7XG5cblx0JCgnLmF3c3RfbGlrZSAudG90YWxfbGlrZScpLm9uKCAnY2xpY2snLCBmdW5jdGlvbigpe1xuXHRcdCQodGhpcykucGFyZW50KCkucGFyZW50KCkucGFyZW50KCkuZmluZChcIi5hd3N0X2xpa2VfdXNlcl9saXN0XCIpLnNsaWRlVG9nZ2xlKCk7XG5cdH0pO1xuXG5cdHZhciBhZGRPclJlbW92ZU93bkxpa2UgPSBmdW5jdGlvbigpe1xuXG5cdFx0Ly8gY29uc29sZS5sb2coIFJFRENPTEFCICk7XG5cblx0XHRpZiggUkVEQ09MQUIudXNlcl9sb2dnZWRfaW4gPT0gXCIwXCIgKSByZXR1cm47XG5cblx0XHR2YXIgaXNMaWtlZCA9ICQodGhpcykuZmluZCgnaScpLmhhc0NsYXNzKCdmYS10aHVtYnMtdXAnKTtcblx0XHR2YXIgcG9zdElkID0gJCh0aGlzKS5maW5kKCdpJykuZGF0YSgncG9zdC1pZCcpO1xuXG5cdFx0Ly8gY29uc29sZS5sb2coIGlzTGlrZWQgKTtcblx0XHQvLyBjb25zb2xlLmxvZyggcG9zdElkICk7XG5cblx0XHRpZiggaXNMaWtlZCA9PT0gdHJ1ZSApIHtcblxuXHRcdFx0Ly8gY29uc29sZS5sb2coICQoICdsaSNhY3Rpdml0eS0nICsgcG9zdElkICkuZmluZCgnLmF3c3RfbGlrZV91c2VyX2xpc3QgYTpjb250YWlucyhcIkAnICsgUkVEQ09MQUIudXNlcl91c2VybmFtZSArICdcIiknKSApO1xuXG5cdFx0XHQkKCAnbGkjYWN0aXZpdHktJyArIHBvc3RJZCApLmZpbmQoJy5hd3N0X2xpa2VfdXNlcl9saXN0IGE6Y29udGFpbnMoXCJAJyArIFJFRENPTEFCLnVzZXJfdXNlcm5hbWUgKyAnXCIpJykucmVtb3ZlKCk7XG5cblx0XHR9IGVsc2Uge1xuXHRcdFx0Ly9hZGQgdG8gbGlzdFxuXHRcdFx0dmFyIG5ld19saWtlID0gJCgnPGEgaHJlZj1cIicgKyBSRURDT0xBQi51c2VyX3Byb2ZpbGVfdXJsICsgJ1wiPkAnICsgUkVEQ09MQUIudXNlcl91c2VybmFtZSArICc8L2E+Jyk7XG5cblx0XHRcdCQoICdsaSNhY3Rpdml0eS0nICsgcG9zdElkICkuZmluZCgnLmF3c3RfbGlrZV91c2VyX2xpc3QnKS4gcHJlcGVuZChuZXdfbGlrZSk7XG5cblx0XHRcdC8vIGNvbnNvbGUubG9nKCAkKCAnbGkjYWN0aXZpdHktJyArIHBvc3RJZCApLmZpbmQoJy5hd3N0X2xpa2VfdXNlcl9saXN0JykgKTtcblxuXHRcdH1cblxuXHR9O1xuXG5cdCQoJy5hd3N0X2xpa2VfYnRuJykuYmluZCggJ2NsaWNrJywgYWRkT3JSZW1vdmVPd25MaWtlICk7XG5cblx0JCgnLmxvYWQtbW9yZSBhJykuY2xpY2soZnVuY3Rpb24oKSB7XG5cdFx0d2luZG93LnNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCcuYWN0aXZpdHkgLmJwLWFjdGl2aXR5LWNvbnRhaW5lciAuYXdzdF9saWtlX3VzZXJfbGlzdCcpLmVhY2goIGZ1bmN0aW9uKCBpZHggKSB7XG5cdFx0XHRcdC8vIG1vdmUgbGlzdCBvZiBwZW9wbGUgd2hvIGxpa2VkIGEgcG9zdCB0byBhZnRlciB0aGUgbGlrZSBidXR0b24gY29udGFpbmVyXG5cdFx0XHRcdCQodGhpcykucGFyZW50KCkuYWZ0ZXIoICQodGhpcykgKTtcblx0XHRcdH0pO1xuXHRcdFx0JCgnLmF3c3RfbGlrZSAudG90YWxfbGlrZScpLm9mZignY2xpY2snKTtcblx0XHRcdCQoJy5hd3N0X2xpa2UgLnRvdGFsX2xpa2UnKS5vbiggJ2NsaWNrJywgZnVuY3Rpb24oKXtcblx0XHRcdFx0JCh0aGlzKS5wYXJlbnQoKS5wYXJlbnQoKS5wYXJlbnQoKS5maW5kKFwiLmF3c3RfbGlrZV91c2VyX2xpc3RcIikuc2xpZGVUb2dnbGUoKTtcblx0XHRcdH0pO1xuXG5cdFx0XHQkKCcuYXdzdF9saWtlX2J0bicpLnVuYmluZCggJ2NsaWNrJywgYWRkT3JSZW1vdmVPd25MaWtlICk7XG5cdFx0XHQkKCcuYXdzdF9saWtlX2J0bicpLmJpbmQoICdjbGljaycsIGFkZE9yUmVtb3ZlT3duTGlrZSApO1xuXG5cdFx0fSwgMTAwMDApO1xuXHRcdHdpbmRvdy5zZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0JCgnLmFjdGl2aXR5IC5icC1hY3Rpdml0eS1jb250YWluZXIgLmF3c3RfbGlrZV91c2VyX2xpc3QnKS5lYWNoKCBmdW5jdGlvbiggaWR4ICkge1xuXHRcdFx0XHQvLyBtb3ZlIGxpc3Qgb2YgcGVvcGxlIHdobyBsaWtlZCBhIHBvc3QgdG8gYWZ0ZXIgdGhlIGxpa2UgYnV0dG9uIGNvbnRhaW5lclxuXHRcdFx0XHQkKHRoaXMpLnBhcmVudCgpLmFmdGVyKCAkKHRoaXMpICk7XG5cdFx0XHR9KTtcblx0XHRcdCQoJy5hd3N0X2xpa2UgLnRvdGFsX2xpa2UnKS5vZmYoJ2NsaWNrJyk7XG5cdFx0XHQkKCcuYXdzdF9saWtlIC50b3RhbF9saWtlJykub24oICdjbGljaycsIGZ1bmN0aW9uKCl7XG5cdFx0XHRcdCQodGhpcykucGFyZW50KCkucGFyZW50KCkucGFyZW50KCkuZmluZChcIi5hd3N0X2xpa2VfdXNlcl9saXN0XCIpLnNsaWRlVG9nZ2xlKCk7XG5cdFx0XHR9KTtcblxuXHRcdFx0JCgnLmF3c3RfbGlrZV9idG4nKS51bmJpbmQoICdjbGljaycsIGFkZE9yUmVtb3ZlT3duTGlrZSApO1xuXHRcdFx0JCgnLmF3c3RfbGlrZV9idG4nKS5iaW5kKCAnY2xpY2snLCBhZGRPclJlbW92ZU93bkxpa2UgKTtcblxuXHRcdH0sIDIwMDAwKTtcblx0XHR3aW5kb3cuc2V0VGltZW91dChmdW5jdGlvbigpIHtcblx0XHRcdCQoJy5hY3Rpdml0eSAuYnAtYWN0aXZpdHktY29udGFpbmVyIC5hd3N0X2xpa2VfdXNlcl9saXN0JykuZWFjaCggZnVuY3Rpb24oIGlkeCApIHtcblx0XHRcdFx0Ly8gbW92ZSBsaXN0IG9mIHBlb3BsZSB3aG8gbGlrZWQgYSBwb3N0IHRvIGFmdGVyIHRoZSBsaWtlIGJ1dHRvbiBjb250YWluZXJcblx0XHRcdFx0JCh0aGlzKS5wYXJlbnQoKS5hZnRlciggJCh0aGlzKSApO1xuXHRcdFx0fSk7XG5cdFx0XHQkKCcuYXdzdF9saWtlIC50b3RhbF9saWtlJykub2ZmKCdjbGljaycpO1xuXHRcdFx0JCgnLmF3c3RfbGlrZSAudG90YWxfbGlrZScpLm9uKCAnY2xpY2snLCBmdW5jdGlvbigpe1xuXHRcdFx0XHQkKHRoaXMpLnBhcmVudCgpLnBhcmVudCgpLnBhcmVudCgpLmZpbmQoXCIuYXdzdF9saWtlX3VzZXJfbGlzdFwiKS5zbGlkZVRvZ2dsZSgpO1xuXHRcdFx0fSk7XG5cblx0XHRcdCQoJy5hd3N0X2xpa2VfYnRuJykudW5iaW5kKCAnY2xpY2snLCBhZGRPclJlbW92ZU93bkxpa2UgKTtcblx0XHRcdCQoJy5hd3N0X2xpa2VfYnRuJykuYmluZCggJ2NsaWNrJywgYWRkT3JSZW1vdmVPd25MaWtlICk7XG5cblx0XHR9LCAzMDAwMCk7XG5cdFx0d2luZG93LnNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCcuYWN0aXZpdHkgLmJwLWFjdGl2aXR5LWNvbnRhaW5lciAuYXdzdF9saWtlX3VzZXJfbGlzdCcpLmVhY2goIGZ1bmN0aW9uKCBpZHggKSB7XG5cdFx0XHRcdC8vIG1vdmUgbGlzdCBvZiBwZW9wbGUgd2hvIGxpa2VkIGEgcG9zdCB0byBhZnRlciB0aGUgbGlrZSBidXR0b24gY29udGFpbmVyXG5cdFx0XHRcdCQodGhpcykucGFyZW50KCkuYWZ0ZXIoICQodGhpcykgKTtcblx0XHRcdH0pO1xuXHRcdFx0JCgnLmF3c3RfbGlrZSAudG90YWxfbGlrZScpLm9mZignY2xpY2snKTtcblx0XHRcdCQoJy5hd3N0X2xpa2UgLnRvdGFsX2xpa2UnKS5vbiggJ2NsaWNrJywgZnVuY3Rpb24oKXtcblx0XHRcdFx0JCh0aGlzKS5wYXJlbnQoKS5wYXJlbnQoKS5wYXJlbnQoKS5maW5kKFwiLmF3c3RfbGlrZV91c2VyX2xpc3RcIikuc2xpZGVUb2dnbGUoKTtcblx0XHRcdH0pO1xuXG5cdFx0XHQkKCcuYXdzdF9saWtlX2J0bicpLnVuYmluZCggJ2NsaWNrJywgYWRkT3JSZW1vdmVPd25MaWtlICk7XG5cdFx0XHQkKCcuYXdzdF9saWtlX2J0bicpLmJpbmQoICdjbGljaycsIGFkZE9yUmVtb3ZlT3duTGlrZSApO1xuXG5cdFx0fSwgNDAwMDApO1xuXHR9KTtcblxufSk7XG4iLCIvKipcbiAqIE1lZGlhUHJlc3MgVXBsb2FkIFNjcmlwdC5cbiAqL1xud2luZG93LldEU19tZWRpYVByZXNzX09iamVjdCA9IHt9O1xuKCBmdW5jdGlvbiggd2luZG93LCAkLCBhcHAgKSB7XG5cblx0Ly8gQ29uc3RydWN0b3IuXG5cdGFwcC5pbml0ID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLmNhY2hlKCk7XG5cblx0XHRpZiAoIGFwcC5tZWV0c1JlcXVpcmVtZW50cygpICkge1xuXHRcdFx0YXBwLmJpbmRFdmVudHMoKTtcblx0XHR9XG5cdH07XG5cblx0Ly8gQ2FjaGUgYWxsIHRoZSB0aGluZ3MuXG5cdGFwcC5jYWNoZSA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYyA9IHtcblx0XHRcdHdpbmRvdzogJCh3aW5kb3cpLFxuXHRcdFx0bWVkaWFQcmVzc1NlbGVjdG9yOiAkKCAnI3doYXRzLW5ldy1vcHRpb25zJyApLFxuXHRcdFx0bWVkaWFVcGxvYWRlcjogJCggJyNtcHAtdXBsb2FkLWRyb3B6b25lLWFjdGl2aXR5Lm1wcC1kcm9wem9uZScgKVxuXHRcdH07XG5cdH07XG5cblx0Ly8gQ29tYmluZSBhbGwgZXZlbnRzLlxuXHRhcHAuYmluZEV2ZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYy5tZWRpYVByZXNzU2VsZWN0b3IuYmluZCggJ0RPTVN1YnRyZWVNb2RpZmllZCcsIGFwcC5kb01lZGlhUHJlc3MgKTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMubWVkaWFQcmVzc1NlbGVjdG9yLmxlbmd0aDtcblx0fTtcblxuXHQvLyBIaWRlIE1lZGlhUHJlc3MgaWYgcGFyZW50IGRpYWxvZ2UgaXMgY2xpY2tlZC5cblx0YXBwLmRvTWVkaWFQcmVzcyA9IGZ1bmN0aW9uKCkge1xuXHRcdFxuXHRcdGxldCBhY3RpdmUgPSAkKCB0aGlzICk7XG5cblx0XHRpZiAoIGFwcC4kYy5tZWRpYVByZXNzU2VsZWN0b3IuYXR0ciggJ3N0eWxlJyApLmluZGV4T2YoICdub25lJyApID09PSAtMSApIHtcblx0XHRcdGFwcC4kYy5tZWRpYVVwbG9hZGVyLmhpZGUoKTtcblx0XHR9XG5cdH07XG5cblx0Ly8gRW5nYWdlIVxuXHQkKCBhcHAuaW5pdCApO1xuXG59KSggd2luZG93LCBqUXVlcnksIHdpbmRvdy5XRFNfbWVkaWFQcmVzc19PYmplY3QgKTsiLCIvKipcbiAqIEZpbGU6IG1vYmlsZS1tZW51LmpzXG4gKlxuICogQ3JlYXRlIGFuIGFjY29yZGlvbiBzdHlsZSBkcm9wZG93bi5cbiAqL1xud2luZG93Lndkc01vYmlsZU1lbnUgPSB7fTtcbiggZnVuY3Rpb24oIHdpbmRvdywgJCwgYXBwICkge1xuXG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0aWYgKCBhcHAubWVldHNSZXF1aXJlbWVudHMoKSApIHtcblx0XHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHRib2R5OiAkKCAnYm9keScgKSxcblx0XHRcdHdpbmRvdzogJCggd2luZG93ICksXG5cdFx0XHRzdWJNZW51Q29udGFpbmVyOiAkKCAnLm1vYmlsZS1tZW51IC5zdWItbWVudSwgLnV0aWxpdHktbmF2aWdhdGlvbiAuc3ViLW1lbnUnICksXG5cdFx0XHRzdWJTdWJNZW51Q29udGFpbmVyOiAkKCAnLm1vYmlsZS1tZW51IC5zdWItbWVudSAuc3ViLW1lbnUnICksXG5cdFx0XHRzdWJNZW51UGFyZW50SXRlbTogJCggJy5tb2JpbGUtbWVudSBsaS5tZW51LWl0ZW0taGFzLWNoaWxkcmVuLCAudXRpbGl0eS1uYXZpZ2F0aW9uIGxpLm1lbnUtaXRlbS1oYXMtY2hpbGRyZW4nICksXG5cdFx0XHRvZmZDYW52YXNDb250YWluZXI6ICQoICcub2ZmLWNhbnZhcy1jb250YWluZXInIClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMud2luZG93Lm9uKCAnbG9hZCcsIGFwcC5hZGREb3duQXJyb3cgKTtcblx0XHRhcHAuJGMuc3ViTWVudVBhcmVudEl0ZW0ub24oICdjbGljaycsIGFwcC50b2dnbGVTdWJtZW51ICk7XG5cdFx0YXBwLiRjLnN1Yk1lbnVQYXJlbnRJdGVtLm9uKCAndHJhbnNpdGlvbmVuZCcsIGFwcC5yZXNldFN1Yk1lbnUgKTtcblx0XHRhcHAuJGMub2ZmQ2FudmFzQ29udGFpbmVyLm9uKCAndHJhbnNpdGlvbmVuZCcsIGFwcC5mb3JjZUNsb3NlU3VibWVudXMgKTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMuc3ViTWVudUNvbnRhaW5lci5sZW5ndGg7XG5cdH07XG5cblx0Ly8gUmVzZXQgdGhlIHN1Ym1lbnVzIGFmdGVyIGl0J3MgZG9uZSBjbG9zaW5nLlxuXHRhcHAucmVzZXRTdWJNZW51ID0gZnVuY3Rpb24oKSB7XG5cblx0XHQvLyBXaGVuIHRoZSBsaXN0IGl0ZW0gaXMgZG9uZSB0cmFuc2l0aW9uaW5nIGluIGhlaWdodCxcblx0XHQvLyByZW1vdmUgdGhlIGNsYXNzZXMgZnJvbSB0aGUgc3VibWVudSBzbyBpdCBpcyByZWFkeSB0byB0b2dnbGUgYWdhaW4uXG5cdFx0aWYgKCAkKCB0aGlzICkuaXMoICdsaS5tZW51LWl0ZW0taGFzLWNoaWxkcmVuJyApICYmICEgJCggdGhpcyApLmhhc0NsYXNzKCAnaXMtdmlzaWJsZScgKSApIHtcblx0XHRcdCQoIHRoaXMgKS5maW5kKCAndWwuc3ViLW1lbnUnICkucmVtb3ZlQ2xhc3MoICdzbGlkZU91dExlZnQgaXMtdmlzaWJsZScgKTtcblx0XHR9XG5cblx0fTtcblxuXHQvLyBTbGlkZSBvdXQgdGhlIHN1Ym1lbnUgaXRlbXMuXG5cdGFwcC5zbGlkZU91dFN1Yk1lbnVzID0gZnVuY3Rpb24oIGVsICkge1xuXG5cdFx0Ly8gSWYgdGhpcyBpdGVtJ3MgcGFyZW50IGlzIHZpc2libGUgYW5kIHRoaXMgaXMgbm90LCBiYWlsLlxuXHRcdGlmICggZWwucGFyZW50KCkuaGFzQ2xhc3MoICdpcy12aXNpYmxlJyApICYmICEgZWwuaGFzQ2xhc3MoICdpcy12aXNpYmxlJyApICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdC8vIElmIHRoaXMgaXRlbSdzIHBhcmVudCBpcyB2aXNpYmxlIGFuZCB0aGlzIGl0ZW0gaXMgdmlzaWJsZSwgaGlkZSBpdHMgc3VibWVudSB0aGVuIGJhaWwuXG5cdFx0aWYgKCBlbC5wYXJlbnQoKS5oYXNDbGFzcyggJ2lzLXZpc2libGUnICkgJiYgZWwuaGFzQ2xhc3MoICdpcy12aXNpYmxlJyApICkge1xuXHRcdFx0ZWwucmVtb3ZlQ2xhc3MoICdpcy12aXNpYmxlJyApLmZpbmQoICcuc3ViLW1lbnUnICkucmVtb3ZlQ2xhc3MoICdzbGlkZUluTGVmdCcgKS5hZGRDbGFzcyggJ3NsaWRlT3V0TGVmdCcgKTtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cblx0XHRhcHAuJGMuc3ViTWVudUNvbnRhaW5lci5lYWNoKCBmdW5jdGlvbigpIHtcblxuXHRcdFx0Ly8gT25seSB0cnkgdG8gY2xvc2Ugc3VibWVudXMgdGhhdCBhcmUgYWN0dWFsbHkgb3Blbi5cblx0XHRcdGlmICggJCggdGhpcyApLmhhc0NsYXNzKCAnc2xpZGVJbkxlZnQnICkgKSB7XG5cblx0XHRcdFx0Ly8gQ2xvc2UgdGhlIHBhcmVudCBsaXN0IGl0ZW0sIGFuZCBzZXQgdGhlIGNvcnJlc3BvbmRpbmcgYnV0dG9uIGFyaWEgdG8gZmFsc2UuXG5cdFx0XHRcdCQoIHRoaXMgKS5wYXJlbnQoKS5yZW1vdmVDbGFzcyggJ2lzLXZpc2libGUnICkuZmluZCggJy5wYXJlbnQtaW5kaWNhdG9yJyApLmF0dHIoICdhcmlhLWV4cGFuZGVkJywgZmFsc2UgKTtcblxuXHRcdFx0XHQvLyBTbGlkZSBvdXQgdGhlIHN1Ym1lbnUuXG5cdFx0XHRcdCQoIHRoaXMgKS5yZW1vdmVDbGFzcyggJ3NsaWRlSW5MZWZ0JyApLmFkZENsYXNzKCAnc2xpZGVPdXRMZWZ0JyApO1xuXHRcdFx0fVxuXG5cdFx0fSApO1xuXHR9O1xuXG5cdC8vIEFkZCB0aGUgZG93biBhcnJvdyB0byBzdWJtZW51IHBhcmVudHMuXG5cdGFwcC5hZGREb3duQXJyb3cgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMuc3ViTWVudVBhcmVudEl0ZW0ucHJlcGVuZCggJzxidXR0b24gdHlwZT1cImJ1dHRvblwiIGFyaWEtZXhwYW5kZWQ9XCJmYWxzZVwiIGNsYXNzPVwicGFyZW50LWluZGljYXRvclwiIGFyaWEtbGFiZWw9XCJPcGVuIHN1Ym1lbnVcIj48c3BhbiBjbGFzcz1cImRvd24tYXJyb3dcIj48L3NwYW4+PC9idXR0b24+JyApO1xuXHR9O1xuXG5cdC8vIERlYWwgd2l0aCB0aGUgc3VibWVudS5cblx0YXBwLnRvZ2dsZVN1Ym1lbnUgPSBmdW5jdGlvbiggZSApIHtcblxuXHRcdGxldCBlbCA9ICQoIHRoaXMgKSwgLy8gVGhlIG1lbnUgZWxlbWVudCB3aGljaCB3YXMgY2xpY2tlZCBvbi5cblx0XHRcdHN1Yk1lbnUgPSBlbC5jaGlsZHJlbiggJ3VsLnN1Yi1tZW51JyApLCAvLyBUaGUgbmVhcmVzdCBzdWJtZW51LlxuXHRcdFx0JHRhcmdldCA9ICQoIGUudGFyZ2V0ICk7IC8vIHRoZSBlbGVtZW50IHRoYXQncyBhY3R1YWxseSBiZWluZyBjbGlja2VkIChjaGlsZCBvZiB0aGUgbGkgdGhhdCB0cmlnZ2VyZWQgdGhlIGNsaWNrIGV2ZW50KS5cblxuXHRcdC8vIEZpZ3VyZSBvdXQgaWYgd2UncmUgY2xpY2tpbmcgdGhlIGJ1dHRvbiBvciBpdHMgYXJyb3cgY2hpbGQsXG5cdFx0Ly8gaWYgc28sIHdlIGNhbiBqdXN0IG9wZW4gb3IgY2xvc2UgdGhlIG1lbnUgYW5kIGJhaWwuXG5cdFx0aWYgKCAkdGFyZ2V0Lmhhc0NsYXNzKCAnZG93bi1hcnJvdycgKSB8fCAkdGFyZ2V0Lmhhc0NsYXNzKCAncGFyZW50LWluZGljYXRvcicgKSApIHtcblxuXHRcdFx0Ly8gRmlyc3QsIGNvbGxhcHNlIGFueSBhbHJlYWR5IG9wZW5lZCBzdWJtZW51cy5cblx0XHRcdGFwcC5zbGlkZU91dFN1Yk1lbnVzKCBlbCApO1xuXG5cdFx0XHRpZiAoICEgc3ViTWVudS5oYXNDbGFzcyggJ2lzLXZpc2libGUnICkgKSB7XG5cblx0XHRcdFx0Ly8gT3BlbiB0aGUgc3VibWVudS5cblx0XHRcdFx0YXBwLm9wZW5TdWJtZW51KCBlbCwgc3ViTWVudSApO1xuXG5cdFx0XHR9XG5cblx0XHRcdHJldHVybiBmYWxzZTtcblx0XHR9XG5cblx0fTtcblxuXHQvLyBPcGVuIGEgc3VibWVudS5cblx0YXBwLm9wZW5TdWJtZW51ID0gZnVuY3Rpb24oIHBhcmVudCwgc3ViTWVudSApIHtcblxuXHRcdC8vIEV4cGFuZCB0aGUgbGlzdCBtZW51IGl0ZW0sIGFuZCBzZXQgdGhlIGNvcnJlc3BvbmRpbmcgYnV0dG9uIGFyaWEgdG8gdHJ1ZS5cblx0XHRwYXJlbnQuYWRkQ2xhc3MoICdpcy12aXNpYmxlJyApLmZpbmQoICcucGFyZW50LWluZGljYXRvcicgKS5hdHRyKCAnYXJpYS1leHBhbmRlZCcsIHRydWUgKTtcblxuXHRcdC8vIFNsaWRlIHRoZSBtZW51IGluLlxuXHRcdHN1Yk1lbnUuYWRkQ2xhc3MoICdpcy12aXNpYmxlIGFuaW1hdGVkIHNsaWRlSW5MZWZ0JyApO1xuXHR9O1xuXG5cdC8vIEZvcmNlIGNsb3NlIGFsbCB0aGUgc3VibWVudXMgd2hlbiB0aGUgbWFpbiBtZW51IGNvbnRhaW5lciBpcyBjbG9zZWQuXG5cdGFwcC5mb3JjZUNsb3NlU3VibWVudXMgPSBmdW5jdGlvbigpIHtcblxuXHRcdC8vIFRoZSB0cmFuc2l0aW9uZW5kIGV2ZW50IHRyaWdnZXJzIG9uIG9wZW4gYW5kIG9uIGNsb3NlLCBuZWVkIHRvIG1ha2Ugc3VyZSB3ZSBvbmx5IGRvIHRoaXMgb24gY2xvc2UuXG5cdFx0aWYgKCAhICQoIHRoaXMgKS5oYXNDbGFzcyggJ2lzLXZpc2libGUnICkgKSB7XG5cdFx0XHRhcHAuJGMuc3ViTWVudVBhcmVudEl0ZW0ucmVtb3ZlQ2xhc3MoICdpcy12aXNpYmxlJyApLmZpbmQoICcucGFyZW50LWluZGljYXRvcicgKS5hdHRyKCAnYXJpYS1leHBhbmRlZCcsIGZhbHNlICk7XG5cdFx0XHRhcHAuJGMuc3ViTWVudUNvbnRhaW5lci5yZW1vdmVDbGFzcyggJ2lzLXZpc2libGUgc2xpZGVJbkxlZnQnICk7XG5cdFx0XHRhcHAuJGMuYm9keS5jc3MoICdvdmVyZmxvdycsICd2aXNpYmxlJyApO1xuXHRcdFx0YXBwLiRjLmJvZHkudW5iaW5kKCAndG91Y2hzdGFydCcgKTtcblx0XHR9XG5cblx0XHRpZiAoICQoIHRoaXMgKS5oYXNDbGFzcyggJ2lzLXZpc2libGUnICkgKSB7XG5cdFx0XHRhcHAuJGMuYm9keS5jc3MoICdvdmVyZmxvdycsICdoaWRkZW4nICk7XG5cdFx0XHRhcHAuJGMuYm9keS5iaW5kKCAndG91Y2hzdGFydCcsIGZ1bmN0aW9uKCBlICkge1xuXHRcdFx0XHRpZiAoICEgJCggZS50YXJnZXQgKS5wYXJlbnRzKCAnLm9mZi1jYW52YXMtY29udGFpbmVyLCAub2ZmLWNhbnZhcy1zY3JlZW4sIC5zaXRlLWhlYWRlcicgKVswXSApIHtcblx0XHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcdH1cblx0XHRcdH0gKTtcblx0XHR9XG5cdH07XG5cblx0Ly8gRW5nYWdlIVxuXHQkKCBhcHAuaW5pdCApO1xuXG59KCB3aW5kb3csIGpRdWVyeSwgd2luZG93Lndkc01vYmlsZU1lbnUgKSApO1xuIiwiLyoqXG4gKiBGaWxlIG1vZGFsLmpzXG4gKlxuICogRGVhbCB3aXRoIG11bHRpcGxlIG1vZGFscyBhbmQgdGhlaXIgbWVkaWEuXG4gKi9cbndpbmRvdy53ZHNNb2RhbCA9IHt9O1xuKCBmdW5jdGlvbiggd2luZG93LCAkLCBhcHAgKSB7XG5cblx0bGV0ICRtb2RhbFRvZ2dsZSxcblx0XHQkZm9jdXNhYmxlQ2hpbGRyZW4sXG5cdFx0JHBsYXllcixcblx0XHQkdGFnID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCggJ3NjcmlwdCcgKSxcblx0XHQkZmlyc3RTY3JpcHRUYWcgPSBkb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSggJ3NjcmlwdCcgKVswXSxcblx0XHRZVDtcblxuXHQvLyBDb25zdHJ1Y3Rvci5cblx0YXBwLmluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuY2FjaGUoKTtcblxuXHRcdGlmICggYXBwLm1lZXRzUmVxdWlyZW1lbnRzKCkgKSB7XG5cdFx0XHQkZmlyc3RTY3JpcHRUYWcucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUoICR0YWcsICRmaXJzdFNjcmlwdFRhZyApO1xuXHRcdFx0YXBwLmJpbmRFdmVudHMoKTtcblx0XHR9XG5cdH07XG5cblx0Ly8gQ2FjaGUgYWxsIHRoZSB0aGluZ3MuXG5cdGFwcC5jYWNoZSA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYyA9IHtcblx0XHRcdCdib2R5JzogJCggJ2JvZHknIClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIERvIHdlIG1lZXQgdGhlIHJlcXVpcmVtZW50cz9cblx0YXBwLm1lZXRzUmVxdWlyZW1lbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0cmV0dXJuICQoICcubW9kYWwtdHJpZ2dlcicgKS5sZW5ndGg7XG5cdH07XG5cblx0Ly8gQ29tYmluZSBhbGwgZXZlbnRzLlxuXHRhcHAuYmluZEV2ZW50cyA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0Ly8gVHJpZ2dlciBhIG1vZGFsIHRvIG9wZW4uXG5cdFx0YXBwLiRjLmJvZHkub24oICdjbGljayB0b3VjaHN0YXJ0JywgJy5tb2RhbC10cmlnZ2VyJywgYXBwLm9wZW5Nb2RhbCApO1xuXG5cdFx0Ly8gVHJpZ2dlciB0aGUgY2xvc2UgYnV0dG9uIHRvIGNsb3NlIHRoZSBtb2RhbC5cblx0XHRhcHAuJGMuYm9keS5vbiggJ2NsaWNrIHRvdWNoc3RhcnQnLCAnLmNsb3NlJywgYXBwLmNsb3NlTW9kYWwgKTtcblxuXHRcdC8vIEFsbG93IHRoZSB1c2VyIHRvIGNsb3NlIHRoZSBtb2RhbCBieSBoaXR0aW5nIHRoZSBlc2Mga2V5LlxuXHRcdGFwcC4kYy5ib2R5Lm9uKCAna2V5ZG93bicsIGFwcC5lc2NLZXlDbG9zZSApO1xuXG5cdFx0Ly8gQWxsb3cgdGhlIHVzZXIgdG8gY2xvc2UgdGhlIG1vZGFsIGJ5IGNsaWNraW5nIG91dHNpZGUgb2YgdGhlIG1vZGFsLlxuXHRcdGFwcC4kYy5ib2R5Lm9uKCAnY2xpY2sgdG91Y2hzdGFydCcsICdkaXYubW9kYWwtb3BlbicsIGFwcC5jbG9zZU1vZGFsQnlDbGljayApO1xuXG5cdFx0Ly8gTGlzdGVuIHRvIHRhYnMsIHRyYXAga2V5Ym9hcmQgaWYgd2UgbmVlZCB0b1xuXHRcdGFwcC4kYy5ib2R5Lm9uKCAna2V5ZG93bicsIGFwcC50cmFwS2V5Ym9hcmRNYXliZSApO1xuXG5cdH07XG5cblx0Ly8gT3BlbiB0aGUgbW9kYWwuXG5cdGFwcC5vcGVuTW9kYWwgPSBmdW5jdGlvbigpIHtcblxuXHRcdC8vIFN0b3JlIHRoZSBtb2RhbCB0b2dnbGUgZWxlbWVudFxuXHRcdCRtb2RhbFRvZ2dsZSA9ICQoIHRoaXMgKTtcblxuXHRcdC8vIEZpZ3VyZSBvdXQgd2hpY2ggbW9kYWwgd2UncmUgb3BlbmluZyBhbmQgc3RvcmUgdGhlIG9iamVjdC5cblx0XHRsZXQgJG1vZGFsID0gJCggJCggdGhpcyApLmRhdGEoICd0YXJnZXQnICkgKTtcblxuXHRcdC8vIERpc3BsYXkgdGhlIG1vZGFsLlxuXHRcdCRtb2RhbC5hZGRDbGFzcyggJ21vZGFsLW9wZW4nICk7XG5cblx0XHQvLyBBZGQgYm9keSBjbGFzcy5cblx0XHRhcHAuJGMuYm9keS5hZGRDbGFzcyggJ21vZGFsLW9wZW4nICk7XG5cblx0XHQvLyBGaW5kIHRoZSBmb2N1c2FibGUgY2hpbGRyZW4gb2YgdGhlIG1vZGFsLlxuXHRcdC8vIFRoaXMgbGlzdCBtYXkgYmUgaW5jb21wbGV0ZSwgcmVhbGx5IHdpc2ggalF1ZXJ5IGhhZCB0aGUgOmZvY3VzYWJsZSBwc2V1ZG8gbGlrZSBqUXVlcnkgVUkgZG9lcy5cblx0XHQvLyBGb3IgbW9yZSBhYm91dCA6aW5wdXQgc2VlOiBodHRwczovL2FwaS5qcXVlcnkuY29tL2lucHV0LXNlbGVjdG9yL1xuXHRcdCRmb2N1c2FibGVDaGlsZHJlbiA9ICRtb2RhbC5maW5kKCAnYSwgOmlucHV0LCBbdGFiaW5kZXhdJyApO1xuXG5cdFx0Ly8gSWRlYWxseSwgdGhlcmUgaXMgYWx3YXlzIG9uZSAodGhlIGNsb3NlIGJ1dHRvbiksIGJ1dCB5b3UgbmV2ZXIga25vdy5cblx0XHRpZiAoIDAgPCAkZm9jdXNhYmxlQ2hpbGRyZW4ubGVuZ3RoICkge1xuXG5cdFx0XHQvLyBTaGlmdCBmb2N1cyB0byB0aGUgZmlyc3QgZm9jdXNhYmxlIGVsZW1lbnQuXG5cdFx0XHQkZm9jdXNhYmxlQ2hpbGRyZW5bMF0uZm9jdXMoKTtcblx0XHR9XG5cblx0fTtcblxuXHQvLyBDbG9zZSB0aGUgbW9kYWwuXG5cdGFwcC5jbG9zZU1vZGFsID0gZnVuY3Rpb24oKSB7XG5cblx0XHQvLyBGaWd1cmUgdGhlIG9wZW5lZCBtb2RhbCB3ZSdyZSBjbG9zaW5nIGFuZCBzdG9yZSB0aGUgb2JqZWN0LlxuXHRcdGxldCAkbW9kYWwgPSAkKCAkKCAnZGl2Lm1vZGFsLW9wZW4gLmNsb3NlJyApLmRhdGEoICd0YXJnZXQnICkgKSxcblxuXHRcdFx0Ly8gRmluZCB0aGUgaWZyYW1lIGluIHRoZSAkbW9kYWwgb2JqZWN0LlxuXHRcdFx0JGlmcmFtZSA9ICRtb2RhbC5maW5kKCAnaWZyYW1lJyApO1xuXG5cdFx0Ly8gT25seSBkbyB0aGlzIGlmIHRoZXJlIGFyZSBhbnkgaWZyYW1lcy5cblx0XHRpZiAoICRpZnJhbWUubGVuZ3RoICkge1xuXG5cdFx0XHQvLyBHZXQgdGhlIGlmcmFtZSBzcmMgVVJMLlxuXHRcdFx0bGV0IHVybCA9ICRpZnJhbWUuYXR0ciggJ3NyYycgKTtcblxuXHRcdFx0Ly8gUmVtb3ZpbmcvUmVhZGRpbmcgdGhlIFVSTCB3aWxsIGVmZmVjdGl2ZWx5IGJyZWFrIHRoZSBZb3VUdWJlIEFQSS5cblx0XHRcdC8vIFNvIGxldCdzIG5vdCBkbyB0aGF0IHdoZW4gdGhlIGlmcmFtZSBVUkwgY29udGFpbnMgdGhlIGVuYWJsZWpzYXBpIHBhcmFtZXRlci5cblx0XHRcdGlmICggISB1cmwuaW5jbHVkZXMoICdlbmFibGVqc2FwaT0xJyApICkge1xuXG5cdFx0XHRcdC8vIFJlbW92ZSB0aGUgc291cmNlIFVSTCwgdGhlbiBhZGQgaXQgYmFjaywgc28gdGhlIHZpZGVvIGNhbiBiZSBwbGF5ZWQgYWdhaW4gbGF0ZXIuXG5cdFx0XHRcdCRpZnJhbWUuYXR0ciggJ3NyYycsICcnICkuYXR0ciggJ3NyYycsIHVybCApO1xuXHRcdFx0fSBlbHNlIHtcblxuXHRcdFx0XHQvLyBVc2UgdGhlIFlvdVR1YmUgQVBJIHRvIHN0b3AgdGhlIHZpZGVvLlxuXHRcdFx0XHQkcGxheWVyLnN0b3BWaWRlbygpO1xuXHRcdFx0fVxuXHRcdH1cblxuXHRcdC8vIEZpbmFsbHksIGhpZGUgdGhlIG1vZGFsLlxuXHRcdCRtb2RhbC5yZW1vdmVDbGFzcyggJ21vZGFsLW9wZW4nICk7XG5cblx0XHQvLyBSZW1vdmUgdGhlIGJvZHkgY2xhc3MuXG5cdFx0YXBwLiRjLmJvZHkucmVtb3ZlQ2xhc3MoICdtb2RhbC1vcGVuJyApO1xuXG5cdFx0Ly8gUmV2ZXJ0IGZvY3VzIGJhY2sgdG8gdG9nZ2xlIGVsZW1lbnRcblx0XHQkbW9kYWxUb2dnbGUuZm9jdXMoKTtcblxuXHR9O1xuXG5cdC8vIENsb3NlIGlmIFwiZXNjXCIga2V5IGlzIHByZXNzZWQuXG5cdGFwcC5lc2NLZXlDbG9zZSA9IGZ1bmN0aW9uKCBldmVudCApIHtcblx0XHRpZiAoIDI3ID09PSBldmVudC5rZXlDb2RlICkge1xuXHRcdFx0YXBwLmNsb3NlTW9kYWwoKTtcblx0XHR9XG5cdH07XG5cblx0Ly8gQ2xvc2UgaWYgdGhlIHVzZXIgY2xpY2tzIG91dHNpZGUgb2YgdGhlIG1vZGFsXG5cdGFwcC5jbG9zZU1vZGFsQnlDbGljayA9IGZ1bmN0aW9uKCBldmVudCApIHtcblxuXHRcdC8vIElmIHRoZSBwYXJlbnQgY29udGFpbmVyIGlzIE5PVCB0aGUgbW9kYWwgZGlhbG9nIGNvbnRhaW5lciwgY2xvc2UgdGhlIG1vZGFsXG5cdFx0aWYgKCAhICQoIGV2ZW50LnRhcmdldCApLnBhcmVudHMoICdkaXYnICkuaGFzQ2xhc3MoICdtb2RhbC1kaWFsb2cnICkgKSB7XG5cdFx0XHRhcHAuY2xvc2VNb2RhbCgpO1xuXHRcdH1cblx0fTtcblxuXHQvLyBUcmFwIHRoZSBrZXlib2FyZCBpbnRvIGEgbW9kYWwgd2hlbiBvbmUgaXMgYWN0aXZlLlxuXHRhcHAudHJhcEtleWJvYXJkTWF5YmUgPSBmdW5jdGlvbiggZXZlbnQgKSB7XG5cblx0XHQvLyBXZSBvbmx5IG5lZWQgdG8gZG8gc3R1ZmYgd2hlbiB0aGUgbW9kYWwgaXMgb3BlbiBhbmQgdGFiIGlzIHByZXNzZWQuXG5cdFx0aWYgKCA5ID09PSBldmVudC53aGljaCAmJiAwIDwgJCggJy5tb2RhbC1vcGVuJyApLmxlbmd0aCApIHtcblx0XHRcdGxldCAkZm9jdXNlZCA9ICQoICc6Zm9jdXMnICksXG5cdFx0XHRcdGZvY3VzSW5kZXggPSAkZm9jdXNhYmxlQ2hpbGRyZW4uaW5kZXgoICRmb2N1c2VkICk7XG5cblx0XHRcdGlmICggMCA9PT0gZm9jdXNJbmRleCAmJiBldmVudC5zaGlmdEtleSApIHtcblxuXHRcdFx0XHQvLyBJZiB0aGlzIGlzIHRoZSBmaXJzdCBmb2N1c2FibGUgZWxlbWVudCwgYW5kIHNoaWZ0IGlzIGhlbGQgd2hlbiBwcmVzc2luZyB0YWIsIGdvIGJhY2sgdG8gbGFzdCBmb2N1c2FibGUgZWxlbWVudC5cblx0XHRcdFx0JGZvY3VzYWJsZUNoaWxkcmVuWyAkZm9jdXNhYmxlQ2hpbGRyZW4ubGVuZ3RoIC0gMSBdLmZvY3VzKCk7XG5cdFx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHR9IGVsc2UgaWYgKCAhIGV2ZW50LnNoaWZ0S2V5ICYmIGZvY3VzSW5kZXggPT09ICRmb2N1c2FibGVDaGlsZHJlbi5sZW5ndGggLSAxICkge1xuXG5cdFx0XHRcdC8vIElmIHRoaXMgaXMgdGhlIGxhc3QgZm9jdXNhYmxlIGVsZW1lbnQsIGFuZCBzaGlmdCBpcyBub3QgaGVsZCwgZ28gYmFjayB0byB0aGUgZmlyc3QgZm9jdXNhYmxlIGVsZW1lbnQuXG5cdFx0XHRcdCRmb2N1c2FibGVDaGlsZHJlblswXS5mb2N1cygpO1xuXHRcdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0fVxuXHRcdH1cblx0fTtcblxuXHQvLyBIb29rIGludG8gWW91VHViZSA8aWZyYW1lPi5cblx0YXBwLm9uWW91VHViZUlmcmFtZUFQSVJlYWR5ID0gZnVuY3Rpb24oKSB7XG5cdFx0bGV0ICRtb2RhbCA9ICQoICdkaXYubW9kYWwnICksXG5cdFx0XHQkaWZyYW1laWQgPSAkbW9kYWwuZmluZCggJ2lmcmFtZScgKS5hdHRyKCAnaWQnICk7XG5cblx0XHQkcGxheWVyID0gbmV3IFlULlBsYXllciggJGlmcmFtZWlkLCB7XG5cdFx0XHRldmVudHM6IHtcblx0XHRcdFx0J29uUmVhZHknOiBhcHAub25QbGF5ZXJSZWFkeSxcblx0XHRcdFx0J29uU3RhdGVDaGFuZ2UnOiBhcHAub25QbGF5ZXJTdGF0ZUNoYW5nZVxuXHRcdFx0fVxuXHRcdH0gKTtcblx0fTtcblxuXHQvLyBEbyBzb21ldGhpbmcgb24gcGxheWVyIHJlYWR5LlxuXHRhcHAub25QbGF5ZXJSZWFkeSA9IGZ1bmN0aW9uKCkge1xuXHR9O1xuXG5cdC8vIERvIHNvbWV0aGluZyBvbiBwbGF5ZXIgc3RhdGUgY2hhbmdlLlxuXHRhcHAub25QbGF5ZXJTdGF0ZUNoYW5nZSA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0Ly8gU2V0IGZvY3VzIHRvIHRoZSBmaXJzdCBmb2N1c2FibGUgZWxlbWVudCBpbnNpZGUgb2YgdGhlIG1vZGFsIHRoZSBwbGF5ZXIgaXMgaW4uXG5cdFx0JCggZXZlbnQudGFyZ2V0LmEgKS5wYXJlbnRzKCAnLm1vZGFsJyApLmZpbmQoICdhLCA6aW5wdXQsIFt0YWJpbmRleF0nICkuZmlyc3QoKS5mb2N1cygpO1xuXHR9O1xuXG5cblx0Ly8gRW5nYWdlIVxuXHQkKCBhcHAuaW5pdCApO1xufSggd2luZG93LCBqUXVlcnksIHdpbmRvdy53ZHNNb2RhbCApICk7XG4iLCIvKipcbiAqIEZpbGU6IG5hdmlnYXRpb24tcHJpbWFyeS5qc1xuICpcbiAqIEhlbHBlcnMgZm9yIHRoZSBwcmltYXJ5IG5hdmlnYXRpb24uXG4gKi9cbndpbmRvdy53ZHNQcmltYXJ5TmF2aWdhdGlvbiA9IHt9O1xuKCBmdW5jdGlvbiggd2luZG93LCAkLCBhcHAgKSB7XG5cblx0Ly8gQ29uc3RydWN0b3IuXG5cdGFwcC5pbml0ID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLmNhY2hlKCk7XG5cblx0XHRpZiAoIGFwcC5tZWV0c1JlcXVpcmVtZW50cygpICkge1xuXHRcdFx0YXBwLmJpbmRFdmVudHMoKTtcblx0XHR9XG5cdH07XG5cblx0Ly8gQ2FjaGUgYWxsIHRoZSB0aGluZ3MuXG5cdGFwcC5jYWNoZSA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYyA9IHtcblx0XHRcdHdpbmRvdzogJCggd2luZG93ICksXG5cdFx0XHRzdWJNZW51Q29udGFpbmVyOiAkKCAnLm1haW4tbmF2aWdhdGlvbiAuc3ViLW1lbnUnICksXG5cdFx0XHRzdWJNZW51UGFyZW50SXRlbTogJCggJy5tYWluLW5hdmlnYXRpb24gbGkubWVudS1pdGVtLWhhcy1jaGlsZHJlbicgKSxcblx0XHRcdHNpdGVIZWFkZXI6ICQoICcuc2l0ZS1oZWFkZXInIClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMud2luZG93Lm9uKCAnbG9hZCcsIGFwcC5hZGREb3duQXJyb3cgKTtcblx0XHRhcHAuJGMuc3ViTWVudVBhcmVudEl0ZW0uZmluZCggJ2EnICkub24oICdmb2N1c2luIGZvY3Vzb3V0JywgYXBwLnRvZ2dsZUZvY3VzICk7XG5cdFx0YXBwLiRjLndpbmRvdy5vbiggJ3Njcm9sbCcsIGFwcC5zY3JvbGxIZWxwZXIgKTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMuc3ViTWVudUNvbnRhaW5lci5sZW5ndGg7XG5cdH07XG5cblx0Ly8gQWRkIHRoZSBkb3duIGFycm93IHRvIHN1Ym1lbnUgcGFyZW50cy5cblx0YXBwLmFkZERvd25BcnJvdyA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYy5zdWJNZW51UGFyZW50SXRlbS5maW5kKCAnPiBhJyApLmFwcGVuZCggJzxzcGFuIGNsYXNzPVwiY2FyZXQtZG93blwiIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPjwvc3Bhbj4nICk7XG5cdH07XG5cblx0Ly8gVG9nZ2xlIHRoZSBmb2N1cyBjbGFzcyBvbiB0aGUgbGluayBwYXJlbnQuXG5cdGFwcC50b2dnbGVGb2N1cyA9IGZ1bmN0aW9uKCkge1xuXHRcdCQoIHRoaXMgKS5wYXJlbnRzKCAnbGkubWVudS1pdGVtLWhhcy1jaGlsZHJlbicgKS50b2dnbGVDbGFzcyggJ2ZvY3VzJyApO1xuXHR9O1xuXG5cdC8vIEFkZCBoZWxwZXIgdG8gY29uZGVuc2UgbmF2IG9uIHNjcm9sbCBmb3IgZGVza3RvcFxuXHRhcHAuc2Nyb2xsSGVscGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0bGV0IHdpZHRoID0gYXBwLiRjLndpbmRvdy53aWR0aCgpLFxuXHRcdFx0c2Nyb2xsID0gYXBwLiRjLndpbmRvdy5zY3JvbGxUb3AoKTtcblxuXHRcdGlmICggJzkwMCcgPD0gd2lkdGggKSB7XG5cblx0XHRcdGlmICggJzI1JyA8PSBzY3JvbGwgKSB7XG5cdFx0XHRcdGFwcC4kYy5zaXRlSGVhZGVyLmFkZENsYXNzKCAnc2Nyb2xsaW5nJyApO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0YXBwLiRjLnNpdGVIZWFkZXIucmVtb3ZlQ2xhc3MoICdzY3JvbGxpbmcnICk7XG5cdFx0XHR9XG5cdFx0fSBlbHNlIHtcblx0XHRcdGFwcC4kYy5zaXRlSGVhZGVyLnJlbW92ZUNsYXNzKCAnc2Nyb2xsaW5nJyApO1xuXHRcdH1cblx0fTtcblxuXHQvLyBFbmdhZ2UhXG5cdCQoIGFwcC5pbml0ICk7XG5cbn0oIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cud2RzUHJpbWFyeU5hdmlnYXRpb24gKSApO1xuIiwiLyoqXG4gKiBGaWxlOiBvZmYtY2FudmFzLmpzXG4gKlxuICogSGVscCBkZWFsIHdpdGggdGhlIG9mZi1jYW52YXMgbW9iaWxlIG1lbnUuXG4gKi9cbndpbmRvdy53ZHNvZmZDYW52YXMgPSB7fTtcbiggZnVuY3Rpb24oIHdpbmRvdywgJCwgYXBwICkge1xuXG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0aWYgKCBhcHAubWVldHNSZXF1aXJlbWVudHMoKSApIHtcblx0XHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHRib2R5OiAkKCAnYm9keScgKSxcblx0XHRcdG9mZkNhbnZhc0Nsb3NlOiAkKCAnLm9mZi1jYW52YXMtY2xvc2UnICksXG5cdFx0XHRvZmZDYW52YXNDb250YWluZXI6ICQoICcub2ZmLWNhbnZhcy1jb250YWluZXInICksXG5cdFx0XHRvZmZDYW52YXNPcGVuOiAkKCAnLm9mZi1jYW52YXMtb3BlbicgKSxcblx0XHRcdG9mZkNhbnZhc1NjcmVlbjogJCggJy5vZmYtY2FudmFzLXNjcmVlbicgKVxuXHRcdH07XG5cdH07XG5cblx0Ly8gQ29tYmluZSBhbGwgZXZlbnRzLlxuXHRhcHAuYmluZEV2ZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYy5ib2R5Lm9uKCAna2V5ZG93bicsIGFwcC5lc2NLZXlDbG9zZSApO1xuXHRcdGFwcC4kYy5vZmZDYW52YXNDbG9zZS5vbiggJ2NsaWNrJywgYXBwLmNsb3Nlb2ZmQ2FudmFzICk7XG5cdFx0YXBwLiRjLm9mZkNhbnZhc09wZW4ub24oICdjbGljaycsIGFwcC50b2dnbGVvZmZDYW52YXMgKTtcblx0XHRhcHAuJGMub2ZmQ2FudmFzU2NyZWVuLm9uKCAnY2xpY2snLCBhcHAuY2xvc2VvZmZDYW52YXMgKTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMub2ZmQ2FudmFzQ29udGFpbmVyLmxlbmd0aDtcblx0fTtcblxuXHQvLyBUbyBzaG93IG9yIG5vdCB0byBzaG93P1xuXHRhcHAudG9nZ2xlb2ZmQ2FudmFzID0gZnVuY3Rpb24oKSB7XG5cblx0XHRpZiAoICd0cnVlJyA9PT0gJCggdGhpcyApLmF0dHIoICdhcmlhLWV4cGFuZGVkJyApICkge1xuXHRcdFx0YXBwLmNsb3Nlb2ZmQ2FudmFzKCk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGFwcC5vcGVub2ZmQ2FudmFzKCk7XG5cdFx0fVxuXG5cdH07XG5cblx0Ly8gU2hvdyB0aGF0IGRyYXdlciFcblx0YXBwLm9wZW5vZmZDYW52YXMgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMub2ZmQ2FudmFzQ29udGFpbmVyLmFkZENsYXNzKCAnaXMtdmlzaWJsZScgKTtcblx0XHRhcHAuJGMub2ZmQ2FudmFzT3Blbi5hZGRDbGFzcyggJ2lzLXZpc2libGUnICk7XG5cdFx0YXBwLiRjLm9mZkNhbnZhc1NjcmVlbi5hZGRDbGFzcyggJ2lzLXZpc2libGUnICk7XG5cblx0XHRhcHAuJGMub2ZmQ2FudmFzT3Blbi5hdHRyKCAnYXJpYS1leHBhbmRlZCcsIHRydWUgKTtcblx0XHRhcHAuJGMub2ZmQ2FudmFzQ29udGFpbmVyLmF0dHIoICdhcmlhLWhpZGRlbicsIGZhbHNlICk7XG5cblx0XHRhcHAuJGMub2ZmQ2FudmFzQ29udGFpbmVyLmZpbmQoICdidXR0b24nICkuZmlyc3QoKS5mb2N1cygpO1xuXHR9O1xuXG5cdC8vIENsb3NlIHRoYXQgZHJhd2VyIVxuXHRhcHAuY2xvc2VvZmZDYW52YXMgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMub2ZmQ2FudmFzQ29udGFpbmVyLnJlbW92ZUNsYXNzKCAnaXMtdmlzaWJsZScgKTtcblx0XHRhcHAuJGMub2ZmQ2FudmFzT3Blbi5yZW1vdmVDbGFzcyggJ2lzLXZpc2libGUnICk7XG5cdFx0YXBwLiRjLm9mZkNhbnZhc1NjcmVlbi5yZW1vdmVDbGFzcyggJ2lzLXZpc2libGUnICk7XG5cblx0XHRhcHAuJGMub2ZmQ2FudmFzT3Blbi5hdHRyKCAnYXJpYS1leHBhbmRlZCcsIGZhbHNlICk7XG5cdFx0YXBwLiRjLm9mZkNhbnZhc0NvbnRhaW5lci5hdHRyKCAnYXJpYS1oaWRkZW4nLCB0cnVlICk7XG5cblx0XHRhcHAuJGMub2ZmQ2FudmFzT3Blbi5mb2N1cygpO1xuXHR9O1xuXG5cdC8vIENsb3NlIGRyYXdlciBpZiBcImVzY1wiIGtleSBpcyBwcmVzc2VkLlxuXHRhcHAuZXNjS2V5Q2xvc2UgPSBmdW5jdGlvbiggZXZlbnQgKSB7XG5cdFx0aWYgKCAyNyA9PT0gZXZlbnQua2V5Q29kZSApIHtcblx0XHRcdGFwcC5jbG9zZW9mZkNhbnZhcygpO1xuXHRcdH1cblx0fTtcblxuXHQvLyBFbmdhZ2UhXG5cdCQoIGFwcC5pbml0ICk7XG5cbn0oIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cud2Rzb2ZmQ2FudmFzICkgKTtcbiIsIi8qKlxuICogT3BlbiBTaWRlYmFyIEFjdGl2aXR5IEZpbHRlciBTY3JpcHQuXG4gKi9cbndpbmRvdy5XRFNPcGVuU2lkZWJhck9iamVjdCA9IHt9O1xuKCBmdW5jdGlvbiggd2luZG93LCAkLCBhcHAgKSB7XG5cblx0Ly8gQ29uc3RydWN0b3IuXG5cdGFwcC5pbml0ID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLmNhY2hlKCk7XG5cblx0XHRpZiAoIGFwcC5tZWV0c1JlcXVpcmVtZW50cygpICkge1xuXHRcdFx0YXBwLmJpbmRFdmVudHMoKTtcblx0XHR9XG5cdH07XG5cblx0Ly8gQ2FjaGUgYWxsIHRoZSB0aGluZ3MuXG5cdGFwcC5jYWNoZSA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYyA9IHtcblx0XHRcdHdpbmRvdzogJCggd2luZG93ICksXG5cdFx0XHRvcGVuU2lkZWJhckNvbnRhaW5lcjogJCggJyN3ZHMtcmVjb2xhYm9yYXItc2lkZWJhci1maWx0ZXJzJyApLFxuXHRcdFx0b3BlblNpZGViYXJTZWxlY3RvcjogJCggJyNzZWFyY2gtZmllbGQnIClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblx0XHQkKCBkb2N1bWVudCApLm9uKCAnY2xpY2snLCBhcHAuZG9DbG9zZVNpZGViYXIgKTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMub3BlblNpZGViYXJDb250YWluZXIubGVuZ3RoO1xuXHR9O1xuXG5cdGFwcC5kb0Nsb3NlU2lkZWJhciA9IGZ1bmN0aW9uKCBlICkge1xuXHRcdGlmICggISBhcHAuJGMub3BlblNpZGViYXJDb250YWluZXIuaXMoIGUudGFyZ2V0ICkgJiYgMCA9PT0gYXBwLiRjLm9wZW5TaWRlYmFyQ29udGFpbmVyLmhhcyggZS50YXJnZXQgKS5sZW5ndGggKSB7XG5cdFx0XHRhcHAuJGMub3BlblNpZGViYXJDb250YWluZXIucmVtb3ZlQ2xhc3MoICdvcGVuLXNpZGViYXInICk7XG4gICAgICAgIH0gZWxzZSB7XG5cdFx0XHRhcHAuJGMub3BlblNpZGViYXJDb250YWluZXIuYWRkQ2xhc3MoICdvcGVuLXNpZGViYXInICk7XG4gICAgICAgIH1cblx0fTtcblxuXHQvLyBFbmdhZ2UhXG5cdCQoIGFwcC5pbml0ICk7XG5cbn0gKCB3aW5kb3csIGpRdWVyeSwgd2luZG93LldEU09wZW5TaWRlYmFyT2JqZWN0ICkgKTtcbiIsIi8qKlxuICogUHJvZmlsZSBCYXIgVXBsb2FkIFNjcmlwdC5cbiAqL1xud2luZG93LldEU1Byb2ZpbGVCYXJPYmplY3QgPSB7fTtcbiggZnVuY3Rpb24oIHdpbmRvdywgJCwgYXBwICkge1xuXG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0aWYgKCBhcHAubWVldHNSZXF1aXJlbWVudHMoKSApIHtcblx0XHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHR3aW5kb3c6ICQoIHdpbmRvdyApLFxuXHRcdFx0cHJvZmlsZUJhclNlbGVjdG9yOiAkKCAnLmJ1dHRvbi1kcm9wZG93bicgKSxcblx0XHRcdGNoZWNrQm94OiAkKCAnLmRyb3Bkb3duLW9wZW4nIClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMucHJvZmlsZUJhclNlbGVjdG9yLm9uKCAnY2xpY2snLCBhcHAuZG9Qcm9maWxlTWVudSApO1xuXHR9O1xuXG5cdC8vIERvIHdlIG1lZXQgdGhlIHJlcXVpcmVtZW50cz9cblx0YXBwLm1lZXRzUmVxdWlyZW1lbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0cmV0dXJuIGFwcC4kYy5wcm9maWxlQmFyU2VsZWN0b3IubGVuZ3RoO1xuXHR9O1xuXG5cdC8vIE1lbnVcblx0YXBwLmRvUHJvZmlsZU1lbnUgPSBmdW5jdGlvbigpIHtcblxuXHRcdGlmICggYXBwLiRjLnByb2ZpbGVCYXJTZWxlY3Rvci5oYXNDbGFzcyggJ21lbnUtb3BlbicgKSApIHtcblx0XHRcdGFwcC4kYy5wcm9maWxlQmFyU2VsZWN0b3IucmVtb3ZlQ2xhc3MoICdtZW51LW9wZW4nICk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGFwcC4kYy5wcm9maWxlQmFyU2VsZWN0b3IuYWRkQ2xhc3MoICdtZW51LW9wZW4nICk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIEVuZ2FnZSFcblx0JCggYXBwLmluaXQgKTtcblxufSAoIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cuV0RTUHJvZmlsZUJhck9iamVjdCApICk7XG4iLCIvKipcbiAqIEZpbGUgc2tpcC1saW5rLWZvY3VzLWZpeC5qcy5cbiAqXG4gKiBIZWxwcyB3aXRoIGFjY2Vzc2liaWxpdHkgZm9yIGtleWJvYXJkIG9ubHkgdXNlcnMuXG4gKlxuICogTGVhcm4gbW9yZTogaHR0cHM6Ly9naXQuaW8vdldkcjJcbiAqL1xuKCBmdW5jdGlvbigpIHtcblx0dmFyIGlzV2Via2l0ID0gLTEgPCBuYXZpZ2F0b3IudXNlckFnZW50LnRvTG93ZXJDYXNlKCkuaW5kZXhPZiggJ3dlYmtpdCcgKSxcblx0XHRpc09wZXJhID0gLTEgPCBuYXZpZ2F0b3IudXNlckFnZW50LnRvTG93ZXJDYXNlKCkuaW5kZXhPZiggJ29wZXJhJyApLFxuXHRcdGlzSWUgPSAtMSA8IG5hdmlnYXRvci51c2VyQWdlbnQudG9Mb3dlckNhc2UoKS5pbmRleE9mKCAnbXNpZScgKTtcblxuXHRpZiAoICggaXNXZWJraXQgfHwgaXNPcGVyYSB8fCBpc0llICkgJiYgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQgJiYgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIgKSB7XG5cdFx0d2luZG93LmFkZEV2ZW50TGlzdGVuZXIoICdoYXNoY2hhbmdlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgaWQgPSBsb2NhdGlvbi5oYXNoLnN1YnN0cmluZyggMSApLFxuXHRcdFx0XHRlbGVtZW50O1xuXG5cdFx0XHRpZiAoICEgKCAvXltBLXowLTlfLV0rJC8gKS50ZXN0KCBpZCApICkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdGVsZW1lbnQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggaWQgKTtcblxuXHRcdFx0aWYgKCBlbGVtZW50ICkge1xuXHRcdFx0XHRpZiAoICEgKCAvXig/OmF8c2VsZWN0fGlucHV0fGJ1dHRvbnx0ZXh0YXJlYSkkL2kgKS50ZXN0KCBlbGVtZW50LnRhZ05hbWUgKSApIHtcblx0XHRcdFx0XHRlbGVtZW50LnRhYkluZGV4ID0gLTE7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRlbGVtZW50LmZvY3VzKCk7XG5cdFx0XHR9XG5cdFx0fSwgZmFsc2UgKTtcblx0fVxufSgpICk7XG4iLCIvKipcbiAqIEZpbGUgd2luZG93LXJlYWR5LmpzXG4gKlxuICogQWRkIGEgXCJyZWFkeVwiIGNsYXNzIHRvIDxib2R5PiB3aGVuIHdpbmRvdyBpcyByZWFkeS5cbiAqL1xud2luZG93Lndkc1dpbmRvd1JlYWR5ID0ge307XG4oIGZ1bmN0aW9uKCB3aW5kb3csICQsIGFwcCApIHtcblxuXHQvLyBDb25zdHJ1Y3Rvci5cblx0YXBwLmluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuY2FjaGUoKTtcblx0XHRhcHAuYmluZEV2ZW50cygpO1xuXHR9O1xuXG5cdC8vIENhY2hlIGRvY3VtZW50IGVsZW1lbnRzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHQnd2luZG93JzogJCggd2luZG93ICksXG5cdFx0XHQnYm9keSc6ICQoIGRvY3VtZW50LmJvZHkgKVxuXHRcdH07XG5cdH07XG5cblx0Ly8gQ29tYmluZSBhbGwgZXZlbnRzLlxuXHRhcHAuYmluZEV2ZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYy53aW5kb3cubG9hZCggYXBwLmFkZEJvZHlDbGFzcyApO1xuXHR9O1xuXG5cdC8vIEFkZCBhIGNsYXNzIHRvIDxib2R5Pi5cblx0YXBwLmFkZEJvZHlDbGFzcyA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYy5ib2R5LmFkZENsYXNzKCAncmVhZHknICk7XG5cdH07XG5cblx0Ly8gRW5nYWdlIVxuXHQkKCBhcHAuaW5pdCApO1xufSggd2luZG93LCBqUXVlcnksIHdpbmRvdy53ZHNXaW5kb3dSZWFkeSApICk7XG4iXX0=
