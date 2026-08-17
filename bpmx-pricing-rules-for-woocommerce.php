<?php
/**
 * Plugin Name:       BPMX Pricing Rules for WooCommerce
 * Plugin URI:        https://github.com/rezaulhossainsorkar/bpmx-pricing-rules-for-woocommerce
 * Description:       Manage WooCommerce product pricing in bulk with flexible rule-based automation.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rezaul Hossain Sorkar
 * Author URI:        https://profiles.wordpress.org/rezaulhossainsorkar/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bpmx-pricing-rules-for-woocommerce
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin constants.
 */
define( 'BPMX_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BPMX_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin Data Integration.
 */

/**==============Rendering============*/
require BPMX_PLUGIN_PATH . 'includes/rendering/wc-products.php';

/**==============AJAX============*/
require_once BPMX_PLUGIN_PATH . 'includes/ajax/product-table.php';

/**==============Pricing============*/
require BPMX_PLUGIN_PATH . 'includes/pricing/wc-filtered-product-list.php';
require BPMX_PLUGIN_PATH . 'includes/pricing/wc-price-engine-rule.php';
require BPMX_PLUGIN_PATH . 'includes/pricing/wc-price-updater.php';

/**
 * Initialize plugin.
 */
function bpmx_init() {

	// Stop initialization if WooCommerce is not active.
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'bpmx_woocommerce_required_notice' );
		return;
	}

	add_action( 'admin_menu', 'bpmx_add_admin_menu' );
	add_action( 'admin_enqueue_scripts', 'bpmx_enqueue_assets' );
	add_action( 'admin_init', 'bpmx_handle_form_submission' );
}
add_action( 'plugins_loaded', 'bpmx_init' );

/**
 * Display an admin notice when WooCommerce is not installed or active.
 */
function bpmx_woocommerce_required_notice() {
	?>

	<div class="notice notice-error">

		<p>
			<strong>
				<?php esc_html_e( 'BPMX Pricing Rules for WooCommerce', 'bpmx-pricing-rules-for-woocommerce' ); ?>
			</strong>
			<?php esc_html_e( 'requires WooCommerce to be installed and activated.', 'bpmx-pricing-rules-for-woocommerce' ); ?>
		</p>

	</div>

	<?php
}

/**
 * Register the plugin admin menu.
 */
function bpmx_add_admin_menu() {

	add_menu_page(
		'BPMX Pricing Rules',
		'BPMX Pricing Rules',
		'manage_options',
		'bpmx-pricing-rules',
		'bpmx_render_admin_page',
		'dashicons-money-alt',
		56
	);
}

/**
 * Enqueue plugin assets.
 */
function bpmx_enqueue_assets() {

	wp_enqueue_style(
		'bpmx-admin-style',
		BPMX_PLUGIN_URL . 'css/bpmx-admin.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_style(
		'bpmx-admin-header',
		BPMX_PLUGIN_URL . 'css/bpmx-admin-header.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_style(
		'bpmx-admin-bulk-actions',
		BPMX_PLUGIN_URL . 'css/bpmx-admin-bulk-actions.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_style(
		'bpmx-admin-filter-card',
		BPMX_PLUGIN_URL . 'css/bpmx-admin-filter-card.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_style(
		'bpmx-admin-product-table',
		BPMX_PLUGIN_URL . 'css/bpmx-admin-product-table.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_script(
		'bpmx-admin-script',
		BPMX_PLUGIN_URL . 'js/bpmx-filtered-product-list-script.js',
		array(),
		'1.0.0',
		true
	);

	wp_localize_script(
		'bpmx-admin-script',
		'bpmx_ajax',
		array(
			'nonce'       => wp_create_nonce( 'bpmx_product_table' ),
			'apply_nonce' => wp_create_nonce( 'bpmx_apply_changes' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'bpmx_enqueue_assets' );

/**
 * Render the plugin admin page.
 */
function bpmx_render_admin_page() {
	?>

	<?php require BPMX_PLUGIN_PATH . 'admin/views/header.php'; ?>

	<?php require BPMX_PLUGIN_PATH . 'admin/views/bulk-actions.php'; ?>

	<?php require BPMX_PLUGIN_PATH . 'admin/views/product-filters.php'; ?>

	<div id="bpmx-product-table-container">

		<p>Loading products...</p>

	</div>

	<div class="bpmx-product-table-footer">

		<div class="bpmx-product-filter bpmx-products-per-page">

			<label for="bpmx-products-per-page">
				Products per page
			</label>

			<select id="bpmx-products-per-page">

				<option value="10">
					10
				</option>

				<option value="20" selected>
					20
				</option>

				<option value="30">
					30
				</option>

				<option value="50">
					50
				</option>

			</select>

		</div>

		<div id="bpmx-product-pagination"></div>

	</div>

	<?php require BPMX_PLUGIN_PATH . 'admin/views/footer.php'; ?>

	<?php
}

/**
 * Handle Apply Changes requests.
 */
function bpmx_handle_form_submission() {

	/*
	 * Only process requests from this plugin page.
	 */
	if (
		! isset( $_GET['page'] ) ||
		'bpmx-pricing-rules' !== sanitize_key( wp_unslash( $_GET['page'] ) )
	) {
		return;
	}

	/*
	 * Only continue when the Apply Changes
	 * action has been requested.
	 */
	if (
		! isset( $_POST['bpmx_action'] ) ||
		'apply_changes' !== sanitize_key( wp_unslash( $_POST['bpmx_action'] ) )
	) {
		return;
	}

	/*
	 * Verify the Apply Changes request.
	 */
	check_admin_referer(
		'bpmx_apply_changes',
		'bpmx_nonce'
	);

	/*
	 * Get the selected product IDs.
	 */
	$selected_products = array();

	if (
		isset( $_POST['products'] ) &&
		is_array( $_POST['products'] )
	) {

		$selected_products = array_map(
			'absint',
			wp_unslash( $_POST['products'] )
		);

		$selected_products = array_filter(
			$selected_products
		);

		$selected_products = array_unique(
			$selected_products
		);
	}

	/*
	 * Get pricing rule configuration.
	 */
	$price_rule = array(

		'method' => isset( $_POST['price_method'] )
			? sanitize_key( wp_unslash( $_POST['price_method'] ) )
			: '',

		'target' => isset( $_POST['price_target'] )
			? sanitize_key( wp_unslash( $_POST['price_target'] ) )
			: '',

		'value' => isset( $_POST['price_value'] )
			? floatval( wp_unslash( $_POST['price_value'] ) )
			: 0,

	);

	/*
	 * Prepare pricing pipeline data.
	 */
	$pipeline_data = array(
		'products' => $selected_products,
		'rule'     => $price_rule,
	);

	/*
	 * Begin the pricing pipeline.
	 */
	do_action(
		'bpmx_apply_changes',
		$pipeline_data
	);

	/*
	 * Return a success response.
	 */
	wp_send_json_success(
		array(
			'message' => 'Products updated successfully.',
		)
	);
}

add_action( 'admin_init', 'bpmx_handle_form_submission' );