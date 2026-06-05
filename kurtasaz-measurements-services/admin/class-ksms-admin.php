<?php
/**
 * Admin Metabox and Layout Handler for ks_orders and WooCommerce Integration
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KSMS_Admin {

	/**
	 * Instance of this class.
	 *
	 * @var KSMS_Admin
	 */
	private static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return KSMS_Admin
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
		// Standard CPT Metaboxes.
		add_action( 'add_meta_boxes', array( $this, 'add_order_meta_boxes' ) );
		add_action( 'save_post_ks_orders', array( $this, 'save_order_meta_data' ) );

		// WooCommerce Product General Tab hooks.
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_wc_product_measurement_setting' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_wc_product_measurement_setting' ) );
	}

	/**
	 * Add Meta Box for Tailoring Orders.
	 */
	public function add_order_meta_boxes() {
		add_meta_box(
			'ksms_order_details',
			__( 'KurtaSaz Tailoring Order Details', 'kurtasaz-measurements-services' ),
			array( $this, 'render_order_details_metabox' ),
			'ks_orders',
			'normal',
			'high'
		);
	}

	/**
	 * Render the Meta Box UI for Tailoring Orders.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_order_details_metabox( $post ) {
		// Add Nonce for security verification.
		wp_nonce_field( 'ksms_save_order_details', 'ksms_order_details_nonce' );

		// Retrieve existing values.
		$service_type   = get_post_meta( $post->ID, '_ksms_service_type', true );
		$garment_type   = get_post_meta( $post->ID, '_ksms_garment_type', true );
		$standard_size  = get_post_meta( $post->ID, '_ksms_standard_size', true );
		$fabric_notes   = get_post_meta( $post->ID, '_ksms_fabric_notes', true );
		$client_name    = get_post_meta( $post->ID, '_ksms_client_name', true );
		$client_email   = get_post_meta( $post->ID, '_ksms_client_email', true );
		$client_phone   = get_post_meta( $post->ID, '_ksms_client_phone', true );

		// Retrieve measurements (Custom Silwaai).
		$measurements = array(
			'neck'           => get_post_meta( $post->ID, '_ksms_m_neck', true ),
			'shoulder'       => get_post_meta( $post->ID, '_ksms_m_shoulder', true ),
			'chest'          => get_post_meta( $post->ID, '_ksms_m_chest', true ),
			'waist'          => get_post_meta( $post->ID, '_ksms_m_waist', true ),
			'hip'            => get_post_meta( $post->ID, '_ksms_m_hip', true ),
			'kurta_length'   => get_post_meta( $post->ID, '_ksms_m_kurta_length', true ),
			'sleeve_length'  => get_post_meta( $post->ID, '_ksms_m_sleeve_length', true ),
			'armhole'        => get_post_meta( $post->ID, '_ksms_m_armhole', true ),
			'bicep'          => get_post_meta( $post->ID, '_ksms_m_bicep', true ),
			'wrist'          => get_post_meta( $post->ID, '_ksms_m_wrist', true ),
			'collar'         => get_post_meta( $post->ID, '_ksms_m_collar', true ),
			'inseam_length'  => get_post_meta( $post->ID, '_ksms_m_inseam_length', true ),
		);

		?>
		<style>
			.ksms-admin-container {
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
				padding: 10px;
			}
			.ksms-admin-section {
				margin-bottom: 25px;
				border-bottom: 1px solid #ccd0d4;
				padding-bottom: 20px;
			}
			.ksms-admin-section:last-child {
				border-bottom: none;
				padding-bottom: 0;
			}
			.ksms-admin-title {
				font-size: 14px;
				font-weight: 600;
				color: #1d2327;
				margin-bottom: 15px;
				text-transform: uppercase;
				letter-spacing: 0.5px;
				border-left: 4px solid #2271b1;
				padding-left: 8px;
			}
			.ksms-grid-2 {
				display: grid;
				grid-template-columns: 1fr 1fr;
				gap: 20px;
			}
			.ksms-grid-3 {
				display: grid;
				grid-template-columns: 1fr 1fr 1fr;
				gap: 15px;
			}
			.ksms-grid-4 {
				display: grid;
				grid-template-columns: 1fr 1fr 1fr 1fr;
				gap: 15px;
			}
			.ksms-field-group {
				display: flex;
				flex-direction: column;
				margin-bottom: 10px;
			}
			.ksms-field-group label {
				font-weight: 500;
				margin-bottom: 5px;
				color: #50575e;
			}
			.ksms-field-group input, .ksms-field-group select, .ksms-field-group textarea {
				padding: 8px 10px;
				border: 1px solid #8c8f94;
				border-radius: 4px;
				font-size: 13px;
			}
			.ksms-field-group input:focus, .ksms-field-group select:focus, .ksms-field-group textarea:focus {
				border-color: #2271b1;
				box-shadow: 0 0 0 1px #2271b1;
				outline: 2px solid transparent;
			}
		</style>

		<div class="ksms-admin-container">
			<!-- Section 1: Client Information -->
			<div class="ksms-admin-section">
				<div class="ksms-admin-title"><?php esc_html_e( 'Client Information', 'kurtasaz-measurements-services' ); ?></div>
				<div class="ksms-grid-3">
					<div class="ksms-field-group">
						<label for="ksms_client_name"><?php esc_html_e( 'Client Name', 'kurtasaz-measurements-services' ); ?></label>
						<input type="text" id="ksms_client_name" name="ksms_client_name" value="<?php echo esc_attr( $client_name ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_client_email"><?php esc_html_e( 'Email Address', 'kurtasaz-measurements-services' ); ?></label>
						<input type="email" id="ksms_client_email" name="ksms_client_email" value="<?php echo esc_attr( $client_email ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_client_phone"><?php esc_html_e( 'WhatsApp / Phone Number', 'kurtasaz-measurements-services' ); ?></label>
						<input type="text" id="ksms_client_phone" name="ksms_client_phone" value="<?php echo esc_attr( $client_phone ); ?>" />
					</div>
				</div>
			</div>

			<!-- Section 2: Order Specifications -->
			<div class="ksms-admin-section">
				<div class="ksms-admin-title"><?php esc_html_e( 'Order Parameters', 'kurtasaz-measurements-services' ); ?></div>
				<div class="ksms-grid-3">
					<div class="ksms-field-group">
						<label for="ksms_service_type"><?php esc_html_e( 'Service Type', 'kurtasaz-measurements-services' ); ?></label>
						<select id="ksms_service_type" name="ksms_service_type">
							<option value="unstitched" <?php selected( $service_type, 'unstitched' ); ?>><?php esc_html_e( 'Unstitched Fabric', 'kurtasaz-measurements-services' ); ?></option>
							<option value="stitched" <?php selected( $service_type, 'stitched' ); ?>><?php esc_html_e( 'Standard Stitched', 'kurtasaz-measurements-services' ); ?></option>
							<option value="custom" <?php selected( $service_type, 'custom' ); ?>><?php esc_html_e( 'Custom Silwaai (Tailored)', 'kurtasaz-measurements-services' ); ?></option>
						</select>
					</div>
					<div class="ksms-field-group">
						<label for="ksms_garment_type"><?php esc_html_e( 'Garment Type', 'kurtasaz-measurements-services' ); ?></label>
						<select id="ksms_garment_type" name="ksms_garment_type">
							<option value="kurta" <?php selected( $garment_type, 'kurta' ); ?>><?php esc_html_e( 'Kurta', 'kurtasaz-measurements-services' ); ?></option>
							<option value="shalwar" <?php selected( $garment_type, 'shalwar' ); ?>><?php esc_html_e( 'Shalwar', 'kurtasaz-measurements-services' ); ?></option>
							<option value="full_suit" <?php selected( $garment_type, 'full_suit' ); ?>><?php esc_html_e( 'Full Suit (Kurta & Shalwar)', 'kurtasaz-measurements-services' ); ?></option>
						</select>
					</div>
					<div class="ksms-field-group">
						<label for="ksms_standard_size"><?php esc_html_e( 'Standard Size Selection', 'kurtasaz-measurements-services' ); ?></label>
						<select id="ksms_standard_size" name="ksms_standard_size">
							<option value="" <?php selected( $standard_size, '' ); ?>><?php esc_html_e( 'None (Custom Measurement)', 'kurtasaz-measurements-services' ); ?></option>
							<option value="S" <?php selected( $standard_size, 'S' ); ?>><?php esc_html_e( 'Small (S)', 'kurtasaz-measurements-services' ); ?></option>
							<option value="M" <?php selected( $standard_size, 'M' ); ?>><?php esc_html_e( 'Medium (M)', 'kurtasaz-measurements-services' ); ?></option>
							<option value="L" <?php selected( $standard_size, 'L' ); ?>><?php esc_html_e( 'Large (L)', 'kurtasaz-measurements-services' ); ?></option>
							<option value="XL" <?php selected( $standard_size, 'XL' ); ?>><?php esc_html_e( 'Extra Large (XL)', 'kurtasaz-measurements-services' ); ?></option>
							<option value="XXL" <?php selected( $standard_size, 'XXL' ); ?>><?php esc_html_e( 'Double Extra Large (XXL)', 'kurtasaz-measurements-services' ); ?></option>
						</select>
					</div>
				</div>
			</div>

			<!-- Section 3: Tailoring Measurements (Visible/Active for Custom Silwaai) -->
			<div class="ksms-admin-section">
				<div class="ksms-admin-title"><?php esc_html_e( 'Tailoring Specifications (Inches)', 'kurtasaz-measurements-services' ); ?></div>
				<div class="ksms-grid-4">
					<div class="ksms-field-group">
						<label for="ksms_m_neck"><?php esc_html_e( 'Neck', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_neck" name="ksms_m_neck" value="<?php echo esc_attr( $measurements['neck'] ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_m_shoulder"><?php esc_html_e( 'Shoulder', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_shoulder" name="ksms_m_shoulder" value="<?php echo esc_attr( $measurements['shoulder'] ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_m_chest"><?php esc_html_e( 'Chest', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_chest" name="ksms_m_chest" value="<?php echo esc_attr( $measurements['chest'] ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_m_waist"><?php esc_html_e( 'Waist', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_waist" name="ksms_m_waist" value="<?php echo esc_attr( $measurements['waist'] ); ?>" />
					</div>
				</div>
				<div class="ksms-grid-4" style="margin-top: 10px;">
					<div class="ksms-field-group">
						<label for="ksms_m_hip"><?php esc_html_e( 'Hip', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_hip" name="ksms_m_hip" value="<?php echo esc_attr( $measurements['hip'] ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_m_kurta_length"><?php esc_html_e( 'Kurta Length', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_kurta_length" name="ksms_m_kurta_length" value="<?php echo esc_attr( $measurements['kurta_length'] ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_m_sleeve_length"><?php esc_html_e( 'Sleeve Length', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_sleeve_length" name="ksms_m_sleeve_length" value="<?php echo esc_attr( $measurements['sleeve_length'] ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_m_armhole"><?php esc_html_e( 'Armhole', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_armhole" name="ksms_m_armhole" value="<?php echo esc_attr( $measurements['armhole'] ); ?>" />
					</div>
				</div>
				<div class="ksms-grid-4" style="margin-top: 10px;">
					<div class="ksms-field-group">
						<label for="ksms_m_bicep"><?php esc_html_e( 'Bicep', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_bicep" name="ksms_m_bicep" value="<?php echo esc_attr( $measurements['bicep'] ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_m_wrist"><?php esc_html_e( 'Wrist', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_wrist" name="ksms_m_wrist" value="<?php echo esc_attr( $measurements['wrist'] ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_m_collar"><?php esc_html_e( 'Collar', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_collar" name="ksms_m_collar" value="<?php echo esc_attr( $measurements['collar'] ); ?>" />
					</div>
					<div class="ksms-field-group">
						<label for="ksms_m_inseam_length"><?php esc_html_e( 'Inseam / Shalwar Length', 'kurtasaz-measurements-services' ); ?></label>
						<input type="number" step="0.1" min="0" id="ksms_m_inseam_length" name="ksms_m_inseam_length" value="<?php echo esc_attr( $measurements['inseam_length'] ); ?>" />
					</div>
				</div>
			</div>

			<!-- Section 4: Fabric & Special Notes -->
			<div class="ksms-admin-section">
				<div class="ksms-admin-title"><?php esc_html_e( 'Fabric Preferences & Special Tailoring Notes', 'kurtasaz-measurements-services' ); ?></div>
				<div class="ksms-field-group">
					<textarea id="ksms_fabric_notes" name="ksms_fabric_notes" rows="4" style="width:100%; resize:vertical;"><?php echo esc_textarea( $fabric_notes ); ?></textarea>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save Meta Box Field Data securely.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_order_meta_data( $post_id ) {
		// Verify Nonce security field.
		if ( ! isset( $_POST['ksms_order_details_nonce'] ) || ! wp_verify_nonce( $_POST['ksms_order_details_nonce'], 'ksms_save_order_details' ) ) {
			return;
		}

		// Prevent auto-save overriding actual data.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check user permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save Client Information.
		if ( isset( $_POST['ksms_client_name'] ) ) {
			update_post_meta( $post_id, '_ksms_client_name', sanitize_text_field( wp_unslash( $_POST['ksms_client_name'] ) ) );
		}
		if ( isset( $_POST['ksms_client_email'] ) ) {
			update_post_meta( $post_id, '_ksms_client_email', sanitize_email( wp_unslash( $_POST['ksms_client_email'] ) ) );
		}
		if ( isset( $_POST['ksms_client_phone'] ) ) {
			update_post_meta( $post_id, '_ksms_client_phone', sanitize_text_field( wp_unslash( $_POST['ksms_client_phone'] ) ) );
		}

		// Save Order Parameters.
		if ( isset( $_POST['ksms_service_type'] ) ) {
			update_post_meta( $post_id, '_ksms_service_type', sanitize_text_field( wp_unslash( $_POST['ksms_service_type'] ) ) );
		}
		if ( isset( $_POST['ksms_garment_type'] ) ) {
			update_post_meta( $post_id, '_ksms_garment_type', sanitize_text_field( wp_unslash( $_POST['ksms_garment_type'] ) ) );
		}
		if ( isset( $_POST['ksms_standard_size'] ) ) {
			update_post_meta( $post_id, '_ksms_standard_size', sanitize_text_field( wp_unslash( $_POST['ksms_standard_size'] ) ) );
		}

		// Save Tailoring Measurements (Numeric Values).
		$fields = array(
			'neck'           => 'ksms_m_neck',
			'shoulder'       => 'ksms_m_shoulder',
			'chest'          => 'ksms_m_chest',
			'waist'          => 'ksms_m_waist',
			'hip'            => 'ksms_m_hip',
			'kurta_length'   => 'ksms_m_kurta_length',
			'sleeve_length'  => 'ksms_m_sleeve_length',
			'armhole'        => 'ksms_m_armhole',
			'bicep'          => 'ksms_m_bicep',
			'wrist'          => 'ksms_m_wrist',
			'collar'         => 'ksms_m_collar',
			'inseam_length'  => 'ksms_m_inseam_length',
		);

		foreach ( $fields as $meta_key => $post_key ) {
			if ( isset( $_POST[ $post_key ] ) ) {
				$val = ( $_POST[ $post_key ] === '' ) ? '' : floatval( $_POST[ $post_key ] );
				update_post_meta( $post_id, '_ksms_m_' . $meta_key, $val );
			}
		}

		// Save Fabric & Special Notes.
		if ( isset( $_POST['ksms_fabric_notes'] ) ) {
			update_post_meta( $post_id, '_ksms_fabric_notes', sanitize_textarea_field( wp_unslash( $_POST['ksms_fabric_notes'] ) ) );
		}
	}

	/**
	 * Add Checkbox setting in WooCommerce Product General Tab options.
	 */
	public function add_wc_product_measurement_setting() {
		echo '<div class="options_group">';
		
		woocommerce_wp_checkbox( array(
			'id'          => '_enable_kurtasaz_measurements',
			'label'       => __( 'Enable KurtaSaz Form', 'kurtasaz-measurements-services' ),
			'description' => __( 'Check this box to automatically render the KurtaSaz tailoring measurement form on this product page.', 'kurtasaz-measurements-services' ),
			'desc_tip'    => true,
		) );

		echo '</div>';
	}

	/**
	 * Save WooCommerce Product General Tab setting.
	 *
	 * @param int $product_id Product Post ID.
	 */
	public function save_wc_product_measurement_setting( $product_id ) {
		$enable_measurements = isset( $_POST['_enable_kurtasaz_measurements'] ) ? 'yes' : 'no';
		update_post_meta( $product_id, '_enable_kurtasaz_measurements', $enable_measurements );
	}
}
