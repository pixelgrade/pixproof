<?php

$settings = get_option('pixproof_settings');

if ( !isset($settings['enable_pixproof_gallery'] ) || $settings['enable_pixproof_gallery'] != true  ) return;

$single_label =  esc_html_x( 'Proof Gallery', 'Post Type Singular Name', 'pixproof' );
if ( isset($settings['pixproof_single_item_label']) && !empty( $settings['pixproof_single_item_label'] ) ) {
	$single_label = $settings['pixproof_single_item_label'];
}

$name = esc_html_x( 'Proof Galleries', 'Post Type General Name', 'pixproof' );
$menu_name = esc_html__( 'Proof Galleries', 'pixproof' );
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
	'parent_item_colon'   => esc_html__( 'Parent Item:', 'pixproof' ),
	'all_items'           => esc_html__( 'All Items', 'pixproof' ),
	'view_item'           => esc_html__( 'View Item', 'pixproof' ),
	'add_new_item'        => esc_html__( 'Add New Proof Gallery', 'pixproof' ),
	'add_new'             => esc_html__( 'Add New', 'pixproof' ),
	'edit_item'           => esc_html__( 'Edit Proof Gallery', 'pixproof' ),
	'update_item'         => esc_html__( 'Update Proof Gallery', 'pixproof' ),
	'search_items'        => esc_html__( 'Search Proof Galelry', 'pixproof' ),
	'not_found'           => esc_html__( 'Not found', 'pixproof' ),
	'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'pixproof' ),
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

