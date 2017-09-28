<?php

function map_init() {
	register_post_type( 'map', array(
		'labels'            => array(
			'name'                => __( 'Maps', '275-poi' ),
			'singular_name'       => __( 'Map', '275-poi' ),
			'all_items'           => __( 'All Maps', '275-poi' ),
			'new_item'            => __( 'New map', '275-poi' ),
			'add_new'             => __( 'Add New', '275-poi' ),
			'add_new_item'        => __( 'Add New map', '275-poi' ),
			'edit_item'           => __( 'Edit map', '275-poi' ),
			'view_item'           => __( 'View map', '275-poi' ),
			'search_items'        => __( 'Search maps', '275-poi' ),
			'not_found'           => __( 'No maps found', '275-poi' ),
			'not_found_in_trash'  => __( 'No maps found in trash', '275-poi' ),
			'parent_item_colon'   => __( 'Parent map', '275-poi' ),
			'menu_name'           => __( 'Maps', '275-poi' ),
		),
		'public'            => true,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'supports'          => array( 'title', 'editor' ),
		'has_archive'       => true,
		'rewrite'           => true,
		'query_var'         => true,
		'menu_icon'         => 'dashicons-admin-post',
		'show_in_rest'      => true,
		'rest_base'         => 'map',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	) );

}
add_action( 'init', 'map_init' );

function map_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['map'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Map updated. <a target="_blank" href="%s">View map</a>', '275-poi'), esc_url( $permalink ) ),
		2 => __('Custom field updated.', '275-poi'),
		3 => __('Custom field deleted.', '275-poi'),
		4 => __('Map updated.', '275-poi'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Map restored to revision from %s', '275-poi'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Map published. <a href="%s">View map</a>', '275-poi'), esc_url( $permalink ) ),
		7 => __('Map saved.', '275-poi'),
		8 => sprintf( __('Map submitted. <a target="_blank" href="%s">Preview map</a>', '275-poi'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Map scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview map</a>', '275-poi'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Map draft updated. <a target="_blank" href="%s">Preview map</a>', '275-poi'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'map_updated_messages' );
