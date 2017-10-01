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
	$meta = get_post_meta( $post_id, 'poi', true );
	if ( ! $meta ) {
		return;
	}

	$marker_color = get_post_meta( $post_id, 'marker-color', true );
	if ( ! $marker_color ) {
		$marker_color = 'blue';
	}
	$images = Color_Marker::icon_images();

	$marker = esc_url( $images[$marker_color] );
	$path = esc_url( plugins_url( 'tags', dirname( __FILE__ ) ) );
	$lat = esc_attr( $meta['lat'] );
	$lng = esc_attr( $meta['lng'] );
	$zoom = esc_attr( $meta['zoom'] );

	$content =<<<EOL
		<div style="width: 100%; height: 300px; margin: 1em 0;"><osm data-lat="{$lat}" data-lng="{$lng}"
				data-zoom="{$zoom}" data-marker="{$marker}"></osm></div>
		<script src="{$path}/osm.tag" type="riot/tag"></script>
EOL;
	return $content;
}

function poi_get_map( $post_id ) {
	global $geometry;
	return $geometry->get_map( $post_id );
}

function poi_get_street_view( $post_id ) {
	$meta = get_post_meta( $post_id, 'poi', true );
	if ( ! $meta ) {
		return;
	}

	$path = esc_url( plugins_url( 'tags', dirname( __FILE__ ) ) );
	$lat = esc_attr( $meta['lat'] );
	$lng = esc_attr( $meta['lng'] );

	$map =<<<EOL
	<div style="margin: 1em 0;"><street-view data-lat="{$lat}"
	data-lng="{$lng}" data-key="AIzaSyCLl8lQB-ooWkYTvhTlgh5A393rSivVcwk"></street-view></div>
	<script src="{$path}/street-view.tag" type="riot/tag"></script>
EOL;

	return $map;
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
			$post_id = url_to_postid( $url );
			if ( is_admin() ) {
				return '<div style="width: 100%; background-color: #f5f5f5; text-align: center; padding: 40px 0;"><img src="https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-grey.png"><br>' . esc_html( get_the_title( $post_id ) ).'</div>';
			} else {
				return poi_get_map( $post_id );
			}
		}
	 );

	 wp_embed_register_handler(
		'poi',
		'#' . home_url(). '/archives/poi/.*/?$#i',
		function( $m, $attr, $url, $rattr ) {
			$post_id = url_to_postid( $url );
			if ( is_admin() ) {
				return '<div style="width: 100%; background-color: #f5f5f5; text-align: center; padding: 40px 0;"><img src="https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-grey.png"><br>' . esc_html( get_the_title( $post_id ) ).'</div>';
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