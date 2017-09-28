<?php

function poi_init() {
	register_post_type( 'poi', array(
		'labels'            => array(
			'name'                => __( 'POIs', '275-poi' ),
			'singular_name'       => __( 'POI', '275-poi' ),
			'all_items'           => __( 'All POIs', '275-poi' ),
			'new_item'            => __( 'New POI', '275-poi' ),
			'add_new'             => __( 'Add New', '275-poi' ),
			'add_new_item'        => __( 'Add New POI', '275-poi' ),
			'edit_item'           => __( 'Edit POI', '275-poi' ),
			'view_item'           => __( 'View POI', '275-poi' ),
			'search_items'        => __( 'Search POIs', '275-poi' ),
			'not_found'           => __( 'No POIs found', '275-poi' ),
			'not_found_in_trash'  => __( 'No POIs found in trash', '275-poi' ),
			'parent_item_colon'   => __( 'Parent POI', '275-poi' ),
			'menu_name'           => __( 'POIs', '275-poi' ),
		),
		'public'            => true,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'supports'          => array( 'title', 'editor', 'thumbnail', 'author' ),
		'has_archive'       => false,
		'rewrite'           => true,
		'query_var'         => true,
		'menu_icon'         => 'dashicons-admin-post',
		'show_in_rest'      => true,
		'rest_base'         => 'poi',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	) );

}
add_action( 'init', 'poi_init' );

function poi_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['POI'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('POI updated. <a target="_blank" href="%s">View POI</a>', '275-poi'), esc_url( $permalink ) ),
		2 => __('Custom field updated.', '275-poi'),
		3 => __('Custom field deleted.', '275-poi'),
		4 => __('POI updated.', '275-poi'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('POI restored to revision from %s', '275-poi'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('POI published. <a href="%s">View POI</a>', '275-poi'), esc_url( $permalink ) ),
		7 => __('POI saved.', '275-poi'),
		8 => sprintf( __('POI submitted. <a target="_blank" href="%s">Preview POI</a>', '275-poi'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('POI scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview POI</a>', '275-poi'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('POI draft updated. <a target="_blank" href="%s">Preview POI</a>', '275-poi'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'poi_updated_messages' );
