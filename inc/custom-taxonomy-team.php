<?php

function team_init() {
	register_taxonomy( 'team', array( 'poi', 'map' ), array(
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
			'name'                       => __( 'Teams', '275-poi' ),
			'singular_name'              => _x( 'Team', 'taxonomy general name', '275-poi' ),
			'search_items'               => __( 'Search teams', '275-poi' ),
			'popular_items'              => __( 'Popular teams', '275-poi' ),
			'all_items'                  => __( 'All teams', '275-poi' ),
			'parent_item'                => __( 'Parent team', '275-poi' ),
			'parent_item_colon'          => __( 'Parent team:', '275-poi' ),
			'edit_item'                  => __( 'Edit team', '275-poi' ),
			'update_item'                => __( 'Update team', '275-poi' ),
			'add_new_item'               => __( 'New team', '275-poi' ),
			'new_item_name'              => __( 'New team', '275-poi' ),
			'separate_items_with_commas' => __( 'Separate teams with commas', '275-poi' ),
			'add_or_remove_items'        => __( 'Add or remove teams', '275-poi' ),
			'choose_from_most_used'      => __( 'Choose from the most used teams', '275-poi' ),
			'not_found'                  => __( 'No teams found.', '275-poi' ),
			'menu_name'                  => __( 'Teams', '275-poi' ),
		),
		'show_in_rest'      => true,
		'rest_base'         => 'team',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	) );

}
add_action( 'init', 'team_init' );
