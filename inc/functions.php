<?php


add_action( 'rest_api_init', 'rest_api_filter_add_filters' );
/**
 * Add the necessary filter to each post type
 **/
function rest_api_filter_add_filters() {
   foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
	   add_filter( 'rest_' . $post_type->name . '_query', 'rest_api_filter_add_filter_param', 10, 2 );
   }
}
/**
* Add the filter parameter
*
* @param  array           $args    The query arguments.
* @param  WP_REST_Request $request Full details about the request.
* @return array $args.
**/
function rest_api_filter_add_filter_param( $args, $request ) {
   // Bail out if no filter parameter is set.
   if ( empty( $request['filter'] ) || ! is_array( $request['filter'] ) ) {
	   return $args;
   }
   $filter = $request['filter'];
   if ( isset( $filter['posts_per_page'] ) && ( (int) $filter['posts_per_page'] >= 1 && (int) $filter['posts_per_page'] <= 100 ) ) {
	   $args['posts_per_page'] = $filter['posts_per_page'];
   }
   global $wp;
   $vars = apply_filters( 'query_vars', $wp->public_query_vars );
   foreach ( $vars as $var ) {
	   if ( isset( $filter[ $var ] ) ) {
		   $args[ $var ] = $filter[ $var ];
	   }
   }
   return $args;
}

add_action( 'rest_api_init', function() {
	register_rest_field( 'poi', 'poi', array(
		'get_callback' => function( $object ) {
			$meta = get_post_meta( $object['id'], 'poi', true );
			$marker_color = get_post_meta( get_the_ID(), 'marker-color', true );
			if ( ! $marker_color ) {
				$marker_color = 'blue';
			}
			$images = Color_Marker::icon_images();
			$marker = esc_url( $images[$marker_color] );

			return array(
				'lat' => $meta['lat'],
				'lng' => $meta['lng'],
				'zoom' => $meta['zoom'],
				'marker' => $marker,
			);
		},
		'schema' => null,
		)
	);
} );

function poi_get_single_map( $post_id ) {
	return $GLOBALS['map']->get_map( $post_id );
}

function poi_get_map( $post_id ) {
	return $GLOBALS['geometry']->get_map( $post_id );
}

function poi_get_street_view( $post_id ) {
	$meta = get_post_meta( $post_id, 'poi', true );
	if ( ! $meta ) {
		return;
	}

	$map = '<div style="margin: 1em 0;">
			<iframe src="%s" width="100%" height="450" frameborder="0"
			style="border:0" allowfullscreen></iframe></div>';

	$base = 'https://www.google.com/maps/embed/v1/streetview';
	$params = '?key=' . G_API_KEY . '&location=' . $meta['lat'] . ',' . $meta['lng'];
	return str_replace( '%s', esc_url( $base . $params ), $map );
}

add_shortcode( 'poi', function( $p ) {
	$post_id = intval( $p['id'] );
	return poi_get_map( $post_id );
} );

add_action( 'plugins_loaded', function() {
	wp_embed_register_handler(
		'pois',
		'#' . home_url(). '/archives/map/.*/?$#i',
		function( $m, $attr, $url, $rattr ) {
			$image = plugins_url( 'img/map.png', dirname( __FILE__ ) );
			$post_id = url_to_postid( $url );
			if ( is_admin() ) {
				return '<div style="border: 1px solid #eeeeee; width: 100%; background-position: center center; background-image: url(' . $image . '); background-size: cover; text-align: center; padding: 50px 0;">' . esc_html( get_the_title( $post_id ) ).'</div>';
			} else {
				return poi_get_map( $post_id );
			}
		}
	 );

	 wp_embed_register_handler(
		'poi',
		'#' . home_url(). '/archives/poi/.*/?$#i',
		function( $m, $attr, $url, $rattr ) {
			$image = plugins_url( 'img/map.png', dirname( __FILE__ ) );
			$post_id = url_to_postid( $url );
			if ( is_admin() ) {
				return '<div style="border: 1px solid #eeeeee; width: 100%; background-position: center center; background-image: url(' . $image . '); background-size: cover; text-align: center; padding: 50px 0;">' . esc_html( get_the_title( $post_id ) ).'</div>';
			} else {
				$post_id = url_to_postid( $url );
				return poi_get_single_map( $post_id );
			}
		}
	 );
} );

function poi_load_js() {
	wp_enqueue_script(
		'app',
		plugins_url( 'js/app.js', dirname( __FILE__ ) ),
		array( 'jquery', 'custom-field-geometry' ),
		false,
		true
	);

	wp_enqueue_style( 'leaflet' );
}

function poi_get_terms( $post_id ) {
	$terms = get_the_terms( $post_id, 'poi-category' );
	if ( $terms ) {
		$item = array();
		foreach ( $terms as $term ) {
			$item[] = sprintf(
				'<label><input type="checkbox" value="%1$s" checked> %2$s</label>',
				esc_attr( $term->slug ),
				esc_html( $term->name )
			);
		}

		return sprintf( '<div id="poi-cats">%s</div>', join( '&nbsp;&nbsp;', $item ) );
	}
}
