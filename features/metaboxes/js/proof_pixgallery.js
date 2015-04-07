(function($){
	$(window).load(function(){

		wp.media.EditPixGallery = {

			frame: function() {
				if ( this._frame )
					return this._frame;
				var selection = this.select();
				// create our own media iframe
				this._frame = wp.media({
					displaySettings:    false,
					id:                 'proof_pixgallery-frame',
					title:              'PixGallery',
					filterable:         'uploaded',
					frame:              'post',
					state:              'gallery-edit',
					library:            { type : 'image' },
					multiple:           true,  // Set to true to allow multiple files to be selected
					editing:            true,
					selection:          selection
				});

				// on update send our attachments ids into a post meta field
				this._frame.on( 'update',
					function() {
						var controller = wp.media.EditPixGallery._frame.states.get('gallery-edit');
						var library = controller.get('library');
						// Need to get all the attachment ids for gallery
						var ids = library.pluck('id'),
							gallery = library.gallery;

						$('#pixgalleries').val( ids.join(',') );

						if ( gallery.attributes._orderbyRandom ) {
							$('#pixgalleries_random').val('true');
						}

						if ( gallery.attributes.columns ) {
							$('#pixgalleries_columns').val(gallery.attributes.columns);
						}

						if ( gallery.attributes.size ) {
							$('#pixgalleries_size').val(gallery.attributes.size);
						}

						// update the galllery_preview
						proof_pixgallery_ajax_preview();

						return false;
					});

				return this._frame;
			},

			init: function() {

				$('#proof_pixgallery').on('click', '.open_proof_pixgallery', function(e){
					e.preventDefault();
					wp.media.EditPixGallery.frame().open();
				});
			},

			select: function(){
				var galleries_ids = $('#pixgalleries').val(),
					random_order =  $('#pixgalleries_random').val(),
					columns =  $('#pixgalleries_columns').val(),
					size =  $('#pixgalleries_size').val(),
					defaultPostId = wp.media.gallery.defaults.id,
					attachments, selection;

				if (random_order) {
					random_order = ' orderby="rand"';
				}

				if (columns) {
					columns = ' columns="'+columns+'"';
				}
				if (size) {
					size = ' size="'+size+'"';
				}

				var shortcode = wp.shortcode.next( 'gallery', '[gallery'+columns+''+size+' ids="'+ galleries_ids +'"'+ random_order +']' );
				// Bail if we didn't match the shortcode or all of the content.
				if ( ! shortcode )
					return;

				// Ignore the rest of the match object.
				shortcode = shortcode.shortcode;

				if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) )
					shortcode.set( 'id', defaultPostId );

				attachments = wp.media.gallery.attachments( shortcode );
				selection = new wp.media.model.Selection( attachments.models, {
					props:    attachments.props.toJSON(),
					multiple: true
				});

				selection.gallery = attachments.gallery;

				// Fetch the query's attachments, and then break ties from the
				// query to allow for sorting.
				selection.more().done( function() {
					// Break ties with the query.
					selection.props.set({ query: false });
					selection.unmirror();
					selection.props.unset('orderby');
				});

				return selection;
			}
		};

		proof_pixgallery_ajax_preview();
		$( wp.media.EditPixGallery.init );

	});

	var proof_pixgallery_ajax_preview = function(){

		var ids = '';
		ids = $('#pixgalleries').val();

		$.ajax({
			type: "post",url: locals.ajax_url,data: { action: 'ajax_proof_pixgallery_preview', attachments_ids: ids },
			beforeSend: function() {
				$('.open_proof_pixgallery i').removeClass('dashicons-images-alt');
				$('.open_proof_pixgallery i').addClass('dashicons-update');
			}, //show loading just when link is clicked
			complete: function() {
				$('.open_proof_pixgallery i').removeClass('dashicons-update');
				$('.open_proof_pixgallery i').addClass('dashicons-images-alt');
			}, //stop showing loading when the process is complete
			success: function( response ){
				var result = JSON.parse(response);
				if (result.success ) {
					$('#proof_pixgallery > ul').html(result.output);
				}
			}
		});
	};

	//init
	if ($("[id$='_post_slider_visiblenearby']").val() == 1) {
		//we need to hide the transition because it will not be used
		$("[id$='_post_slider_transition']").closest('tr').hide();
	}

	$("[id$='_post_slider_visiblenearby']").on('change', function() {
		if ($(this).val() == 1) {
			//we need to hide the transition because it will not be used
			$("[id$='_post_slider_transition']").closest('tr').fadeOut();
		} else {
			$("[id$='_post_slider_transition']").closest('tr').fadeIn();
		}
	});

	//for the autoplay
	//init
	if ($("[id$='_post_slider_autoplay']").val() != 1) {
		//we need to hide the delay because it will not be used
		$("[id$='_post_slider_delay']").closest('tr').hide();
	}

	$("[id$='_post_slider_autoplay']").on('change', function() {
		if ($(this).val() == 1) {
			//we need to hide the delay because it will not be used
			$("[id$='_post_slider_delay']").closest('tr').fadeIn();
		} else {
			$("[id$='_post_slider_delay']").closest('tr').fadeOut();
		}
	});

})(jQuery);
