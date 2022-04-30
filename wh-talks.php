<?php
/**
 * Plugin Name: Talks
 * Plugin Description: Managing talks you gave.
 * Plugin Author: Your name
 * Version: 0.1.0
 * Text Domain: wh-talks
 */

namespace WH\Talks;

use WP_Block_Type_Registry;

/**
 * Lodas the textdomain to make the plugin translatable.
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

/**
 * Registers the block talk meta block.
 *
 * @return void
 */
function register_block() {
	register_block_type(
		__DIR__,
		[
			'render_callback' => __NAMESPACE__ . '\\render_block',
		]
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_block' );

/**
 * Renders the talk meta block on the frontend.
 *
 * @param array  $attributes The block attributes.
 * @param string $block_content The block markup from the editor.
 *
 * @return string
 */
function render_block( $attributes, $block_content ) {
	$markup = '';
	foreach ( get_meta_keys() as $meta_key => $label ) {
		$meta_value = get_post_meta( get_the_ID(), $meta_key, true ) ?? null;
		if ( null === $meta_value ) {
			continue;
		}

		$value = $meta_value;

		if ( strrpos( $meta_key, '_link' ) === strlen( $meta_key ) - strlen( '_link' ) ) {
			$url   = esc_url( $meta_value );
			$host  = parse_url( $url, PHP_URL_HOST );
			$value = sprintf(
				'<a href="%s">%s</a>',
				$url,
				$host
			);
		}
		$markup .= sprintf(
			'<li><span class="wh-talks-meta-label">%s:</span> %s</li>',
			$label,
			$value
		);
	}

	if ( '' === $markup ) {
		return '';
	}

	return str_replace( '</ul>', "$markup</ul>", $block_content );
}

/**
 * Only show talk meta block for `talk` CPT in editor.
 *
 * @param bool|array $allowed_block_types Array of block type slugs, or boolean to enable/disable all.
 *
 * @return bool|array
 */
function filter_allowed_blocks( $allowed_block_types ) {
	if ( 'talk' === get_post_type() ) {
		return $allowed_block_types;
	}

	$block_to_remove = 'wh-talks/meta';
	if ( is_array( $allowed_block_types ) ) {
		if ( ! in_array( $block_to_remove, $allowed_block_types ) ) {
			return $allowed_block_types;
		}

		foreach ( $allowed_block_types as $key => $block_type ) {
			if ( $block_type !== $block_to_remove ) {
				continue;
			}

			unset( $allowed_block_types[ $key ] );
			return $allowed_block_types;
		}
	}

	$tmp = WP_Block_Type_Registry::get_instance()->get_all_registered();

	$allowed_block_types = [];
	foreach ( $tmp as $block_name => $block_object ) {
		if ( $block_name === $block_to_remove ) {
			continue;
		}

		$allowed_block_types[] = $block_name;
	}

	return $allowed_block_types;
}
add_filter( 'allowed_block_types_all', __NAMESPACE__ . '\\filter_allowed_blocks' );

/**
 * Returns array of meta keys with their labels.
 *
 * @return array
 */
function get_meta_keys() {
	return [
		'wh_talks_event_name'  => __( 'Event', 'wh-talks' ),
		'wh_talks_language'    => __( 'Language', 'wh-talks' ),
		'wh_talks_duration'    => __( 'Duration', 'wh-talks' ),
		'wh_talks_video_link'  => __( 'Video', 'wh-talks' ),
		'wh_talks_slides_link' => __( 'Slides', 'wh-talks' ),
	];
}

require_once 'inc/cpt.php';
