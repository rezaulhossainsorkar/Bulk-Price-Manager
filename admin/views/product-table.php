<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="bpmx-card">

	<div class="bpmx-card__header">

		<h2>Products</h2>

		<p>
			Select the products you want to update using the configured pricing rules.
		</p>

	</div>

	<div class="bpmx-table-wrapper">

		<table class="bpmx-product-table">

			<thead>

				<tr>

					<th>
						<input
							type="checkbox"
							id="bpmx-select-all-products"
						>
					</th>

					<th>Product</th>

					<th>SKU</th>

					<th>Category</th>

					<th>Type</th>

					<th>Current Price</th>

					<th>Stock</th>

					<th>Status</th>

				</tr>

			</thead>

			<tbody>

				<?php if ( ! empty( $products ) ) : ?>

					<?php foreach ( $products as $product ) : ?>

						<?php

						/*
						 * Categories.
						 */
						$category_names = array();

						foreach ( $product->get_category_ids() as $category_id ) {

							$category = get_term(
								$category_id,
								'product_cat'
							);

							if ( $category && ! is_wp_error( $category ) ) {
								$category_names[] = $category->name;
							}
						}

						/*
						 * Product Type.
						 */
						$product_type = ucfirst(
							$product->get_type()
						);

						/*
						 * Stock Display.
						 */
						if ( $product->get_manage_stock() ) {

							$stock_display = $product->get_stock_quantity();

						} else {

							switch ( $product->get_stock_status() ) {

								case 'outofstock':
									$stock_display = 'Out of stock';
									break;

								case 'onbackorder':
									$stock_display = 'On backorder';
									break;

								default:
									$stock_display = 'In stock';
									break;
							}
						}

						?>

						<tr>

							<td>

								<input
									type="checkbox"
									class="bpmx-product-checkbox"
									name="products[]"
									value="<?php echo esc_attr( $product->get_id() ); ?>"
								>

							</td>

							<td>
								<?php echo esc_html( $product->get_name() ); ?>
							</td>

							<td>
								<?php echo esc_html( $product->get_sku() ); ?>
							</td>

							<td>

								<?php

								echo ! empty( $category_names )
									? esc_html(
										implode(
											', ',
											$category_names
										)
									)
									: '—';

								?>

							</td>

							<td>
								<?php echo esc_html( $product_type ); ?>
							</td>

							<td>
								<?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?>
							</td>

							<td>
								<?php echo esc_html( $stock_display ); ?>
							</td>

							<td>
								<?php echo esc_html( ucfirst( $product->get_status() ) ); ?>
							</td>

						</tr>

					<?php endforeach; ?>

				<?php else : ?>

					<tr>

						<td colspan="8">
							No products found.
						</td>

					</tr>

				<?php endif; ?>

			</tbody>

		</table>

	</div>

</div>