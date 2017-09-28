<?php

function poi_category_init() {
	register_taxonomy( 'poi-category', array( 'poi' ), array(
		'hierarchical'      => true,
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
			'name'                       => __( 'POI categories', '275-poi' ),
			'singular_name'              => _x( 'POI category', 'taxonomy general name', '275-poi' ),
			'search_items'               => __( 'Search POI categories', '275-poi' ),
			'popular_items'              => __( 'Popular POI categories', '275-poi' ),
			'all_items'                  => __( 'All POI categories', '275-poi' ),
			'parent_item'                => __( 'Parent POI category', '275-poi' ),
			'parent_item_colon'          => __( 'Parent POI category:', '275-poi' ),
			'edit_item'                  => __( 'Edit POI category', '275-poi' ),
			'update_item'                => __( 'Update POI category', '275-poi' ),
			'add_new_item'               => __( 'New POI category', '275-poi' ),
			'new_item_name'              => __( 'New POI category', '275-poi' ),
			'separate_items_with_commas' => __( 'Separate POI categories with commas', '275-poi' ),
			'add_or_remove_items'        => __( 'Add or remove POI categories', '275-poi' ),
			'choose_from_most_used'      => __( 'Choose from the most used POI categories', '275-poi' ),
			'not_found'                  => __( 'No POI categories found.', '275-poi' ),
			'menu_name'                  => __( 'POI categories', '275-poi' ),
		),
		'show_in_rest'      => true,
		'rest_base'         => 'poi-category',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	) );

}
add_action( 'init', 'poi_category_init' );

function poi_category_rewrite_flush() {
    poi_category_init();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'poi_category_rewrite_flush' );
