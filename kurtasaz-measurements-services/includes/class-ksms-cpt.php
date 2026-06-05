<?php
/**
 * Custom Post Type Registration for Tailoring Orders
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KSMS_CPT {

	/**
	 * Instance of this class.
	 *
	 * @var KSMS_CPT
	 */
	private static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return KSMS_CPT
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
	}

	/**
	 * Register Custom Post Type ks_orders.
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Tailoring Orders', 'Post type general name', 'kurtasaz-measurements-services' ),
			'singular_name'         => _x( 'Tailoring Order', 'Post type singular name', 'kurtasaz-measurements-services' ),
			'menu_name'             => _x( 'KS Orders', 'Admin Menu text', 'kurtasaz-measurements-services' ),
			'name_admin_bar'        => _x( 'Tailoring Order', 'Add New on Toolbar', 'kurtasaz-measurements-services' ),
			'add_new'               => __( 'Add New', 'kurtasaz-measurements-services' ),
			'add_new_item'          => __( 'Add New Tailoring Order', 'kurtasaz-measurements-services' ),
			'new_item'              => __( 'New Tailoring Order', 'kurtasaz-measurements-services' ),
			'edit_item'             => __( 'Edit Tailoring Order', 'kurtasaz-measurements-services' ),
			'view_item'             => __( 'View Tailoring Order', 'kurtasaz-measurements-services' ),
			'all_items'             => __( 'All Orders', 'kurtasaz-measurements-services' ),
			'search_items'          => __( 'Search Tailoring Orders', 'kurtasaz-measurements-services' ),
			'parent_item_colon'     => __( 'Parent Tailoring Orders:', 'kurtasaz-measurements-services' ),
			'not_found'             => __( 'No tailoring orders found.', 'kurtasaz-measurements-services' ),
			'not_found_in_trash'    => __( 'No tailoring orders found in Trash.', 'kurtasaz-measurements-services' ),
			'featured_image'        => _x( 'Order Sheet Image', 'Overrides the “Featured Image” phrase', 'kurtasaz-measurements-services' ),
			'set_featured_image'    => _x( 'Set order sheet image', 'Overrides the “Set featured image” phrase', 'kurtasaz-measurements-services' ),
			'remove_featured_image' => _x( 'Remove order sheet image', 'Overrides the “Remove featured image” phrase', 'kurtasaz-measurements-services' ),
			'use_featured_image'    => _x( 'Use as order sheet image', 'Overrides the “Use as featured image” phrase', 'kurtasaz-measurements-services' ),
			'archives'              => _x( 'Tailoring Order Archives', 'The post type archive label', 'kurtasaz-measurements-services' ),
			'insert_into_item'      => _x( 'Insert into order sheet', 'Overrides the “Insert into post”/”Insert into page” phrase', 'kurtasaz-measurements-services' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this order sheet', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase', 'kurtasaz-measurements-services' ),
			'filter_items_list'     => _x( 'Filter tailoring orders list', 'Screen reader text for the filter links', 'kurtasaz-measurements-services' ),
			'items_list_navigation' => _x( 'Tailoring orders list navigation', 'Screen reader text for the pagination', 'kurtasaz-measurements-services' ),
			'items_list'            => _x( 'Tailoring orders list', 'Screen reader text for the items list', 'kurtasaz-measurements-services' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false, // Hidden from frontend search and URLs.
			'publicly_queryable' => false,
			'show_ui'            => true, // Shown in WordPress Admin.
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'ks_orders' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 26,
			'menu_icon'          => 'dashicons-scissors', // Tailoring scissors icon.
			'supports'           => array( 'title' ),
		);

		register_post_type( 'ks_orders', $args );
	}

	/**
	 * Custom messages for order updates.
	 *
	 * @param array $messages Post update messages.
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		$post             = get_post();
		$post_type_object = get_post_type_object( 'ks_orders' );

		$messages['ks_orders'] = array(
			0  => '', // Unused. Required to match 1-based index.
			1  => __( 'Tailoring Order updated.', 'kurtasaz-measurements-services' ),
			2  => __( 'Custom field updated.', 'kurtasaz-measurements-services' ),
			3  => __( 'Custom field deleted.', 'kurtasaz-measurements-services' ),
			4  => __( 'Tailoring Order updated.', 'kurtasaz-measurements-services' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Tailoring Order restored to revision from %s', 'kurtasaz-measurements-services' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Tailoring Order published.', 'kurtasaz-measurements-services' ),
			7  => __( 'Tailoring Order saved.', 'kurtasaz-measurements-services' ),
			8  => __( 'Tailoring Order submitted.', 'kurtasaz-measurements-services' ),
			9  => sprintf(
				__( 'Tailoring Order scheduled for: <strong>%1$s</strong>.', 'kurtasaz-measurements-services' ),
				date_i18n( __( 'M j, Y @ G:i', 'kurtasaz-measurements-services' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Tailoring Order draft updated.', 'kurtasaz-measurements-services' ),
		);

		return $messages;
	}
}
