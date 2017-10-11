<?php

function map_tag_init() {
	register_taxonomy( 'map-tag', array( 'map' ), array(
		'hierarchical'      => false,
		'public'            => true,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'show_admin_column' => false,
		'query_var'         => true,
		'rewrite'           => true,
		'capabilities'      => array(
			'manage_terms'  => 'edit_posts',
			'edit_terms'    => 'edit_posts',
			'delete_terms'  => 'edit_posts',
			'assign_terms'  => 'edit_posts'
		),
		'labels'            => array(
			'name'                       => __( 'Map tags', '275-poi' ),
			'singular_name'              => _x( 'Map tags', 'taxonomy general name', '275-poi' ),
			'search_items'               => __( 'Search map tags', '275-poi' ),
			'popular_items'              => __( 'Popular map tags', '275-poi' ),
			'all_items'                  => __( 'All map tags', '275-poi' ),
			'parent_item'                => __( 'Parent map tag', '275-poi' ),
			'parent_item_colon'          => __( 'Parent map tag:', '275-poi' ),
			'edit_item'                  => __( 'Edit map tag', '275-poi' ),
			'update_item'                => __( 'Update map tag', '275-poi' ),
			'add_new_item'               => __( 'New map tag', '275-poi' ),
			'new_item_name'              => __( 'New map tag', '275-poi' ),
			'separate_items_with_commas' => __( 'Separate map tags with commas', '275-poi' ),
			'add_or_remove_items'        => __( 'Add or remove map tags', '275-poi' ),
			'choose_from_most_used'      => __( 'Choose from the most used map tags', '275-poi' ),
			'not_found'                  => __( 'No map tags found.', '275-poi' ),
			'menu_name'                  => __( 'Map tags', '275-poi' ),
		),
		'show_in_rest'      => true,
		'rest_base'         => 'map-category',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	) );

}
add_action( 'init', 'map_tag_init' );

function map_tag_rewrite_flush() {
    map_tag_init();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'map_tag_rewrite_flush' );
