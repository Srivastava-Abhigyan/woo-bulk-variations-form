jQuery(document).ready(function($) {
    function updatePrice($row) {
        let product_id = $('.wc-bulk-variations-form').data('product_id');
        let attributes = {};
        $row.find('select.bulk-attribute').each(function() {
            const attrName = $(this).attr('name').replace('attribute_', '').replace('[]', '');
            attributes[attrName] = $(this).val();
        });

        // If any attribute is empty, set price to 0
        for (let key in attributes) {
            if (!attributes[key]) {
                $row.find('.bulk-price').html(bulk_vars.price_zero_html || '0');
                return;
            }
        }

        $.ajax({
            url: bulk_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'get_variation_price',
                nonce: bulk_vars.nonce,
                product_id: product_id,
                attributes: attributes
            },
            success: function(response) {
                if (response.success) {
                    $row.find('.bulk-price').html(response.data.price_html);
                    updateSummary();
                    saveFormState();
                } 
                else {
                    $row.find('.bulk-price').html(bulk_vars.price_zero_html || '0');
                }
            },
            error: function() {
                $row.find('.bulk-price').html(bulk_vars.price_zero_html || '0');
            }
        });
    }

    function updateSummary() {
        let totalItems = 0;
        let grandTotal = 0;

        $('.bulk-variations-table tbody tr').each(function() {
            const $row = $(this);
            // console.log($row);
            const quantity = parseInt($row.find('.bulk-qty').val()) || 0;
            // console.log(quantity);
            const priceText = $row.find('.bulk-price').text().replace(/[^0-9\.,]/g, '').replace(',', '.');
            // console.log(priceText);
            const price = parseFloat(priceText) || 0;
            // console.log(price);

            totalItems += quantity;
            grandTotal += price * quantity;

            //console.log(totalItems);
            // console.log(grandTotal);
        });

        $('.bulk-total-items').text(totalItems);
        // Format grand total using wc_price from backend or fallback
        if (typeof bulk_vars.format_price === 'string') {
            // Evaluate the localized format_price function string and call it
            const formatPriceFunc = eval('(' + bulk_vars.format_price + ')');
            $('.bulk-grand-total').html(formatPriceFunc(grandTotal));
        } 
        else {
            $('.bulk-grand-total').text(grandTotal.toFixed(2));
        }
        saveFormState();
    }

    function saveFormState() {
        let product_id = $('.wc-bulk-variations-form').data('product_id');
        // console.log(product_id);
        let formState = [];
        $('.bulk-variations-table tbody tr').each(function() {
            const $row = $(this);
            let attributes = {};
            $row.find('select.bulk-attribute').each(function() {
                const attrName = $(this).attr('name').replace('attribute_', '').replace('[]', '');
                attributes[attrName] = $(this).val();
            });
            const quantity = $row.find('.bulk-qty').val();
            // console.log(quantity);
            formState.push({ attributes: attributes, quantity: quantity });
        });
        localStorage.setItem('bulkVariationsFormState_' + product_id, JSON.stringify(formState));
    }

    function restoreFormState() {
        let product_id = $('.wc-bulk-variations-form').data('product_id');
        // console.log(product_id);
        let formState = localStorage.getItem('bulkVariationsFormState_' + product_id);
        // console.log(formState);
        if (!formState) {
            return;
        }
        
        formState = JSON.parse(formState);
        $('.bulk-variations-table tbody tr').each(function(index) {
            if (!formState[index]) return;
            const $row = $(this);
            // console.log($row);
            const state = formState[index];
            // console.log(state);
            $row.find('select.bulk-attribute').each(function() {
                const attrName = $(this).attr('name').replace('attribute_', '').replace('[]', '');
                // console.log(attrName);
                if (state.attributes[attrName]) {
                    $(this).val(state.attributes[attrName]);
                }
            });
            $row.find('.bulk-qty').val(state.quantity);
            updatePrice($row);
        });
        updateSummary();
    }

    $('.bulk-attribute').on('change', function() {
        const $row = $(this).closest('tr');
        // console.log($row);
        updatePrice($row);
    });

    $('.bulk-qty').on('input', function() {
        updateSummary();
    });

    $('.bulk-add-to-cart').on('click', function(e) {
        e.preventDefault();

        let product_id = $('.wc-bulk-variations-form').data('product_id');
        let rows = [];

        $('.bulk-variations-table tbody tr').each(function() {
            const $row = $(this);
            // console.log($row);
            const quantity = parseInt($row.find('.bulk-qty').val());
            // console.log(quantity);

            if (quantity > 0) {
                let attributes = {};
                $row.find('select.bulk-attribute').each(function() {
                    const attrName = $(this).attr('name').replace('attribute_', '').replace('[]', '');
                    // console.log(attrName);
                    attributes[attrName] = $(this).val();
                });

                rows.push({
                    quantity: quantity,
                    attributes: attributes
                });
            }
        });

        if (rows.length === 0) {
            alert('Please select at least one variation.');
            return;
        }

        // console.log('Submitting rows:', rows);

        $.ajax({
            url: bulk_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'bulk_add_to_cart',
                nonce: bulk_vars.nonce,
                product_id: product_id,
                rows: rows
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.cart_url;
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function(err) {
                console.error('AJAX Error:', err);
            }
        });
    });

    restoreFormState();
});
