<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Register pricing rule callback.
 */
add_action(
	'bpmx_apply_changes',
	'bpmx_store_price_engine_rule',
	20,
	1
);

/**
 * Validate the pricing rule.
 *
 * @param array $pipeline_data Pricing pipeline data.
 */
function bpmx_store_price_engine_rule( $pipeline_data ) {

	/*
	 * Ensure a pricing rule exists.
	 */
	if ( empty( $pipeline_data['rule'] ) ) {

		return;

	}

	$rule = $pipeline_data['rule'];

	/*
	 * Ensure a valid pricing method exists.
	 */
	$allowed_methods = array(
		'increase_percentage',
		'decrease_percentage',
		'increase_fixed',
		'decrease_fixed',
	);

	if (
		empty( $rule['method'] ) ||
		! in_array( $rule['method'], $allowed_methods, true )
	) {

		return;

	}

	/*
	 * Ensure a valid pricing target exists.
	 */
	$allowed_targets = array(
		'regular_price',
		'sale_price',
		'both',
	);

	if (
		empty( $rule['target'] ) ||
		! in_array( $rule['target'], $allowed_targets, true )
	) {

		return;

	}

	/*
	 * Ensure the pricing value exists.
	 */
	if ( ! isset( $rule['value'] ) || ! is_numeric( $rule['value'] ) ) {

		return;

	}

}