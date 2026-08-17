<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle product table AJAX requests.
 */
function bpmx_ajax_product_table() {

	check_ajax_referer( 'bpmx_product_table', 'nonce' );

	$filters = array(

		'search' => isset( $_POST['search'] )
			? sanitize_text_field( wp_unslash( $_POST['search'] ) )
			: '',

		'category' => isset( $_POST['category'] )
			? sanitize_title( wp_unslash( $_POST['category'] ) )
			: '',

		'type' => isset( $_POST['type'] )
			? sanitize_key( wp_unslash( $_POST['type'] ) )
			: '',

		'stock_status' => isset( $_POST['stock_status'] )
			? sanitize_key( wp_unslash( $_POST['stock_status'] ) )
			: '',

		'status' => isset( $_POST['status'] )
			? sanitize_key( wp_unslash( $_POST['status'] ) )
			: '',

		'page' => isset( $_POST['page'] )
			? absint( wp_unslash( $_POST['page'] ) )
			: 1,

		'per_page' => isset( $_POST['per_page'] )
			? absint( wp_unslash( $_POST['per_page'] ) )
			: 20,
	);

	$product_result = bpmx_get_products( $filters );

	$products = $product_result->products;

	$total = $product_result->total;

	$max_num_pages = $product_result->max_num_pages;

	ob_start();

	include BPMX_PLUGIN_PATH . 'admin/views/product-table.php';

	$table_html = ob_get_clean();

	wp_send_json_success(
		array(
			'html'          => $table_html,
			'total'         => $total,
			'max_num_pages' => $max_num_pages,
		)
	);
}

add_action( 'wp_ajax_bpmx_product_table', 'bpmx_ajax_product_table' );