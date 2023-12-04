(function($) {

	function list_elements_displayed() {
		
		return $('.shortcode_list li').length
	}

	$(document).on( 'click', '.pbd-more', function( event ) {
		event.preventDefault();

		category = jQuery(this).data( 'category' );  
		total_posts = jQuery(this).data( 'total-posts' );  
		posts_per_page = jQuery(this).data('posts-per-page');

		date = jQuery(this).data('date');		


		// set the wp query to the current displayed posts
		post_count = $('.post_count')
		more_button = $('.pbd-more')

		if ( list_elements_displayed() + posts_per_page < total_posts)  {
			offset = list_elements_displayed()
			console.log(offset + 'hel')
		} else {
			offset = list_elements_displayed()
			posts_per_page = total_posts - list_elements_displayed()
			console.log(offset)
		}

		jQuery('.pbd-more').attr('disabled', true)

		$.ajax({
			url: ajax_js_object.ajaxurl,
			type: 'post',
			data: {
				action: 'pbd_ajax_pagination',
				number_of_posts: posts_per_page,
				category:category,
				offset : offset,
			},
			beforeSend: function() {

				$('.shortcode_list').append( '<div class="page-content" id="loader">Loading New Posts...</div>' );
			},
			success: function( data ) {
				
				$('.page-content').remove()

				$('.shortcode_list').append( data );

				post_count.text( $('.shortcode_list li').length ) 

				if ( total_posts <= list_elements_displayed() ) {
					more_button.remove()
				}

				jQuery('.pbd-more').attr('disabled', false)
			},
		})
	})
})(jQuery);

