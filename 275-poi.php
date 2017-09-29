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

$map = new \Miya\WP\Custom_Field\Map( 'poi', 'Marker', array( 'priority' => 'high' ) );
$map->add( 'poi' );

$map = new \Miya\WP\Custom_Field\Geometry( 'geo', 'Map', array(
	'priority' => 'high',
	'lat' => 0,
	'lng' => 0,
	'zoom' => 1,
	'layers' => array(
		array(
			'name' => 'Open Street Map',
			'tile' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
			'attribution' => 'OpenStreetMap Contributers',
			'attribution_url' => 'http://osm.org/copyright',
		),
	),
	'controls' => array(
		'circle' => false,
		'circlemarker' => false,
		'marker' => false,
	),
) );
$map->add( 'map' );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script(
		'riot',
		plugins_url( 'lib/riot/riot+compiler.min.js', __FILE__ ),
		array(),
		false,
		true
	);
	wp_enqueue_script(
		'leaflet',
		plugins_url( 'lib/leaflet/dist/leaflet.js', __FILE__ ),
		array(),
		false,
		true
	);
	wp_enqueue_script(
		'app',
		plugins_url( 'js/app.js', __FILE__ ),
		array( 'jquery', 'riot', 'leaflet' ),
		false,
		true
	);
	wp_enqueue_style(
		'leaflet',
		plugins_url( 'lib/leaflet/dist/leaflet.css', __FILE__ ),
		array(),
		false
	);
 } );

 add_filter( 'the_content', function( $content ) {
	if ( 'poi' === get_post_type() ) {
		$meta = get_post_meta( get_the_ID(), 'poi', true );
		$path = plugins_url( 'tags', __FILE__ );
		$content .=<<<EOL
			<div><street-view data-lat="{$meta['lat']}"
					data-lng="{$meta['lng']}" data-key="AIzaSyCLl8lQB-ooWkYTvhTlgh5A393rSivVcwk"></street-view></div>
			<div style="width: 100%; height: 300px;"><osm data-lat="{$meta['lat']}" data-lng="{$meta['lng']}"
					data-zoom="{$meta['zoom']}"></osm></div>
			<script src="{$path}/street-view.tag" type="riot/tag"></script>
			<script src="{$path}/osm.tag" type="riot/tag"></script>
EOL;
	}

	return $content;
 } );
