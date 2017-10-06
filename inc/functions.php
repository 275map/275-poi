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
				$images = Color_Marker::icon_images();
				$tmeta = get_term_meta( $object['poi-category'][0], '__color', true );
				if ( ! $tmeta ) {
					$tmeta = 'blue';
				}
				$marker = esc_url( $images[ $tmeta ] );

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
	$terms = get_the_terms( $post_id, 'poi-category' );
	$color = get_term_meta( $terms[0]->term_id, '__color', true );
	if ( empty( $color ) ) {
		$color = 'blue';
	}
	$markers = Color_Marker::icon_images();
	$marker = $markers[ $color ];
	$script = '<script>var marker_color = "'.esc_url( $marker ).'";</script>';
	return '<div class="single-map" data-marker="' . esc_url( $marker ) . '">'
			 . $GLOBALS['map']->get_map( $post_id ) . '</div>';
}

function poi_get_map( $post_id ) {
	return $GLOBALS['geometry']->get_map( $post_id );
}

function poi_get_street_view( $post_id ) {
	if ( ! defined( 'G_API_KEY' ) ) {
		return;
	}

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

add_action( 'init', function() {
	wp_embed_register_handler(
		'maps',
		'#' . untrailingslashit( get_post_type_archive_link( 'map' ) ) . '/.+$#',
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
		'#' . untrailingslashit( get_post_type_archive_link( 'poi' ) ) . '/.+$#',
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
		'leaflet-fullscreen',
		'https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js',
		array( 'jquery', 'custom-field-geometry' ),
		false,
		true
	);
	wp_enqueue_script(
		'app',
		plugins_url( 'js/app.js', dirname( __FILE__ ) ),
		array( 'jquery', 'leaflet-fullscreen', 'custom-field-geometry' ),
		false,
		true
	);

	wp_enqueue_style( 'leaflet' );
	wp_enqueue_style(
		'275-poi',
		plugins_url( 'css/style.css', dirname( __FILE__ ) ),
		array( 'leaflet' ),
		false
	);
	wp_enqueue_style(
		'leaflet-fullscreen',
		'https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css',
		array( 'leaflet' ),
		false
	);
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

if ( ! function_exists( 'wp_new_user_notification' ) ) {
function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
	if ( $deprecated !== null ) {
		_deprecated_argument( __FUNCTION__, '4.3.1' );
	}

	global $wpdb, $wp_hasher;
	$user = get_userdata( $user_id );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	if ( 'user' !== $notify ) {
		$switched_locale = switch_to_locale( get_locale() );
		$message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
		$message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";

		@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}

	// `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
	if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
		return;
	}

	// Generate something random for a password reset key.
	$key = wp_generate_password( 20, false );

	/** This action is documented in wp-login.php */
	do_action( 'retrieve_password_key', $user->user_login, $key );

	// Now insert the key, hashed, into the DB.
	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

	$switched_locale = switch_to_locale( get_user_locale( $user ) );

	$message = "草津と近江八幡の「まちづくりマップ」が統合されて新しくなりましたので、メールの指示に従って、ログインしてください。なお、古い「まちづくりマップ」も、年末までは公開しており、順次データを移管しますので、よろしくお願いします。\r\n";
	$message .= home_url() . "/\r\n\r\n";

	$message .= sprintf(__('ユーザー名: %s'), $user->user_login) . "\r\n\r\n";
	$message .= __('パスワードを設定するには以下のアドレスにアクセスしてください。') . "\r\n";
	$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

	$message .= "パスワードの設定が完了したら以下の URL からログインしてください。\r\n";
	$message .= wp_login_url() . "\r\n";

	wp_mail($user->user_email, sprintf(__('[%s] ユーザー登録のご案内について'), $blogname), $message);

	if ( $switched_locale ) {
		restore_previous_locale();
	}
}
}
