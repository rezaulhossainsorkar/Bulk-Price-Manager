<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Retrieve WooCommerce products.
 *
 * @param array $filters Product filters and pagination settings.
 * @return array
 */
function bpm_get_products($filters = array())
{

	$args = array(
		'limit' => 20,
		'paged' => 1,
	);

	/*
	 * Products per page.
	 */
	if (!empty($filters['per_page'])) {

		$args['limit'] = absint(
			$filters['per_page']
		);

	}

	/*
	 * Current page.
	 */
	if (!empty($filters['page'])) {

		$args['paged'] = absint(
			$filters['page']
		);

	}

	/*
	 * Search products by name or SKU.
	 */
	if (!empty($filters['search'])) {

		$args['bpm_search'] = $filters['search'];

	}

	/*
	 * Filter by product category.
	 */
	if (!empty($filters['category'])) {

		$args['category'] = array(
			$filters['category'],
		);

	}

	/*
	 * Filter by product type.
	 */
	if (!empty($filters['type'])) {

		$args['type'] = $filters['type'];

	}

	/*
	 * Filter by stock status.
	 */
	if (!empty($filters['stock_status'])) {

		$args['stock_status'] = $filters['stock_status'];

	}

	/*
	 * Filter by product status.
	 */
	if (!empty($filters['status'])) {

		$args['status'] = $filters['status'];

	}

	/*
	 * Return pagination information.
	 */
	$args['paginate'] = true;

	return wc_get_products($args);
}


/**
 * Add product search support to the WooCommerce product query.
 *
 * Searches products by name or SKU.
 *
 * @param array $query       Product query arguments.
 * @param array $query_vars  WooCommerce product query variables.
 * @return array
 */
function bpm_handle_product_search( $query, $query_vars ) {

	if ( empty( $query_vars['bpm_search'] ) ) {
		return $query;
	}

	$search = sanitize_text_field(
		$query_vars['bpm_search']
	);

	$query['s'] = $search;

	return $query;
}

add_filter(
	'woocommerce_product_data_store_cpt_get_products_query',
	'bpm_handle_product_search',
	10,
	2
);