<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register callbacks.
 */
add_action(
	'bpm_apply_changes',
	'bpm_store_selected_products',
	10,
	1
);

/**
 * Validate the selected product IDs.
 *
 * @param array $pipeline_data Pricing pipeline data.
 */
function bpm_store_selected_products( $pipeline_data ) {

	if ( ! isset( $pipeline_data['products'] ) ) {
		return;
	}


}