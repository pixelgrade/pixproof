<?php
/* Filter the single_template with our custom function*/

// not now
// add_filter('single_template', 'my_custom_template');

function my_custom_template($single) {
	global $wp_query, $post;



	/* Checks for single template by post type */
	if ($post->post_type == "proof_gallery"){

		if(file_exists(plugin_dir_path( __FILE__ ). '/single-proof_gallery.php')) {

			return plugin_dir_path( __FILE__ ) . '/single-proof_gallery.php';
		}



	}
	return $single;
}
