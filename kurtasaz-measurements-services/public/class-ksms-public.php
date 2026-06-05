<?php
/**
 * Frontend Public Handler, Automatic WooCommerce Form Injection, and Cart Hooking
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KSMS_Public {

	/**
	 * Instance of this class.
	 *
	 * @var KSMS_Public
	 */
	private static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return KSMS_Public
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
		// Asset Enqueues.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		// Automatic WooCommerce Form Injection.
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'inject_measurements_form' ), 15 );

		// WooCommerce Cart & Checkout Integrations.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 3 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'display_cart_item_data' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'save_order_line_item_meta' ), 10, 4 );

		// Transactional Emails Hook.
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'send_wc_order_emails' ), 10, 3 );

		// Keep shortcode fallback for compatibility.
		add_shortcode( 'kurtasaz_measurements', array( $this, 'render_measurements_form_shortcode' ) );
	}

	/**
	 * Check if the current product page qualifies for the KurtaSaz Form.
	 *
	 * @param int $product_id Product ID.
	 * @return bool True if qualifies.
	 */
	private function qualifies_for_form( $product_id ) {
		if ( ! $product_id ) {
			return false;
		}

		// Check Custom Metadata Enabled Setting.
		$enabled = get_post_meta( $product_id, '_enable_kurtasaz_measurements', true );
		if ( 'yes' !== $enabled ) {
			return false;
		}

		// Check Product Categories (Stitched or Unstitched).
		if ( ! has_term( array( 'stitched', 'unstitched' ), 'product_cat', $product_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Enqueue assets only when the form is rendered on the frontend.
	 */
	public function enqueue_assets() {
		$should_enqueue = false;

		// 1. Check if we are on a qualifying single product page.
		if ( is_product() ) {
			$product_id = get_the_ID();
			if ( $this->qualifies_for_form( $product_id ) ) {
				$should_enqueue = true;
			}
		}

		// 2. Fallback check for shortcode in post contents.
		if ( ! $should_enqueue ) {
			global $post;
			if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'kurtasaz_measurements' ) ) {
				$should_enqueue = true;
			}
		}

		if ( $should_enqueue ) {
			// Enqueue Tailwind Play CDN.
			wp_enqueue_script( 'tailwind-cdn', 'https://cdn.tailwindcss.com', array(), '3.4.1', false );

			// Enqueue Frontend Custom JS.
			wp_enqueue_script( 'ksms-frontend-js', KSMS_URL . 'public/js/measurements.js', array(), KSMS_VERSION, true );

			// Localize JS Script.
			wp_localize_script( 'ksms-frontend-js', 'ksms_ajax_obj', array(
				'nonce' => wp_create_nonce( 'ksms_nonce' ),
			) );
		}
	}

	/**
	 * Automatically inject the HTML measurements form above the Add to Cart button.
	 */
	public function inject_measurements_form() {
		$product_id = get_the_ID();
		if ( $this->qualifies_for_form( $product_id ) ) {
			include KSMS_PATH . 'public/templates/measurements-form.php';
		}
	}

	/**
	 * Shortcode callback fallback.
	 *
	 * @return string HTML contents.
	 */
	public function render_measurements_form_shortcode() {
		ob_start();
		include KSMS_PATH . 'public/templates/measurements-form.php';
		return ob_get_clean();
	}

	/**
	 * Capture measurements inputs into WooCommerce Cart Item Data.
	 *
	 * @param array $cart_item_data Cart item session data.
	 * @param int   $product_id     Product ID.
	 * @param int   $variation_id   Variation ID.
	 * @return array Updated cart item data.
	 */
	public function add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		if ( isset( $_POST['ksms_service_type'] ) ) {
			$custom_data = array(
				'service_type'  => sanitize_text_field( wp_unslash( $_POST['ksms_service_type'] ) ),
				'garment_type'  => sanitize_text_field( wp_unslash( $_POST['ksms_garment_type'] ) ),
				'client_name'   => sanitize_text_field( wp_unslash( $_POST['ksms_client_name'] ) ),
				'client_email'  => sanitize_email( wp_unslash( $_POST['ksms_client_email'] ) ),
				'client_phone'  => sanitize_text_field( wp_unslash( $_POST['ksms_client_phone'] ) ),
				'fabric_notes'  => sanitize_textarea_field( wp_unslash( $_POST['ksms_fabric_notes'] ) ),
				'standard_size' => isset( $_POST['ksms_standard_size'] ) ? sanitize_text_field( wp_unslash( $_POST['ksms_standard_size'] ) ) : '',
			);

			// Capture tailoring specs if custom silwaai is selected.
			if ( 'custom' === $custom_data['service_type'] ) {
				$fields = array( 'neck', 'shoulder', 'chest', 'waist', 'hip', 'kurta_length', 'sleeve_length', 'armhole', 'bicep', 'wrist', 'collar', 'inseam_length' );
				$custom_data['measurements'] = array();
				foreach ( $fields as $field ) {
					$post_key = 'ksms_m_' . $field;
					if ( isset( $_POST[ $post_key ] ) ) {
						$custom_data['measurements'][ $field ] = floatval( $_POST[ $post_key ] );
					}
				}
			}

			// Store in cart session.
			$cart_item_data['ksms_data'] = $custom_data;

			// Generate a unique key so identical products with different measurements are separated.
			$cart_item_data['unique_key'] = md5( microtime() . wp_rand() );
		}

		return $cart_item_data;
	}

	/**
	 * Display custom sizes & measurements in WooCommerce Cart & Checkout layouts.
	 *
	 * @param array $item_data Cart item display data.
	 * @param array $cart_item Cart item data.
	 * @return array
	 */
	public function display_cart_item_data( $item_data, $cart_item ) {
		if ( isset( $cart_item['ksms_data'] ) ) {
			$data = $cart_item['ksms_data'];

			$service_labels = array(
				'unstitched' => __( 'Unstitched Fabric', 'kurtasaz-measurements-services' ),
				'stitched'   => __( 'Standard Stitched', 'kurtasaz-measurements-services' ),
				'custom'     => __( 'Custom Silwaai (Tailored)', 'kurtasaz-measurements-services' ),
			);
			$garment_labels = array(
				'kurta'     => __( 'Kurta Only', 'kurtasaz-measurements-services' ),
				'shalwar'   => __( 'Shalwar Only', 'kurtasaz-measurements-services' ),
				'full_suit' => __( 'Full Suit (Kurta & Shalwar)', 'kurtasaz-measurements-services' ),
			);

			$service_lbl = isset( $service_labels[ $data['service_type'] ] ) ? $service_labels[ $data['service_type'] ] : $data['service_type'];
			$garment_lbl = isset( $garment_labels[ $data['garment_type'] ] ) ? $garment_labels[ $data['garment_type'] ] : $data['garment_type'];

			$item_data[] = array(
				'name'  => __( 'Tailoring Service', 'kurtasaz-measurements-services' ),
				'value' => $service_lbl,
			);
			$item_data[] = array(
				'name'  => __( 'Garment Type', 'kurtasaz-measurements-services' ),
				'value' => $garment_lbl,
			);
			$item_data[] = array(
				'name'  => __( 'Client Name', 'kurtasaz-measurements-services' ),
				'value' => $data['client_name'],
			);

			if ( 'stitched' === $data['service_type'] && ! empty( $data['standard_size'] ) ) {
				$item_data[] = array(
					'name'  => __( 'Size', 'kurtasaz-measurements-services' ),
					'value' => $data['standard_size'],
				);
			}

			if ( 'custom' === $data['service_type'] && ! empty( $data['measurements'] ) ) {
				$m_string = '';
				foreach ( $data['measurements'] as $key => $val ) {
					$m_string .= ucwords( str_replace( '_', ' ', $key ) ) . ': ' . $val . '" | ';
				}
				$item_data[] = array(
					'name'  => __( 'Tailor Specs', 'kurtasaz-measurements-services' ),
					'value' => rtrim( $m_string, ' | ' ),
				);
			}

			if ( ! empty( $data['fabric_notes'] ) ) {
				$item_data[] = array(
					'name'  => __( 'Notes', 'kurtasaz-measurements-services' ),
					'value' => $data['fabric_notes'],
				);
			}
		}

		return $item_data;
	}

	/**
	 * Save cart item measurement metadata to WooCommerce Order Line Items.
	 *
	 * @param WC_Order_Item_Product $item          Order Line Item.
	 * @param string                $cart_item_key Cart item unique key.
	 * @param array                 $values        Cart item database values.
	 * @param WC_Order              $order         Order object.
	 */
	public function save_order_line_item_meta( $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['ksms_data'] ) ) {
			$data = $values['ksms_data'];

			$service_labels = array(
				'unstitched' => __( 'Unstitched Fabric', 'kurtasaz-measurements-services' ),
				'stitched'   => __( 'Standard Stitched', 'kurtasaz-measurements-services' ),
				'custom'     => __( 'Custom Silwaai (Tailored)', 'kurtasaz-measurements-services' ),
			);
			$garment_labels = array(
				'kurta'     => __( 'Kurta Only', 'kurtasaz-measurements-services' ),
				'shalwar'   => __( 'Shalwar Only', 'kurtasaz-measurements-services' ),
				'full_suit' => __( 'Full Suit (Kurta & Shalwar)', 'kurtasaz-measurements-services' ),
			);

			$service_lbl = isset( $service_labels[ $data['service_type'] ] ) ? $service_labels[ $data['service_type'] ] : $data['service_type'];
			$garment_lbl = isset( $garment_labels[ $data['garment_type'] ] ) ? $garment_labels[ $data['garment_type'] ] : $data['garment_type'];

			// Save internal keys (with underscores).
			$item->add_meta_data( '_ksms_client_name', $data['client_name'] );
			$item->add_meta_data( '_ksms_client_email', $data['client_email'] );
			$item->add_meta_data( '_ksms_client_phone', $data['client_phone'] );
			$item->add_meta_data( '_ksms_service_type', $data['service_type'] );
			$item->add_meta_data( '_ksms_garment_type', $data['garment_type'] );
			$item->add_meta_data( '_ksms_standard_size', $data['standard_size'] );
			$item->add_meta_data( '_ksms_fabric_notes', $data['fabric_notes'] );

			// Save readable labels (without underscores) for auto-rendering in WooCommerce dashboard.
			$item->add_meta_data( __( 'Tailoring Service', 'kurtasaz-measurements-services' ), $service_lbl );
			$item->add_meta_data( __( 'Garment Type', 'kurtasaz-measurements-services' ), $garment_lbl );
			$item->add_meta_data( __( 'Client Name', 'kurtasaz-measurements-services' ), $data['client_name'] );
			$item->add_meta_data( __( 'Client WhatsApp', 'kurtasaz-measurements-services' ), $data['client_phone'] );

			if ( 'stitched' === $data['service_type'] && ! empty( $data['standard_size'] ) ) {
				$item->add_meta_data( __( 'Standard Size', 'kurtasaz-measurements-services' ), $data['standard_size'] );
			}

			if ( 'custom' === $data['service_type'] && ! empty( $data['measurements'] ) ) {
				foreach ( $data['measurements'] as $key => $val ) {
					$meta_name = ucwords( str_replace( '_', ' ', $key ) ) . ' (Inches)';
					$item->add_meta_data( $meta_name, $val . '"' );
					$item->add_meta_data( '_ksms_m_' . $key, $val );
				}
			}

			if ( ! empty( $data['fabric_notes'] ) ) {
				$item->add_meta_data( __( 'Tailor Instructions', 'kurtasaz-measurements-services' ), $data['fabric_notes'] );
			}
		}
	}

	/**
	 * Send dual transactional emails when an order containing tailoring specifications completes checkout.
	 *
	 * @param int      $order_id Order ID.
	 * @param array    $posted   Posted checkout form data.
	 * @param WC_Order $order    Order object.
	 */
	public function send_wc_order_emails( $order_id, $posted, $order ) {
		// Verify valid order.
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		$has_tailoring = false;
		$tailoring_items = array();

		// Loop items to check if any have tailoring metadata.
		foreach ( $order->get_items() as $item_id => $item ) {
			$service_type = $item->get_meta( '_ksms_service_type' );
			if ( ! empty( $service_type ) ) {
				$has_tailoring = true;
				
				// Reconstruct measurements from metadata.
				$measurements = array();
				$fields = array( 'neck', 'shoulder', 'chest', 'waist', 'hip', 'kurta_length', 'sleeve_length', 'armhole', 'bicep', 'wrist', 'collar', 'inseam_length' );
				foreach ( $fields as $field ) {
					$val = $item->get_meta( '_ksms_m_' . $field );
					if ( ! empty( $val ) ) {
						$measurements[ $field ] = $val;
					}
				}

				$tailoring_items[] = array(
					'product_name'  => $item->get_name(),
					'client_name'   => $item->get_meta( '_ksms_client_name' ),
					'client_email'  => $item->get_meta( '_ksms_client_email' ),
					'client_phone'  => $item->get_meta( '_ksms_client_phone' ),
					'service_type'  => $service_type,
					'garment_type'  => $item->get_meta( '_ksms_garment_type' ),
					'standard_size' => $item->get_meta( '_ksms_standard_size' ),
					'fabric_notes'  => $item->get_meta( '_ksms_fabric_notes' ),
					'measurements'  => $measurements,
				);
			}
		}

		// Dispatch if order contains tailoring parameters.
		if ( $has_tailoring ) {
			foreach ( $tailoring_items as $item ) {
				$this->dispatch_emails(
					$item['client_name'],
					$item['client_email'],
					$item['client_phone'],
					$item['service_type'],
					$item['garment_type'],
					$item['standard_size'],
					$item['measurements'],
					$item['fabric_notes'],
					$item['product_name'],
					$order->get_order_number()
				);
			}
		}
	}

	/**
	 * Construct and dispatch transactional HTML emails using WooCommerce order contexts.
	 */
	private function dispatch_emails( $name, $email, $phone, $service, $garment, $size, $measurements, $notes, $product_name, $order_num ) {
		$admin_email = get_option( 'admin_email' );

		$service_labels = array(
			'unstitched' => __( 'Unstitched Fabric', 'kurtasaz-measurements-services' ),
			'stitched'   => __( 'Standard Stitched', 'kurtasaz-measurements-services' ),
			'custom'     => __( 'Custom Silwaai (Tailored)', 'kurtasaz-measurements-services' ),
		);
		$garment_labels = array(
			'kurta'     => __( 'Kurta Only', 'kurtasaz-measurements-services' ),
			'shalwar'   => __( 'Shalwar Only', 'kurtasaz-measurements-services' ),
			'full_suit' => __( 'Full Suit (Kurta & Shalwar)', 'kurtasaz-measurements-services' ),
		);

		$service_lbl = isset( $service_labels[ $service ] ) ? $service_labels[ $service ] : $service;
		$garment_lbl = isset( $garment_labels[ $garment ] ) ? $garment_labels[ $garment ] : $garment;

		$m_rows = '';
		if ( 'custom' === $service && ! empty( $measurements ) ) {
			$m_rows .= '<tr><td colspan="2" style="background:#f1f5f9;font-weight:bold;padding:10px;text-align:center;border:1px solid #cbd5e1;color:#1e293b;">' . esc_html__( 'Tailoring Dimensions (Inches)', 'kurtasaz-measurements-services' ) . '</td></tr>';
			foreach ( $measurements as $part => $val ) {
				$label   = ucwords( str_replace( '_', ' ', $part ) );
				$m_rows .= '<tr>';
				$m_rows .= '<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:500;color:#475569;">' . esc_html( $label ) . '</td>';
				$m_rows .= '<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:bold;color:#0f172a;text-align:right;">' . esc_html( $val ) . '"</td>';
				$m_rows .= '</tr>';
			}
		} elseif ( 'stitched' === $service ) {
			$m_rows .= '<tr>';
			$m_rows .= '<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:500;color:#475569;">' . esc_html__( 'Selected Size', 'kurtasaz-measurements-services' ) . '</td>';
			$m_rows .= '<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:bold;color:#0f172a;text-align:right;">' . esc_html( $size ) . '</td>';
			$m_rows .= '</tr>';
		} else {
			$m_rows .= '<tr>';
			$m_rows .= '<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:500;color:#475569;">' . esc_html__( 'Selected Size', 'kurtasaz-measurements-services' ) . '</td>';
			$m_rows .= '<td style="padding:8px 12px;border:1px solid #e2e8f0;color:#64748b;font-style:italic;text-align:right;">' . esc_html__( 'Fabric Recommendation Guidelines Only', 'kurtasaz-measurements-services' ) . '</td>';
			$m_rows .= '</tr>';
		}

		$email_style = '
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
			line-height: 1.6;
			color: #334155;
			background-color: #f8fafc;
			margin: 0;
			padding: 40px 10px;
		';
		$card_style = '
			max-width: 600px;
			margin: 0 auto;
			background-color: #ffffff;
			border-radius: 12px;
			box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
			border: 1px solid #e2e8f0;
			overflow: hidden;
		';
		$header_style = '
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
			padding: 30px;
			text-align: center;
			color: #ffffff;
		';
		$body_style = '
			padding: 30px;
		';
		$footer_style = '
			background-color: #f1f5f9;
			padding: 20px;
			text-align: center;
			font-size: 12px;
			color: #64748b;
			border-top: 1px solid #e2e8f0;
		';

		$shared_html = '
		<div style="' . esc_attr( $card_style ) . '">
			<div style="' . esc_attr( $header_style ) . '">
				<h1 style="margin:0;font-size:24px;font-weight:700;letter-spacing:1px;">KURTASAZ</h1>
				<p style="margin:5px 0 0;font-size:12px;text-transform:uppercase;letter-spacing:2px;opacity:0.8;">' . esc_html__( 'Measurements & Custom Tailoring', 'kurtasaz-measurements-services' ) . '</p>
			</div>
			<div style="' . esc_attr( $body_style ) . '">';

		// A. Admin Email HTML.
		$admin_body = $shared_html . '
				<h2 style="font-size:18px;margin-top:0;color:#0f172a;">' . sprintf( esc_html__( 'Order #%s Sizing Specs', 'kurtasaz-measurements-services' ), $order_num ) . '</h2>
				<p style="margin-bottom:20px;">' . sprintf( esc_html__( 'Tailoring details have been captured for product: <strong>%s</strong>.', 'kurtasaz-measurements-services' ), esc_html( $product_name ) ) . '</p>
				
				<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
					<tr>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:bold;color:#475569;width:40%;">' . esc_html__( 'Client Name', 'kurtasaz-measurements-services' ) . '</td>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;color:#0f172a;">' . esc_html( $name ) . '</td>
					</tr>
					<tr>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:bold;color:#475569;">' . esc_html__( 'Email', 'kurtasaz-measurements-services' ) . '</td>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;color:#0f172a;">' . esc_html( $email ) . '</td>
					</tr>
					<tr>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:bold;color:#475569;">' . esc_html__( 'WhatsApp / Phone', 'kurtasaz-measurements-services' ) . '</td>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;color:#0f172a;">' . esc_html( $phone ) . '</td>
					</tr>
					<tr>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:bold;color:#475569;">' . esc_html__( 'Service Type', 'kurtasaz-measurements-services' ) . '</td>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;color:#0f172a;"><span style="background-color:#f1f5f9;padding:2px 8px;border-radius:4px;font-weight:600;font-size:12px;">' . esc_html( $service_lbl ) . '</span></td>
					</tr>
					<tr>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:bold;color:#475569;">' . esc_html__( 'Garment Type', 'kurtasaz-measurements-services' ) . '</td>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;color:#0f172a;">' . esc_html( $garment_lbl ) . '</td>
					</tr>
				</table>

				<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
					' . $m_rows . '
				</table>';

		if ( ! empty( $notes ) ) {
			$admin_body .= '
				<div style="background:#f8fafc;padding:15px;border-radius:8px;border:1px solid #e2e8f0;margin-top:20px;">
					<h3 style="margin:0 0 8px 0;font-size:14px;color:#0f172a;">' . esc_html__( 'Fabric & Styling Notes', 'kurtasaz-measurements-services' ) . '</h3>
					<p style="margin:0;font-size:13px;white-space:pre-wrap;">' . esc_html( $notes ) . '</p>
				</div>';
		}

		$admin_body .= '
			</div>
			<div style="' . esc_attr( $footer_style ) . '">
				<p style="margin:0;">&copy; ' . date( 'Y' ) . ' KurtaSaz Tailoring. All rights reserved.</p>
			</div>
		</div>';

		$admin_message = '<div style="' . esc_attr( $email_style ) . '">' . $admin_body . '</div>';

		// B. Customer Email HTML.
		$customer_body = $shared_html . '
				<h2 style="font-size:18px;margin-top:0;color:#0f172a;">' . sprintf( esc_html__( 'Salam %s,', 'kurtasaz-measurements-services' ), esc_html( $name ) ) . '</h2>
				<p style="margin-bottom:20px;">' . sprintf( esc_html__( 'Thank you for your order #%s! We have successfully received your custom tailoring sizes for: <strong>%s</strong>.', 'kurtasaz-measurements-services' ), $order_num, esc_html( $product_name ) ) . '</p>
				
				<h3 style="font-size:14px;color:#0f172a;margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px;">' . esc_html__( 'Your Sizing Parameters', 'kurtasaz-measurements-services' ) . '</h3>
				<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
					<tr>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:bold;color:#475569;width:40%;">' . esc_html__( 'Service Type', 'kurtasaz-measurements-services' ) . '</td>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;color:#0f172a;">' . esc_html( $service_lbl ) . '</td>
					</tr>
					<tr>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;font-weight:bold;color:#475569;">' . esc_html__( 'Garment Type', 'kurtasaz-measurements-services' ) . '</td>
						<td style="padding:8px 12px;border:1px solid #e2e8f0;color:#0f172a;">' . esc_html( $garment_lbl ) . '</td>
					</tr>
				</table>

				<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
					' . $m_rows . '
				</table>';

		if ( ! empty( $notes ) ) {
			$customer_body .= '
				<div style="background:#f8fafc;padding:15px;border-radius:8px;border:1px solid #e2e8f0;margin-top:20px;">
					<h3 style="margin:0 0 8px 0;font-size:14px;color:#0f172a;">' . esc_html__( 'Your Styling Instructions', 'kurtasaz-measurements-services' ) . '</h3>
					<p style="margin:0;font-size:13px;white-space:pre-wrap;">' . esc_html( $notes ) . '</p>
				</div>';
		}

		$customer_body .= '
				<p style="margin-top:25px;">' . esc_html__( 'If you have any questions or need to make adjustments, please message us on WhatsApp with your Order ID.', 'kurtasaz-measurements-services' ) . '</p>
				<p style="margin-top:20px;font-weight:600;">' . esc_html__( 'Warm regards,', 'kurtasaz-measurements-services' ) . '<br><span style="color:#0f172a;font-weight:700;">' . esc_html__( 'The KurtaSaz Team', 'kurtasaz-measurements-services' ) . '</span></p>
			</div>
			<div style="' . esc_attr( $footer_style ) . '">
				<p style="margin:0 0 5px 0;">&copy; ' . date( 'Y' ) . ' KurtaSaz Tailoring. All rights reserved.</p>
			</div>
		</div>';

		$customer_message = '<div style="' . esc_attr( $email_style ) . '">' . $customer_body . '</div>';

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		// Send Admin Email
		wp_mail(
			$admin_email,
			sprintf( __( '[KurtaSaz] Sizing Sheet for Order #%1$s - %2$s', 'kurtasaz-measurements-services' ), $order_num, $name ),
			$admin_message,
			$headers
		);

		// Send Customer Email
		wp_mail(
			$email,
			sprintf( __( 'KurtaSaz Sizing Sheet for Order #%s', 'kurtasaz-measurements-services' ), $order_num ),
			$customer_message,
			$headers
		);
	}
}
