<?php
/**
 * Plugin Name:       Bulk Price Manager by BonikPress
 * Plugin URI:        https://github.com/rezaulhossainsorkar/Bulk-Price-Manager
 * Description:       Manage WooCommerce product pricing in bulk with flexible rule-based automation.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rezaul Hossain Sorkar
 * Author URI:        https://profiles.wordpress.org/rezaulhossainsorkar/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bulk-price-manager-by-bonikpress
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Plugin constants.
 */
define('BPM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BPM_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Plugin Data Integration
 */

/**==============Rendering============*/
require BPM_PLUGIN_PATH . 'includes/rendering/wc-products.php';

/**==============AJAX============*/
require_once BPM_PLUGIN_PATH . 'includes/ajax/product-table.php';

/**==============Pricing============*/
require BPM_PLUGIN_PATH . 'includes/pricing/wc-filtered-product-list.php';
require BPM_PLUGIN_PATH . 'includes/pricing/wc-price-engine-rule.php';
require BPM_PLUGIN_PATH . 'includes/pricing/wc-price-updater.php';

/**
 * Initialize plugin.
 */
function bpm_init()
{
	// Stop initialization if WooCommerce is not active.
	if (!class_exists('WooCommerce')) {
		add_action('admin_notices', 'bpm_woocommerce_required_notice');
		return;
	}

	add_action('admin_menu', 'bpm_add_admin_menu');
	add_action('admin_enqueue_scripts', 'bpm_enqueue_assets');
	add_action('admin_init', 'bpm_handle_form_submission');

}
add_action('plugins_loaded', 'bpm_init');

/**
 * Display an admin notice when WooCommerce is not installed or active.
 */
function bpm_woocommerce_required_notice()
{
	?>

	<div class="notice notice-error">

		<p>
			<strong>
				<?php esc_html_e('Bulk Price Manager by bonikpress', 'bulk-price-manager-by-bonikpress'); ?>
			</strong>
			<?php esc_html_e('requires WooCommerce to be installed and activated.', 'bulk-price-manager-by-bonikpress'); ?>
		</p>

	</div>

	<?php
}

/**
 * Register the plugin admin menu.
 */
function bpm_add_admin_menu()
{

	add_menu_page(
		'Bulk Price Manager',
		'Bulk Price Manager',
		'manage_options',
		'bulk-price-manager',
		'bpm_render_admin_page',
		'dashicons-money-alt',
		56
	);

}

/**
 * Enqueue plugin assets.
 */
function bpm_enqueue_assets()
{
	wp_enqueue_style(
		'bpm-admin-style',
		BPM_PLUGIN_URL . 'css/bpm-admin.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_style(
		'bpm-admin-header',
		BPM_PLUGIN_URL . 'css/bpm-admin-header.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_style(
		'bpm-admin-bulk-actions',
		BPM_PLUGIN_URL . 'css/bpm-admin-bulk-actions.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_style(
		'bpm-admin-filter-card',
		BPM_PLUGIN_URL . 'css/bpm-admin-filter-card.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_style(
		'bpm-admin-product-table',
		BPM_PLUGIN_URL . 'css/bpm-admin-product-table.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_script(
		'bpm-admin-script',
		BPM_PLUGIN_URL . 'js/bpm-filtered-product-list-script.js',
		array(),
		'1.0.0',
		true
	);

	wp_localize_script(
		'bpm-admin-script',
		'bpm_ajax',
		array(
			'nonce' => wp_create_nonce('bpm_product_table'),
			'apply_nonce' => wp_create_nonce('bpm_apply_changes'),
		)
	);
}
add_action('admin_enqueue_scripts', 'bpm_enqueue_assets');

/**
 * Render the plugin admin page.
 */
function bpm_render_admin_page()
{
	?>

	<?php require BPM_PLUGIN_PATH . 'admin/views/header.php'; ?>

	<?php require BPM_PLUGIN_PATH . 'admin/views/bulk-actions.php'; ?>

	<?php require BPM_PLUGIN_PATH . 'admin/views/product-filters.php'; ?>

	<div id="bpm-product-table-container">

		<p>Loading products...</p>

	</div>

	<div class="bpm-product-table-footer">

		<div class="bpm-product-filter bpm-products-per-page">

			<label for="bpm-products-per-page">
				Products per page
			</label>

			<select id="bpm-products-per-page">

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

		<div id="bpm-product-pagination"></div>

	</div>

	<?php require BPM_PLUGIN_PATH . 'admin/views/footer.php'; ?>

	<?php
}

/**
 * Handle Apply Changes requests.
 */
function bpm_handle_form_submission()
{

	/*
	 * Only process requests from this plugin page.
	 */
	if (
		!isset($_GET['page']) ||
		'bulk-price-manager' !== sanitize_key(wp_unslash($_GET['page']))
	) {
		return;
	}

	/*
	 * Only continue when the Apply Changes
	 * action has been requested.
	 */
	if (
		!isset($_POST['bpm_action']) ||
		'apply_changes' !== sanitize_key(wp_unslash($_POST['bpm_action']))
	) {
		return;
	}

	/*
	 * Verify the Apply Changes request.
	 */
	check_admin_referer(
		'bpm_apply_changes',
		'bpm_nonce'
	);

	/*
	 * Get the selected product IDs.
	 */
	$selected_products = array();

	if (
		isset($_POST['products']) &&
		is_array($_POST['products'])
	) {

		$selected_products = array_map(
			'absint',
			wp_unslash($_POST['products'])
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

		'method' => isset($_POST['price_method'])
			? sanitize_key(wp_unslash($_POST['price_method']))
			: '',

		'target' => isset($_POST['price_target'])
			? sanitize_key(wp_unslash($_POST['price_target']))
			: '',

		'value' => isset($_POST['price_value'])
			? floatval(wp_unslash($_POST['price_value']))
			: 0,

	);

	/*
	 * Prepare pricing pipeline data.
	 */
	$pipeline_data = array(
		'products' => $selected_products,
		'rule' => $price_rule,
	);

	/*
	 * Begin the pricing pipeline.
	 */
	do_action(
		'bpm_apply_changes',
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

add_action('admin_init', 'bpm_handle_form_submission');