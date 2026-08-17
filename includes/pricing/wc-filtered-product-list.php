<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register callbacks.
 */
add_action(
	'bpmx_apply_changes',
	'bpmx_store_selected_products',
	10,
	1
);

/**
 * Validate the selected product IDs.
 *
 * @param array $pipeline_data Pricing pipeline data.
 */
function bpmx_store_selected_products( $pipeline_data ) {

	if ( ! isset( $pipeline_data['products'] ) ) {
		return;
	}
}