<?php
/**
 * Plugin Name:     275-poi
 * Plugin URI:      https://github.com/ogijima-library/geonic
 * Description:     A WordPress plugin that manages geometries.
 * Author:          Takayuki Miyauchi
 * Author URI:      https://miya.io/
 * Text Domain:     geonic
 * Domain Path:     /languages
 * Version:         nightly
 *
 * @package         Geonic
 */

// Autoload
require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );

add_action( 'init', function() {
	$plugin_slug = plugin_basename( __FILE__ ); // e.g. `hello/hello.php`.
	$gh_user = '275map';                      // The user name of GitHub.
	$gh_repo = '275-poi';       // The repository name of your plugin.

	// Activate automatic update.
	new \Miya\WP\GH_Auto_Updater( $plugin_slug, $gh_user, $gh_repo );
} );

$map = new \Miya\WP\Custom_Field\Map( 'poi', 'Map', array(
	'priority' => 'high',
	'tiles' => array(
		array(
			"name" => "Open Street Map",
			"tile" => "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
			"attribution" => "OpenStreetMap Contributers",
			"attribution_url" => "http://osm.org/copyright"
		),
		array(
			"name" => "国土地理院 (標準)",
			"tile" => "https://cyberjapandata.gsi.go.jp/xyz/std/{z}/{x}/{y}.png",
			"attribution" => "国土地理院",
			"attribution_url" => "http://osm.org/copyright"
		),
		array(
			"name" => "国土地理院 (オルソ画像)",
			"tile" => "https://cyberjapandata.gsi.go.jp/xyz/ort/{z}/{x}/{y}.jpg",
			"attribution" => "国土地理院",
			"attribution_url" => "http://osm.org/copyright"
		)
	),
) );
$map->add( 'poi' );

$marker = new Color_Marker( 'marker-color', 'Marker' );
$marker->add( 'poi' );

$geometry = new \Miya\WP\Custom_Field\Geometry( 'geo', 'Map', array(
	'priority' => 'high',
	'lat' => 0,
	'lng' => 0,
	'zoom' => 1,
	'tiles' => array(
		array(
			"name" => "Open Street Map",
			"tile" => "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
			"attribution" => "OpenStreetMap Contributers",
			"attribution_url" => "http://osm.org/copyright"
		),
		array(
			"name" => "国土地理院 (標準)",
			"tile" => "https://cyberjapandata.gsi.go.jp/xyz/std/{z}/{x}/{y}.png",
			"attribution" => "国土地理院",
			"attribution_url" => "http://osm.org/copyright"
		),
		array(
			"name" => "国土地理院 (オルソ画像)",
			"tile" => "https://cyberjapandata.gsi.go.jp/xyz/ort/{z}/{x}/{y}.jpg",
			"attribution" => "国土地理院",
			"attribution_url" => "http://osm.org/copyright"
		)
	),
	'controls' => array(
		'circle' => false,
		'circlemarker' => false,
		'marker' => false,
	),
) );
$geometry->add( 'map' );

add_action( 'wp_enqueue_scripts', 'poi_load_js' );

add_filter( 'the_content', function( $content ) {
	if ( 'poi' === get_post_type() ) {
		$content .= poi_get_single_map( get_the_ID() ) . poi_get_street_view( get_the_ID() );
	} elseif ( 'map' === get_post_type() ) {
		$cats = '<div class="term-checkboxes">' . poi_get_terms( get_the_ID() ) . '</div>';
		$map = poi_get_map( get_the_ID() );
		$content = $cats . $map . $content;
	}

	return $content;
} );
