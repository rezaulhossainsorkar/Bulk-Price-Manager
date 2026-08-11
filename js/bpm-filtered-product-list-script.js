document.addEventListener('DOMContentLoaded', function () {

	/*
	 * Product table container.
	 */
	const tableContainer = document.getElementById(
		'bpm-product-table-container'
	);

	let bpmCurrentPage = 1;


	/*
	 * Store selected product IDs.
	 *
	 * This persists selections while the AJAX
	 * table is being replaced.
	 */
	const bpmSelectedProducts = new Set();


	/*
	 * Load the product table.
	 *
	 * @param {number} page Current page.
	 */
	function bpmLoadProductTable(page = 1) {

		bpmCurrentPage = page;

		if (!tableContainer) {
			return;
		}


		const formData = new URLSearchParams();


		/*
		 * AJAX action.
		 */
		formData.append(
			'action',
			'bpm_product_table'
		);

		formData.append(
			'nonce',
			bpm_ajax.nonce
		);


		/*
		 * Current page.
		 */
		formData.append(
			'page',
			page
		);


		/*
		 * Products per page.
		 */
		const productsPerPage = document.getElementById(
			'bpm-products-per-page'
		);

		formData.append(
			'per_page',
			productsPerPage
				? productsPerPage.value
				: '20'
		);


		/*
		 * Product search.
		 */
		const search = document.getElementById(
			'bpm-search'
		);

		formData.append(
			'search',
			search ? search.value : ''
		);


		/*
		 * Product category.
		 */
		const category = document.getElementById(
			'bpm-category'
		);

		formData.append(
			'category',
			category ? category.value : ''
		);


		/*
		 * Product type.
		 */
		const type = document.getElementById(
			'bpm-type'
		);

		formData.append(
			'type',
			type ? type.value : ''
		);


		/*
		 * Stock status.
		 */
		const stockStatus = document.getElementById(
			'bpm-stock-status'
		);

		formData.append(
			'stock_status',
			stockStatus ? stockStatus.value : ''
		);


		/*
		 * Product status.
		 */
		const status = document.getElementById(
			'bpm-status'
		);

		formData.append(
			'status',
			status ? status.value : ''
		);


		/*
		 * Request products.
		 */
		fetch(window.ajaxurl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: formData
		})
			.then(function (response) {

				return response.json();

			})
			.then(function (response) {

				if (!response.success) {
					return;
				}


				/*
				 * Replace the product table.
				 */
				tableContainer.innerHTML = response.data.html;


				/*
				 * Restore selections for the
				 * newly rendered products.
				 */
				bpmInitializeProductSelection();


				/*
				 * Render pagination.
				 */
				bpmRenderPagination(
					page,
					response.data.max_num_pages
				);

			})
			.catch(function (error) {

				console.error(error);

			});

	}


	/*
	 * Initialize product selection.
	 */
	function bpmInitializeProductSelection() {

		const selectAll = document.getElementById(
			'bpm-select-all-products'
		);

		if (!selectAll) {
			return;
		}


		const checkboxes = document.querySelectorAll(
			'.bpm-product-checkbox'
		);


		/*
		 * Restore previously selected products.
		 */
		checkboxes.forEach(function (checkbox) {

			checkbox.checked = bpmSelectedProducts.has(
				checkbox.value
			);

		});


		/*
		 * Update Select All state for
		 * the currently visible products.
		 */
		const selectedVisibleProducts = document.querySelectorAll(
			'.bpm-product-checkbox:checked'
		).length;

		selectAll.checked = (
			checkboxes.length > 0 &&
			selectedVisibleProducts === checkboxes.length
		);


		/*
		 * Select / deselect all products
		 * on the current page.
		 */
		selectAll.addEventListener('change', function () {

			checkboxes.forEach(function (checkbox) {

				checkbox.checked = selectAll.checked;


				if (selectAll.checked) {

					bpmSelectedProducts.add(
						checkbox.value
					);

				} else {

					bpmSelectedProducts.delete(
						checkbox.value
					);

				}

			});

		});


		/*
		 * Update the persistent selection
		 * when an individual checkbox changes.
		 */
		checkboxes.forEach(function (checkbox) {

			checkbox.addEventListener('change', function () {

				if (checkbox.checked) {

					bpmSelectedProducts.add(
						checkbox.value
					);

				} else {

					bpmSelectedProducts.delete(
						checkbox.value
					);

				}


				/*
				 * Update Select All for the
				 * currently visible products.
				 */
				const checked = document.querySelectorAll(
					'.bpm-product-checkbox:checked'
				).length;

				selectAll.checked = (
					checkboxes.length > 0 &&
					checked === checkboxes.length
				);

			});

		});

	}


	/*
	 * Render pagination buttons.
	 *
	 * Uses a compact layout so that thousands
	 * of pages do not produce thousands of buttons.
	 */
	function bpmRenderPagination(
		currentPage,
		maxPages
	) {

		const pagination = document.getElementById(
			'bpm-product-pagination'
		);

		if (!pagination) {
			return;
		}


		pagination.innerHTML = '';


		/*
		 * No pagination needed when
		 * there is only one page.
		 */
		if (maxPages <= 1) {
			return;
		}


		const fragment = document.createDocumentFragment();


		/*
		 * Create a page button.
		 */
		function createPageButton(
			page,
			isCurrent = false
		) {

			const button = document.createElement(
				'button'
			);

			button.type = 'button';

			button.className = 'button';

			button.textContent = page;


			if (isCurrent) {

				button.classList.add(
					'button-primary'
				);

				button.disabled = true;

			}


			button.addEventListener(
				'click',
				function () {

					bpmLoadProductTable(
						page
					);

				}
			);


			return button;

		}


		/*
		 * Create an ellipsis.
		 */
		function createEllipsis() {

			const span = document.createElement(
				'span'
			);

			span.textContent = '...';

			span.style.margin = '0 4px';

			return span;

		}


		/*
		 * Previous button.
		 */
		if (currentPage > 1) {

			const previousButton = document.createElement(
				'button'
			);

			previousButton.type = 'button';

			previousButton.className = 'button';

			previousButton.textContent = 'Previous';


			previousButton.addEventListener(
				'click',
				function () {

					bpmLoadProductTable(
						currentPage - 1
					);

				}
			);


			fragment.appendChild(
				previousButton
			);

		}


		/*
		 * First page.
		 */
		fragment.appendChild(
			createPageButton(
				1,
				currentPage === 1
			)
		);


		/*
		 * Determine the visible page range.
		 */
		let startPage;
		let endPage;


		if (maxPages <= 7) {

			startPage = 2;
			endPage = maxPages - 1;

		} else if (currentPage <= 4) {

			startPage = 2;
			endPage = 5;

		} else if (currentPage >= maxPages - 3) {

			startPage = maxPages - 4;
			endPage = maxPages - 1;

		} else {

			startPage = currentPage - 2;
			endPage = currentPage + 2;

		}


		/*
		 * Ellipsis after the first page.
		 */
		if (startPage > 2) {

			fragment.appendChild(
				createEllipsis()
			);

		}


		/*
		 * Middle page buttons.
		 */
		for (
			let page = startPage;
			page <= endPage;
			page++
		) {

			fragment.appendChild(
				createPageButton(
					page,
					page === currentPage
				)
			);

		}


		/*
		 * Ellipsis before the last page.
		 */
		if (endPage < maxPages - 1) {

			fragment.appendChild(
				createEllipsis()
			);

		}


		/*
		 * Last page.
		 */
		if (maxPages > 1) {

			fragment.appendChild(
				createPageButton(
					maxPages,
					currentPage === maxPages
				)
			);

		}


		/*
		 * Next button.
		 */
		if (currentPage < maxPages) {

			const nextButton = document.createElement(
				'button'
			);

			nextButton.type = 'button';

			nextButton.className = 'button';

			nextButton.textContent = 'Next';


			nextButton.addEventListener(
				'click',
				function () {

					bpmLoadProductTable(
						currentPage + 1
					);

				}
			);


			fragment.appendChild(
				nextButton
			);

		}


		pagination.appendChild(
			fragment
		);

	}


	/*
	 * Search input.
	 *
	 * Wait briefly after the user stops typing
	 * before sending the AJAX request.
	 */
	const searchInput = document.getElementById(
		'bpm-search'
	);

	let searchTimeout;


	if (searchInput) {

		searchInput.addEventListener(
			'input',
			function () {

				clearTimeout(
					searchTimeout
				);


				searchTimeout = setTimeout(
					function () {

						bpmLoadProductTable(1);

					},
					300
				);

			}
		);

	}


	/*
	 * Category filter.
	 */
	const categoryFilter = document.getElementById(
		'bpm-category'
	);

	if (categoryFilter) {

		categoryFilter.addEventListener(
			'change',
			function () {

				bpmLoadProductTable(1);

			}
		);

	}


	/*
	 * Product type filter.
	 */
	const typeFilter = document.getElementById(
		'bpm-type'
	);

	if (typeFilter) {

		typeFilter.addEventListener(
			'change',
			function () {

				bpmLoadProductTable(1);

			}
		);

	}


	/*
	 * Stock status filter.
	 */
	const stockStatusFilter = document.getElementById(
		'bpm-stock-status'
	);

	if (stockStatusFilter) {

		stockStatusFilter.addEventListener(
			'change',
			function () {

				bpmLoadProductTable(1);

			}
		);

	}


	/*
	 * Product status filter.
	 */
	const statusFilter = document.getElementById(
		'bpm-status'
	);

	if (statusFilter) {

		statusFilter.addEventListener(
			'change',
			function () {

				bpmLoadProductTable(1);

			}
		);

	}


	/*
	 * Products per page.
	 */
	const productsPerPage = document.getElementById(
		'bpm-products-per-page'
	);

	if (productsPerPage) {

		productsPerPage.addEventListener(
			'change',
			function () {

				bpmLoadProductTable(1);

			}
		);

	}


	/*
	 * Apply Changes.
	 */
	const applyButton = document.getElementById(
		'bpm-apply-changes'
	);

	if (applyButton) {

		applyButton.addEventListener(
			'click',
			function () {

				const formData = new URLSearchParams();


				/*
				 * Trigger the Apply Changes request.
				 */
				formData.append(
					'bpm_action',
					'apply_changes'
				);

				formData.append(
					'bpm_nonce',
					bpm_ajax.apply_nonce
				);


				/*
				 * Send every selected product ID,
				 * including products selected on
				 * previous AJAX pages.
				 */
				bpmSelectedProducts.forEach(
					function (productId) {

						formData.append(
							'products[]',
							productId
						);

					}
				);


				/*
				 * Collect pricing rule.
				 */
				const priceMethod = document.getElementById(
					'bpm-price-method'
				);

				const priceTarget = document.getElementById(
					'bpm-price-target'
				);

				const priceValue = document.getElementById(
					'bpm-price-value'
				);


				if (priceMethod) {

					formData.append(
						'price_method',
						priceMethod.value
					);

				}


				if (priceTarget) {

					formData.append(
						'price_target',
						priceTarget.value
					);

				}


				if (priceValue) {

					formData.append(
						'price_value',
						priceValue.value
					);

				}


				/*
				 * Submit Apply Changes.
				 */
				fetch(window.location.href, {
					method: 'POST',
					headers: {
						'Content-Type':
							'application/x-www-form-urlencoded',
					},
					body: formData
				})
					.then(function (response) {

						return response.text();

					})
					.then(function () {

						/*
						 * Clear all selected products
						 * after Apply Changes.
						 */
						bpmSelectedProducts.clear();


						/*
						 * Reload the current table page.
						 */
						bpmLoadProductTable(
							bpmCurrentPage
						);

					})
					.catch(function (error) {

						console.error(error);

					});

			}
		);

	}


	/*
	 * Load the initial product table.
	 */
	bpmLoadProductTable(1);

});