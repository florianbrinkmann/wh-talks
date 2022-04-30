<?php
/**
 * All CPT-related things.
 */

namespace WH\Talks;

/**
 * Registers the `talks` CPT.
 */
function register_cpt() {
	$labels = [
		'name'          => __( 'Talks', 'wh-talks' ),
		'singular_name' => __( 'Talk', 'wh-talks' ),
		'add_new_item'  => __( 'Add talk', 'wh-talks' ),
		'edit_item'     => __( 'Edit talk', 'wh-talks' ),
		'new_item'      => __( 'New talk', 'wh-talks' ),
		'view_item'     => __( 'View talk', 'wh-talks' ),
		'view_items'    => __( 'View talks', 'wh-talks' ),
		'not_found'     => __( 'No talks found.', 'wh-talks' ),
	];

	/**
	 * Block template that is added to the editor when creating talks.
	 *
	 * @param array The blocks.
	 */
	$cpt_template = (array) apply_filters(
		'wh_talks_cpt_block_template',
		[
			[ 'core/paragraph' ],
			[ 'core/more' ],
			[ 'wh-talks/meta' ],
		]
	);

	$args = [
		'labels'       => $labels,
		'supports'     => [
			'title',
			'editor',
			'custom-fields',
			'excerpt',
			'thumbnail',
			'revisions',
		],
		'hierarchical' => false,
		'public'       => true,
		'show_in_rest' => true,
		'template'     => $cpt_template,
		'menu_icon'    => 'dashicons-megaphone',
	];
	register_post_type( 'talk', $args );
}
add_action( 'init', __NAMESPACE__ . '\\register_cpt' );

/**
 * Registers metadata for the CPT.
 *
 * @return void
 */
function register_meta() {
	foreach ( get_meta_keys() as $meta_key => $label ) {
		register_post_meta(
			'talk',
			$meta_key,
			[
				'single'       => true,
				'show_in_rest' => true,
			]
		);
	}

	register_post_meta(
		'talk',
		'wh_talks_is_highlight',
		[
			'single'       => true,
			'show_in_rest' => true,
			'type'         => 'boolean',
		]
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_meta' );
