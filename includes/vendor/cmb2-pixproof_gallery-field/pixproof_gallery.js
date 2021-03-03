(function($){
	$(window).on('load', function(){

    $('#proof_pixgallery').each(function() {
      var instance = this;

      $('.open_proof_pixgallery', instance).on('click',function(e){
        var galleries_ids = $('#pixgalleries').val(),
          random_order =  $('#pixgalleries_random').val(),
          columns =  $('#pixgalleries_columns').val(),
          size =  $('#pixgalleries_size').val(),
          defaultPostId = wp.media.gallery.defaults.id,
          attachments, selection;

        if (random_order === 'true' ) {
          random_order = ' orderby="rand"';
        } else {
          random_order = ' orderby="title"';
        }

        if (columns) {
          columns = ' columns="'+columns+'"';
        }
        if (size) {
          size = ' size="'+size+'"';
        }

        var gallerysc = '[gallery'+columns+''+size+' ids="'+ galleries_ids +'"'+ random_order +']';

        wp.media.gallery.edit(gallerysc).on('update', function(g) {
          var id_array = [];
          $.each(g.models, function(id, img) { id_array.push(img.id); });
          $('#pixgalleries').val( id_array.join(',') );

          if ( g.gallery.attributes._orderbyRandom ) {
            $('#pixgalleries_random').val('true');
          } else {
            $('#pixgalleries_random').val('false');
          }

          if ( g.gallery.attributes.columns ) {
            $('#pixgalleries_columns').val(g.gallery.attributes.columns);
          }

          if ( g.gallery.attributes.size ) {
            $('#pixgalleries_size').val(g.gallery.attributes.size);
          }

          // update the gallery_preview
          proof_pixgallery_ajax_preview();
          return false;
        });
      });
    });

		proof_pixgallery_ajax_preview();

	});

	var proof_pixgallery_ajax_preview = function(){
		$.ajax({
			type: "post",url: locals.ajax_url,data: { action: 'ajax_proof_pixgallery_preview', attachments_ids: $('#pixgalleries').val() },
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

})(jQuery);
