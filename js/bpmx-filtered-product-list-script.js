document.addEventListener('DOMContentLoaded', function() {

    /*
     * Product table container.
     */
    const tableContainer = document.getElementById('bpmx-product-table-container');
    let bpmxCurrentPage = 1;

    /*
     * Store selected product IDs.
     *
     * This persists selections while the AJAX
     * table is being replaced.
     */
    const bpmxSelectedProducts = new Set();

    /*
     * Load the product table.
     *
     * @param {number} page Current page.
     */
    function bpmxLoadProductTable(page = 1) {

        bpmxCurrentPage = page;

        if (!tableContainer) {
            return;
        }

        const formData = new URLSearchParams();

        /*
         * AJAX action.
         */
        formData.append('action', 'bpmx_product_table');
        formData.append('nonce', bpmx_ajax.nonce);

        /*
         * Current page.
         */
        formData.append('page', page);

        /*
         * Products per page.
         */
        const productsPerPage = document.getElementById('bpmx-products-per-page');

        formData.append(
            'per_page',
            productsPerPage ? productsPerPage.value : '20'
        );

        /*
         * Product search.
         */
        const search = document.getElementById('bpmx-search');

        formData.append(
            'search',
            search ? search.value : ''
        );

        /*
         * Product category.
         */
        const category = document.getElementById('bpmx-category');

        formData.append(
            'category',
            category ? category.value : ''
        );

        /*
         * Product type.
         */
        const type = document.getElementById('bpmx-type');

        formData.append(
            'type',
            type ? type.value : ''
        );

        /*
         * Stock status.
         */
        const stockStatus = document.getElementById('bpmx-stock-status');

        formData.append(
            'stock_status',
            stockStatus ? stockStatus.value : ''
        );

        /*
         * Product status.
         */
        const status = document.getElementById('bpmx-status');

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
            .then(function(response) {
                return response.json();
            })
            .then(function(response) {

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
                bpmxInitializeProductSelection();

                /*
                 * Render pagination.
                 */
                bpmxRenderPagination(
                    page,
                    response.data.max_num_pages
                );
            })
            .catch(function(error) {
                console.error(error);
            });
    }

    /*
     * Initialize product selection.
     */
    function bpmxInitializeProductSelection() {

        const selectAll = document.getElementById(
            'bpmx-select-all-products'
        );

        if (!selectAll) {
            return;
        }

        const checkboxes = document.querySelectorAll(
            '.bpmx-product-checkbox'
        );

        /*
         * Restore previously selected products.
         */
        checkboxes.forEach(function(checkbox) {

            checkbox.checked = bpmxSelectedProducts.has(
                checkbox.value
            );
        });

        /*
         * Update Select All state for
         * the currently visible products.
         */
        const selectedVisibleProducts = document.querySelectorAll(
            '.bpmx-product-checkbox:checked'
        ).length;

        selectAll.checked = (
            checkboxes.length > 0 &&
            selectedVisibleProducts === checkboxes.length
        );

        /*
         * Select / deselect all products
         * on the current page.
         */
        selectAll.addEventListener('change', function() {

            checkboxes.forEach(function(checkbox) {

                checkbox.checked = selectAll.checked;

                if (selectAll.checked) {
                    bpmxSelectedProducts.add(checkbox.value);
                } else {
                    bpmxSelectedProducts.delete(checkbox.value);
                }
            });
        });

        /*
         * Update the persistent selection
         * when an individual checkbox changes.
         */
        checkboxes.forEach(function(checkbox) {

            checkbox.addEventListener('change', function() {

                if (checkbox.checked) {
                    bpmxSelectedProducts.add(checkbox.value);
                } else {
                    bpmxSelectedProducts.delete(checkbox.value);
                }

                /*
                 * Update Select All for the
                 * currently visible products.
                 */
                const checked = document.querySelectorAll(
                    '.bpmx-product-checkbox:checked'
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
    function bpmxRenderPagination(currentPage, maxPages) {

        const pagination = document.getElementById(
            'bpmx-product-pagination'
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
        function createPageButton(page, isCurrent = false) {

            const button = document.createElement('button');

            button.type = 'button';
            button.className = 'button';
            button.textContent = page;

            if (isCurrent) {
                button.classList.add('button-primary');
                button.disabled = true;
            }

            button.addEventListener('click', function() {
                bpmxLoadProductTable(page);
            });

            return button;
        }

        /*
         * Create an ellipsis.
         */
        function createEllipsis() {

            const span = document.createElement('span');

            span.textContent = '...';
            span.style.margin = '0 4px';

            return span;
        }

        /*
         * Previous button.
         */
        if (currentPage > 1) {

            const previousButton = document.createElement('button');

            previousButton.type = 'button';
            previousButton.className = 'button';
            previousButton.textContent = 'Previous';

            previousButton.addEventListener('click', function() {
                bpmxLoadProductTable(currentPage - 1);
            });

            fragment.appendChild(previousButton);
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
            fragment.appendChild(createEllipsis());
        }

        /*
         * Middle page buttons.
         */
        for (let page = startPage; page <= endPage; page++) {

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
            fragment.appendChild(createEllipsis());
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

            const nextButton = document.createElement('button');

            nextButton.type = 'button';
            nextButton.className = 'button';
            nextButton.textContent = 'Next';

            nextButton.addEventListener('click', function() {
                bpmxLoadProductTable(currentPage + 1);
            });

            fragment.appendChild(nextButton);
        }

        pagination.appendChild(fragment);
    }

    /*
     * Search input.
     *
     * Wait briefly after the user stops typing
     * before sending the AJAX request.
     */
    const searchInput = document.getElementById('bpmx-search');

    let searchTimeout;

    if (searchInput) {

        searchInput.addEventListener('input', function() {

            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(function() {
                bpmxLoadProductTable(1);
            }, 300);
        });
    }

    /*
     * Category filter.
     */
    const categoryFilter = document.getElementById('bpmx-category');

    if (categoryFilter) {

        categoryFilter.addEventListener('change', function() {
            bpmxLoadProductTable(1);
        });
    }

    /*
     * Product type filter.
     */
    const typeFilter = document.getElementById('bpmx-type');

    if (typeFilter) {

        typeFilter.addEventListener('change', function() {
            bpmxLoadProductTable(1);
        });
    }

    /*
     * Stock status filter.
     */
    const stockStatusFilter = document.getElementById(
        'bpmx-stock-status'
    );

    if (stockStatusFilter) {

        stockStatusFilter.addEventListener('change', function() {
            bpmxLoadProductTable(1);
        });
    }

    /*
     * Product status filter.
     */
    const statusFilter = document.getElementById('bpmx-status');

    if (statusFilter) {

        statusFilter.addEventListener('change', function() {
            bpmxLoadProductTable(1);
        });
    }

    /*
     * Products per page.
     */
    const productsPerPage = document.getElementById(
        'bpmx-products-per-page'
    );

    if (productsPerPage) {

        productsPerPage.addEventListener('change', function() {
            bpmxLoadProductTable(1);
        });
    }

    /*
     * Apply Changes.
     */
    const applyButton = document.getElementById(
        'bpmx-apply-changes'
    );

    if (applyButton) {

        applyButton.addEventListener('click', function() {

            const formData = new URLSearchParams();

            /*
             * Trigger the Apply Changes request.
             */
            formData.append(
                'bpmx_action',
                'apply_changes'
            );

            formData.append(
                'bpmx_nonce',
                bpmx_ajax.apply_nonce
            );

            /*
             * Send every selected product ID,
             * including products selected on
             * previous AJAX pages.
             */
            bpmxSelectedProducts.forEach(function(productId) {

                formData.append(
                    'products[]',
                    productId
                );
            });

            /*
             * Collect pricing rule.
             */
            const priceMethod = document.getElementById(
                'bpmx-price-method'
            );

            const priceTarget = document.getElementById(
                'bpmx-price-target'
            );

            const priceValue = document.getElementById(
                'bpmx-price-value'
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
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            })
                .then(function(response) {
                    return response.text();
                })
                .then(function() {

                    /*
                     * Clear all selected products
                     * after Apply Changes.
                     */
                    bpmxSelectedProducts.clear();

                    /*
                     * Reload the current table page.
                     */
                    bpmxLoadProductTable(bpmxCurrentPage);
                })
                .catch(function(error) {
                    console.error(error);
                });
        });
    }

    /*
     * Load the initial product table.
     */
    bpmxLoadProductTable(1);
});