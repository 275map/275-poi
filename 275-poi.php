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

$map = new \Miya\WP\Custom_Field\Geometry( 'geo', 'Map', array( 'priority' => 'high' ) );
$map->add( 'map' );
