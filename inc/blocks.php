<?php
/**
 * Doing block things (registration, filtering allowed blocks, et cetera).
 */

namespace WH\Talks;

use WP_Block_Editor_Context;
use WP_Block_Type_Registry;

/**
 * Registers the blocks.
 *
 * @return void
 */
function register_blocks() {
	register_block_type(
		__DIR__ . '/../build/blocks/meta-list',
		[
			'render_callback' => __NAMESPACE__ . '\\render_meta_list_block',
		]
	);
	register_block_type(
		__DIR__ . '/../build/blocks/single-meta',
		[
			'render_callback' => __NAMESPACE__ . '\\render_single_meta_block',
		]
	);
	register_block_type(
		__DIR__ . '/../build'
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_blocks' );

/**
 * Adds assets to block editor.
 *
 * @return void
 */
function enqueue_editor_assets() {
	wp_localize_script(
		'wh-talks-meta-list-editor-script',
		'whTalksObject',
		[
			'metas' => get_string_metas(),
		]
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_editor_assets' );

/**
 * Do not load editor modifications script in widgets screen.
 *
 * @return void
 */
function remove_editor_modifications_script_from_widgets_screen() {
	global $block_editor_context;
	$context_name = $block_editor_context->name ?? null;
	if ( 'core/edit-widgets' !== $context_name ) {
		return;
	}

	wp_deregister_script( 'wh-talks-editor-modifications-editor-script' );
}
add_filter( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\remove_editor_modifications_script_from_widgets_screen', 9 );

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
 * Only show talk meta block for `talk` CPT in editor.
 *
 * @param bool|array              $allowed_block_types Array of block type slugs, or boolean to enable/disable all.
 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
 *
 * @return bool|array
 */
function filter_allowed_blocks( $allowed_block_types, $block_editor_context ) {
	if ( 'talk' === get_post_type() || 'core/edit-site' === $block_editor_context->name ) {
		return $allowed_block_types;
	}

	if ( false === $allowed_block_types ) {
		return $allowed_block_types;
	}

	$blocks_to_remove = [
		'wh-talks/meta-list',
		'wh-talks/single-meta',
	];
	if ( is_array( $allowed_block_types ) ) {
		foreach ( $allowed_block_types as $key => $block_type ) {
			if ( ! in_array( $block_type, $blocks_to_remove, true ) ) {
				continue;
			}

			unset( $allowed_block_types[ $key ] );
		}
		return $allowed_block_types;
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
