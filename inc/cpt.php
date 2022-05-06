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
		'has_archive'  => true,
		'rewrite'      => [ /* translators: slug for permalink of CPT */
			'slug' => __( 'talks', 'wh-talks' ),
		],
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
	foreach ( get_string_metas() as $meta ) {
		$meta_key = $meta['key'] ?? null;
		if ( empty( $meta_key ) ) {
			continue;
		}
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
