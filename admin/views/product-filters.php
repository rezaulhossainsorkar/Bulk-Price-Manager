<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="bpmx-product-filters">

	<div class="bpmx-product-filters-header">

		<h2>Filter Products</h2>

		<p>
			Use the filters below to find the products you want to update.
		</p>

	</div>

	<div class="bpmx-product-filter">

		<label for="bpmx-search">
			Search
		</label>

		<input
			type="search"
			id="bpmx-search"
			placeholder="Search products"
		>

	</div>

	<div class="bpmx-product-filter">

		<label for="bpmx-category">
			Category
		</label>

		<select id="bpmx-category">

			<option value="">
				All Categories
			</option>

			<?php

			$categories = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
				)
			);

			?>

			<?php if ( ! is_wp_error( $categories ) ) : ?>

				<?php foreach ( $categories as $category ) : ?>

					<option value="<?php echo esc_attr( $category->slug ); ?>">
						<?php echo esc_html( $category->name ); ?>
					</option>

				<?php endforeach; ?>

			<?php endif; ?>

		</select>

	</div>

	<div class="bpmx-product-filter">

		<label for="bpmx-type">
			Type
		</label>

		<select id="bpmx-type">

			<option value="">
				All Types
			</option>

			<option value="simple">
				Simple
			</option>

			<option value="variable">
				Variable
			</option>

			<option value="grouped">
				Grouped
			</option>

			<option value="external">
				External
			</option>

		</select>

	</div>

	<div class="bpmx-product-filter">

		<label for="bpmx-stock-status">
			Stock Status
		</label>

		<select id="bpmx-stock-status">

			<option value="">
				All Stock Statuses
			</option>

			<option value="instock">
				In stock
			</option>

			<option value="outofstock">
				Out of stock
			</option>

			<option value="onbackorder">
				On backorder
			</option>

		</select>

	</div>

	<div class="bpmx-product-filter">

		<label for="bpmx-status">
			Status
		</label>

		<select id="bpmx-status">

			<option value="">
				All Statuses
			</option>

			<option value="publish">
				Published
			</option>

			<option value="draft">
				Draft
			</option>

			<option value="pending">
				Pending
			</option>

			<option value="private">
				Private
			</option>

		</select>

	</div>

</div>