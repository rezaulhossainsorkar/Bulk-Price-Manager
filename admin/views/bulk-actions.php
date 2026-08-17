<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="bpmx-card__header">

	<h2>BPMX Pricing Rules</h2>

	<p>
		Configure how the selected WooCommerce products should be updated.
	</p>

</div>

<div class="bpmx-rule-grid">

	<div class="bpmx-rule bpmx-rule-global">

		<h3>Price Adjustment</h3>

		<p>
			Choose a pricing operation, the price type to update, and the value to apply to all selected products.
		</p>

		<div class="bpmx-inline-fields">

			<div>

				<label for="bpmx-price-method">
					Operation
				</label>

				<select id="bpmx-price-method" name="price_method">

					<option value="increase_percentage">
						Increase by Percentage (%)
					</option>

					<option value="decrease_percentage">
						Decrease by Percentage (%)
					</option>

					<option value="increase_fixed">
						Increase by Fixed Amount
					</option>

					<option value="decrease_fixed">
						Decrease by Fixed Amount
					</option>

				</select>

			</div>

			<div>

				<label for="bpmx-price-target">
					Price Type
				</label>

				<select id="bpmx-price-target" name="price_target">

					<option value="regular_price">
						Regular Price
					</option>

					<option value="sale_price">
						Sale Price
					</option>

					<option value="both">
						Regular &amp; Sale Price
					</option>

				</select>

			</div>

			<div>

				<label for="bpmx-price-value">
					Value
				</label>

				<input
					type="number"
					id="bpmx-price-value"
					name="price_value"
					placeholder="10"
					step="0.01"
					min="0"
				>

			</div>

		</div>

	</div>

</div>