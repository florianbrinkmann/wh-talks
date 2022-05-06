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
 * Registers the blocks.
 *
 * @return void
 */
function register_blocks() {
	register_block_type(
		__DIR__ . '/build/blocks/meta-list',
		[
			'render_callback' => __NAMESPACE__ . '\\render_meta_list_block',
		]
	);
	register_block_type(
		__DIR__ . '/build/blocks/single-meta',
		[
			'render_callback' => __NAMESPACE__ . '\\render_single_meta_block',
		]
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_blocks' );

/**
 * Adds assets to block editor.
 *
 * @return void
 */
function enqueue_block_editor_assets() {
	$asset_info = include __DIR__ . '/build/index.asset.php';

	wp_enqueue_script(
		'wh-talk-editor-customizations',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset_info['dependencies'],
		$asset_info['version']
	);

	$metas = wp_json_encode( get_string_metas() );
	echo "<script>var whTalksMetas = $metas;</script>";
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_block_editor_assets' );

/**
 * Renders the meta list block block on the frontend.
 *
 * @param array  $attributes The block attributes.
 * @param string $block_content The block markup from the editor.
 *
 * @return string
 */
function render_meta_list_block( $attributes, $block_content ) {
	$markup = '';
	foreach ( get_string_metas() as $meta ) {
		$meta_key   = $meta['key'] ?? null;
		$meta_label = $meta['label'] ?? null;
		if ( empty( $meta_key ) || empty( $meta_label ) ) {
			continue;
		}

		$meta_value_markup = get_meta_value_markup( $meta_key );
		if ( empty( $meta_value_markup ) ) {
			continue;
		}

		$markup .= sprintf(
			'<li><span class="wh-talks-meta-label">%s:</span> %s</li>',
			$meta_label,
			$meta_value_markup
		);
	}

	if ( '' === $markup ) {
		return '';
	}

	return str_replace( '</ul>', "$markup</ul>", $block_content );
}

/**
 * Renders the single meta block on the frontend.
 *
 * @param array  $attributes The block attributes.
 * @param string $block_content The block markup from the editor.
 *
 * @return string
 */
function render_single_meta_block( $attributes, $block_content ) {
	$meta_key = $attributes['metaKey'] ?? null;
	if ( empty( $meta_key ) ) {
		return '';
	}
	$meta_value_markup = get_meta_value_markup( $meta_key );
	if ( empty( $meta_value_markup ) ) {
		return '';
	}

	$label = $attributes['label'] ?? null;
	if ( empty( $label ) ) {
		return str_replace( '</p>', "$meta_value_markup</p>", $block_content );
	}

	return str_replace( '</p>', "<span class='wh-talks-meta-label'>$label</span>$meta_value_markup</p>", $block_content );
}

/**
 * Returns the string or markup for a talk meta key.
 *
 * @param string $meta_key The meta key.
 *
 * @return string
 */
function get_meta_value_markup( $meta_key ) {
	$meta_value = get_post_meta( get_the_ID(), $meta_key, true ) ?? null;
	if ( empty( $meta_value ) ) {
		return '';
	}

	$value = $meta_value;

	if ( strrpos( $meta_key, '_link' ) !== strlen( $meta_key ) - strlen( '_link' ) ) {
		return $value;
	}

	if ( 1 !== preg_match( '/^(http:|https:)?\/\//', $meta_value ) ) {
		return '';
	}

	$url  = esc_url( $meta_value );
	$host = parse_url( $url, PHP_URL_HOST );
	return sprintf(
		'<a href="%s">%s</a>',
		$url,
		$host
	);
}

/**
 * Only show talk meta block for `talk` CPT in editor.
 *
 * @param bool|array $allowed_block_types Array of block type slugs, or boolean to enable/disable all.
 *
 * @return bool|array
 */
function filter_allowed_blocks( $allowed_block_types, $block_editor_context ) {
	if ( 'talk' === get_post_type() || $block_editor_context->name === 'core/edit-site' ) {
		return $allowed_block_types;
	}

	$blocks_to_remove = [
		'wh-talks/meta-list',
		'wh-talks/single-meta',
	];
	if ( is_array( $allowed_block_types ) ) {
		if ( ! in_array( $blocks_to_remove, $allowed_block_types ) ) {
			return $allowed_block_types;
		}

		foreach ( $allowed_block_types as $key => $block_type ) {
			if ( ! in_array( $block_type, $blocks_to_remove, true ) ) {
				continue;
			}

			unset( $allowed_block_types[ $key ] );
			return $allowed_block_types;
		}
	}

	$tmp = WP_Block_Type_Registry::get_instance()->get_all_registered();

	$allowed_block_types = [];
	foreach ( $tmp as $block_name => $block_object ) {
		if ( in_array( $block_name, $blocks_to_remove, true ) ) {
			continue;
		}

		$allowed_block_types[] = $block_name;
	}

	return $allowed_block_types;
}
add_filter( 'allowed_block_types_all', __NAMESPACE__ . '\\filter_allowed_blocks', 10, 2 );

/**
 * Returns array of meta keys with their labels.
 *
 * @return array
 */
function get_string_metas() {
	return [
		[
			'key'   => 'wh_talks_event_name',
			'label' => __( 'Event', 'wh-talks' ),
		],
		[
			'key'   => 'wh_talks_language',
			'label' => __( 'Language', 'wh-talks' ),
		],
		[
			'key'   => 'wh_talks_duration',
			'label' => __( 'Duration', 'wh-talks' ),
		],
		[
			'key'   => 'wh_talks_video_link',
			'label' => __( 'Video', 'wh-talks' ),
		],
		[
			'key'   => 'wh_talks_slides_link',
			'label' => __( 'Slides', 'wh-talks' ),
		],
	];
}

require_once 'inc/cpt.php';
