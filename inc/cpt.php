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
		'name'                     => __( 'Talks', 'wh-talks' ),
		'singular_name'            => __( 'Talk', 'wh-talks' ),
		'add_new_item'             => __( 'Add new talk', 'wh-talks' ),
		'edit_item'                => __( 'Edit talk', 'wh-talks' ),
		'new_item'                 => __( 'New talk', 'wh-talks' ),
		'view_item'                => __( 'View talk', 'wh-talks' ),
		'view_items'               => __( 'View talks', 'wh-talks' ),
		'search_items'             => __( 'Search talks', 'wh-talks' ),
		'not_found'                => __( 'No talks found', 'wh-talks' ),
		'not_found_in_trash'       => __( 'No talks found in trash', 'wh-talks' ),
		'all_items'                => __( 'All talks', 'wh-talks' ),
		'archives'                 => __( 'Talk archives', 'wh-talks' ),
		'attributes'               => __( 'Talk attributes', 'wh-talks' ),
		'insert_into_item'         => __( 'Insert into talk', 'wh-talks' ),
		'uploaded_to_this_item'    => __( 'Uploaded to this talk', 'wh-talks' ),
		'filter_items_list'        => __( 'Filter talks list', 'wh-talks' ),
		'items_list_navigation'    => __( 'Talks list navigation', 'wh-talks' ),
		'items_list'               => __( 'Talks list', 'wh-talks' ),
		'item_published'           => __( 'Talk published.', 'wh-talks' ),
		'item_published_privately' => __( 'Talk published privately', 'wh-talks' ),
		'item_reverted_to_draft'   => __( 'Talk reverted to draft.', 'wh-talks' ),
		'item_scheduled'           => __( 'Talk scheduled.', 'wh-talks' ),
		'item_updated'             => __( 'Talk updated.', 'wh-talks' ),
		'item_link'                => __( 'Talk link', 'wh-talks' ),
		'item_link_description'    => __( 'A link to a talk', 'wh-talks' ),
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
			[ 'wh-talks/meta-list' ],
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
		'rest_base'    => 'talks',
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

/**
 * Returns the string or markup for a talk meta key.
 *
 * @param string $meta_key The meta key.
 *
 * @return string
 */
function get_meta_value_markup( $meta_key ) {
	$allowed_meta = false;

	foreach ( get_string_metas() as $meta ) {
		if ( $meta_key !== $meta['key'] ) {
			continue;
		}

		$allowed_meta = true;
		break;
	}

	if ( ! $allowed_meta ) {
		return '';
	}

	$meta_value = get_post_meta( get_the_ID(), $meta_key, true ) ?? null;
	if ( empty( $meta_value ) ) {
		return '';
	}

	if ( strrpos( $meta_key, '_link' ) !== strlen( $meta_key ) - strlen( '_link' ) ) {
		return esc_html( $meta_value );
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
 * Adds post class to highlighted `talk` posts.
 *
 * @param array $classes Array of post classes.
 * @param array $class Array of additional classes.
 * @param int   $post_id Post ID.
 * @return array
 */
function add_highlight_post_class( $classes, $class, $post_id ) {
	if ( 'talk' !== get_post_type() ) {
		return $classes;
	}

	$is_highlight = (bool) get_post_meta( $post_id, 'wh_talks_is_highlight', true );
	if ( $is_highlight ) {
		$classes[] = 'highlight-talk';
	}

	return $classes;
}
add_filter( 'post_class', __NAMESPACE__ . '\\add_highlight_post_class', 10, 3 );

/**
 * Adds body class to highlighted `talk` posts.
 *
 * @param array $classes Array of post classes.
 * @return array
 */
function add_highlight_body_class( $classes ) {
	if ( 'talk' !== get_post_type() || ! is_single() ) {
		return $classes;
	}

	$is_highlight = (bool) get_post_meta( get_the_ID(), 'wh_talks_is_highlight', true );
	if ( $is_highlight ) {
		$classes[] = 'highlight-talk';
	}

	return $classes;
}
add_filter( 'body_class', __NAMESPACE__ . '\\add_highlight_body_class' );
