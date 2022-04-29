<?php
/**
 * Plugin Name: Talks
 * Plugin Description: Managing talks you gave.
 * Plugin Author: Your name
 * Version: 0.1.0
 * Text Domain: wh-talks
 */

namespace WH\Talks;

function load_textdomain() {
	load_plugin_textdomain(
		'wh-talks',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
add_action( 'init', __NAMESPACE__ . '\\load_textdomain' );


function register_block() {
	register_block_type(
		__DIR__,
		[
			'render_callback' => __NAMESPACE__ . '\\render_block',
		]
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_block' );

function render_block( $attributes, $block_content ) {
	$markup = '';
	foreach ( get_meta_keys() as $meta_key => $label ) {
		$meta_value = get_post_meta( get_the_ID(), $meta_key, true ) ?? null;
		if ( null === $meta_value ) {
			continue;
		}
		$markup .= sprintf(
			'<li><span class="wh-talks-meta-label">%s:</span> %s</li>',
			$label,
			$meta_value
		);
	}

	if ( '' === $markup ) {
		return '';
	}

	return str_replace( '</ul>', "$markup</ul>", $block_content );
}

function get_meta_keys() {
	return [
		'wh_talks_event_name' => __( 'Event', 'wh-talks' ),
		'wh_talks_language'   => __( 'Language', 'wh-talks' ),
		'wh_talks_duration'   => __( 'Duration', 'wh-talks' ),
	];
}

require_once 'inc/cpt.php';
