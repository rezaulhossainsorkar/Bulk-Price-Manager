<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Register price updater.
 */
add_action(
	'bpmx_apply_changes',
	'bpmx_update_product_prices',
	30,
	1
);

/**
 * Update the selected product prices.
 *
 * @param array $pipeline_data Pricing pipeline data.
 */
function bpmx_update_product_prices( $pipeline_data ) {

	/*
	 * Ensure products exist.
	 */
	if ( empty( $pipeline_data['products'] ) ) {

		return;

	}

	/*
	 * Ensure a pricing rule exists.
	 */
	if ( empty( $pipeline_data['rule'] ) ) {

		return;

	}

	$products = $pipeline_data['products'];
	$rule     = $pipeline_data['rule'];

	foreach ( $products as $product_id ) {

		$product = wc_get_product( $product_id );

		if ( ! $product ) {

			continue;

		}

		/*
		 * Variable products do not have prices themselves.
		 * Update each variation instead.
		 */
		if ( $product->is_type( 'variable' ) ) {

			$variation_ids = $product->get_children();

			foreach ( $variation_ids as $variation_id ) {

				$variation = wc_get_product( $variation_id );

				if ( ! $variation ) {

					continue;

				}

				bpmx_update_single_product_price(
					$variation,
					$rule
				);
			}

			continue;
		}

		/*
		 * Update simple products and individual variations.
		 */
		bpmx_update_single_product_price(
			$product,
			$rule
		);
	}
}

/**
 * Update a single product price.
 *
 * @param WC_Product $product WooCommerce product object.
 * @param array      $rule    Pricing rule.
 */
function bpmx_update_single_product_price( $product, $rule ) {

	/*
	 * Ensure a valid pricing target exists.
	 */
	if ( empty( $rule['target'] ) ) {

		return;

	}

	/*
	 * Update the regular price.
	 */
	if (
		'regular_price' === $rule['target'] ||
		'both' === $rule['target']
	) {

		$regular_price = $product->get_regular_price();

		if ( '' !== $regular_price ) {

			$regular_price = (float) $regular_price;

			$new_regular_price = bpmx_calculate_new_price(
				$regular_price,
				$rule
			);

			$product->set_regular_price( $new_regular_price );

			/*
			 * If there is no sale price, the active price
			 * should follow the new regular price.
			 */
			if ( '' === $product->get_sale_price() ) {

				$product->set_price( $new_regular_price );

			}
		}
	}

	/*
	 * Update the sale price.
	 */
	if (
		'sale_price' === $rule['target'] ||
		'both' === $rule['target']
	) {

		$sale_price = $product->get_sale_price();

		if ( '' !== $sale_price ) {

			$sale_price = (float) $sale_price;

			$new_sale_price = bpmx_calculate_new_price(
				$sale_price,
				$rule
			);

			$product->set_sale_price( $new_sale_price );

			/*
			 * The sale price is the active price.
			 */
			$product->set_price( $new_sale_price );
		}
	}

	$product->save();
}

/**
 * Calculate a new price from the pricing rule.
 *
 * @param float $current_price Current product price.
 * @param array $rule          Pricing rule.
 * @return float
 */
function bpmx_calculate_new_price( $current_price, $rule ) {

	$new_price = $current_price;

	switch ( $rule['method'] ) {

		case 'increase_fixed':

			$new_price = $current_price + (float) $rule['value'];

			break;

		case 'decrease_fixed':

			$new_price = $current_price - (float) $rule['value'];

			break;

		case 'increase_percentage':

			$new_price = $current_price + (
				$current_price * (float) $rule['value'] / 100
			);

			break;

		case 'decrease_percentage':

			$new_price = $current_price - (
				$current_price * (float) $rule['value'] / 100
			);

			break;
	}

	/*
	 * Prevent negative prices.
	 */
	return max( 0, $new_price );
}