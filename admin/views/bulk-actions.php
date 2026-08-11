<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="bpm-card__header">

	<h2>Bulk Price Manager</h2>

	<p>
		Configure how the selected WooCommerce products should be updated.
	</p>

</div>

<div class="bpm-rule-grid">

	<div class="bpm-rule bpm-rule-global">

		<h3>Price Adjustment</h3>

		<p>
			Choose a pricing operation, the price type to update, and the value to apply to all selected products.
		</p>

		<div class="bpm-inline-fields">

			<div>

				<label for="bpm-price-method">
					Operation
				</label>

				<select id="bpm-price-method" name="price_method">

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

				<label for="bpm-price-target">
					Price Type
				</label>

				<select id="bpm-price-target" name="price_target">

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

				<label for="bpm-price-value">
					Value
				</label>

				<input type="number" id="bpm-price-value" name="price_value" placeholder="10" step="0.01" min="0">

			</div>

		</div>

	</div>

</div>