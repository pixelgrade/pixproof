<?php

$settings = get_option('pixproof_settings');

if ( !isset($settings['enable_pixproof_gallery'] ) || $settings['enable_pixproof_gallery'] != true  ) return;

$single_label =  _x( 'Proof Gallery', 'Post Type Singular Name', 'pixproof_txtd' );
if ( isset($settings['pixproof_single_item_label']) && !empty( $settings['pixproof_single_item_label'] ) ) {
	$single_label = $settings['pixproof_single_item_label'];
}

$name = _x( 'Proof Galleries', 'Post Type General Name', 'pixproof_txtd' );
$menu_name = __( 'Proof Galleries', 'pixproof_txtd' );
if ( isset($settings['pixproof_multiple_items_label']) && !empty( $settings['pixproof_multiple_items_label'] ) ) {
	$name = $menu_name = $settings['pixproof_multiple_items_label'];
}

$slug = 'proof_gallery';
if ( isset($settings['pixproof_change_single_item_slug']) && ( $settings['pixproof_change_single_item_slug'] ) && !empty($settings['pixproof_gallery_new_single_item_slug']) ) {
	$slug = $settings['pixproof_gallery_new_single_item_slug'];
}

$rewrite = array( 'slug' => $slug);

$labels = array(
	'name'                => $name,
	'singular_name'       => $single_label,
	'menu_name'           => $menu_name,
	'parent_item_colon'   => __( 'Parent Item:', 'pixproof_txtd' ),
	'all_items'           => __( 'All Items', 'pixproof_txtd' ),
	'view_item'           => __( 'View Item', 'pixproof_txtd' ),
	'add_new_item'        => __( 'Add New Proof Gallery', 'pixproof_txtd' ),
	'add_new'             => __( 'Add New', 'pixproof_txtd' ),
	'edit_item'           => __( 'Edit Proof Gallery', 'pixproof_txtd' ),
	'update_item'         => __( 'Update Proof Gallery', 'pixproof_txtd' ),
	'search_items'        => __( 'Search Proof Galelry', 'pixproof_txtd' ),
	'not_found'           => __( 'Not found', 'pixproof_txtd' ),
	'not_found_in_trash'  => __( 'Not found in Trash', 'pixproof_txtd' ),
);

$args = array(
	'label'               => $single_label,
	'description'         => $menu_name,
	'labels'              => $labels,
	'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'revisions', 'page-attributes', ),
//	'taxonomies'          => array( 'category', 'post_tag' ),
	'hierarchical'        => true,
	'public'              => true,
	'show_ui'             => true,
	'show_in_menu'        => true,
	'show_in_nav_menus'   => true,
	'show_in_admin_bar'   => true,
	'menu_position'       => NULL,
	'menu_icon'           => 'dashicons-visibility',
	'can_export'          => true,
	'has_archive'         => false,
	'exclude_from_search' => true,
	'publicly_queryable'  => true,
	'query_var'           => $slug,
	'rewrite'                => $rewrite,
	'capability_type'     => 'page',
	'yarpp_support' => false,
);
register_post_type( 'proof_gallery', $args );

