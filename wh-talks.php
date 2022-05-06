<?php
/**
 * Plugin Name: Talks
 * Description: Managing talks you gave.
 * Author: Florian Brinkmann
 * Author URI: https://florianbrinkmann.com/en/
 * Version: 0.1.0
 * Requires at least: 6.0
 * Requires PHP: 7.0
 * Text Domain: wh-talks
 */

namespace WH\Talks;

/**
 * Loads the textdomain to make the plugin translatable.
 *
 * @return void
 */
function load_textdomain() {
	load_plugin_textdomain(
		'wh-talks',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
add_action( 'init', __NAMESPACE__ . '\\load_textdomain' );

require_once 'inc/blocks.php';
require_once 'inc/cpt.php';
