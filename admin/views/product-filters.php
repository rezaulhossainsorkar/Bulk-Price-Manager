<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="bpm-product-filters">

	<div class="bpm-product-filters-header">

		<h2>Filter Products</h2>

		<p>
			Use the filters below to find the products you want to update.
		</p>

	</div>

	<div class="bpm-product-filter">

		<label for="bpm-search">
			Search
		</label>

		<input type="search" id="bpm-search" placeholder="Search products">

	</div>

	<div class="bpm-product-filter">

		<label for="bpm-category">
			Category
		</label>

		<select id="bpm-category">

			<option value="">
				All Categories
			</option>

			<?php

			$categories = get_terms(
				array(
					'taxonomy' => 'product_cat',
					'hide_empty' => false,
				)
			);

			?>

			<?php if (!is_wp_error($categories)): ?>

				<?php foreach ($categories as $category): ?>

					<option value="<?php echo esc_attr($category->slug); ?>">
						<?php echo esc_html($category->name); ?>
					</option>

				<?php endforeach; ?>

			<?php endif; ?>

		</select>

	</div>

	<div class="bpm-product-filter">

		<label for="bpm-type">
			Type
		</label>

		<select id="bpm-type">

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

	<div class="bpm-product-filter">

		<label for="bpm-stock-status">
			Stock Status
		</label>

		<select id="bpm-stock-status">

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

	<div class="bpm-product-filter">

		<label for="bpm-status">
			Status
		</label>

		<select id="bpm-status">

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