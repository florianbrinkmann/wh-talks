<?php
/**
 * All CPT-related things.
 */

namespace WH\Talks;

/**
 * Registering the `talks` CPT.
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

	$args = [
		'labels'       => $labels,
		'supports'     => [ 'title', 'editor', 'custom-fields', 'excerpt', 'thumbnail', 'revisions' ],
		'hierarchical' => false,
		'public'       => true,
		'show_in_rest' => true,
	];
	register_post_type( 'talk', $args );
}
add_action( 'init', __NAMESPACE__ . '\\register_cpt' );
