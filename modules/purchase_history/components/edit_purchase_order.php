<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'edit') {
        $purchase_order_id = $_POST['po_id'];

?>
        <section id="add-purchase-order">
            <form class="form-container" id="purchase-order-form" method="POST">
                <h2>Purchase Order Form | <span id="title-po-code"></span></h2>
                <div class="form-row">
                    <div class="form-group">
                        <div class="autocomplete">
                            <label for="vendor-name">Vendor Name</label>
                            <input type="text" id="vendor-name" name="vendor_name" class="autocomplete-input" placeholder="Enter Vendor Name..." oninput="searchVendorNameOnInput(event)">

                            <ul class="autocomplete-results" id="results"></ul>
                            <input type="hidden" id="vendor-id" name="vendor_id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="po-number">Purchase Order Number</label>
                        <input type="text" id="po-number" name="po_number" readonly>
                        <input type="hidden" name="po-id" value="<?= $purchase_order_id ?>">
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="text" id="date" name="date" value="<?= date(DATE_FORMAT) ?>" readonly>
                    </div>
                </div>
                <div class="table-container">
                    <label>
                        <h4>Cart</h4>
                    </label>
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Item Name</th>
                                <th>Unit of Measure</th>
                                <th>Rate</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cart-body"></tbody>
                    </table>
                </div>
                <div class="form-row">
                    <div class="add-item" id="add-item">
                        <h5>Add Item</h5>
                        <button type="button" class="circular-button">+</button>
                    </div>
                    <div class="line"></div>
                </div>
                <div class="totals-container">
                    <div class="form-group">
                        <label for="subtotal">Subtotal</label>
                        <input type="number" id="subtotal" name="subtotal" value="0.00" readonly>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="discount-percentage">Discount Percentage</label>
                            <input type="number" id="discount-percentage" name="discount_percentage" value="0.00" oninput="calculatePurchaseOrder()">
                        </div>
                        <div class="form-group">
                            <label for="discount-amount">Discount Amount</label>
                            <input type="number" id="discount-amount" name="discount_amount" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="adjustment-amount">Adjustment Amount</label>
                            <input type="text" id="adjustment-amount" name="adjustment-amount" value="0.00" oninput="calculatePurchaseOrder()">
                        </div>
                        <div class="form-group">
                            <label for="grand-total">Grand Total</label>
                            <input type="number" id="grand-total" name="grand_total" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="amount-in-words">Amount In Words</label>
                            <textarea name="amount-in-words" id="amount-in-words" cols="30" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="purchase-order-status">Status</label>
                            <select id="purchase-order-status" name="purchase-order-status" required>
                                <option value="" disabled>Select Status</option>
                                <option value="0">Pending</option>
                                <option value="1">Purchased</option>
                            </select>
                            <div id="purchase_date">
                                <label for="purchase-order-status">Purchased Date</label>
                                <input type="date" name="purchased-date" id="purchased-date" value="<?= date("Y-m-d") ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <input type="button" value="Submit" class="submit" onclick="confirmOrder()" />
            </form>
        </section>

        <script>
            $(document).ready(function() {
                // Cache the elements
                var $statusSelect = $('#purchase-order-status');
                var $purchaseDateDiv = $('#purchase_date');

                // Function to toggle visibility of the purchase date div
                function togglePurchaseDate() {
                    if ($statusSelect.val() === '1') {
                        $purchaseDateDiv.show();
                    } else {
                        $purchaseDateDiv.hide();
                    }
                }

                // Initial check
                togglePurchaseDate();

                // Add event listener for change event
                $statusSelect.change(togglePurchaseDate);
            });

            function fetchPrePurchaseDetails(item_id, count) {
                const vendor_id = $('#vendor-id').val()
                $.ajax({
                    url: '<?= MODULES . '/purchase_history/json/item_pre_purchase_details.php' ?>',
                    type: 'POST',
                    data: {
                        vendor_id: $('#vendor-id').val(),
                        item_id: $('#item_id_' + count).val()
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            const result = response.data;
                            $('#purchase_order_unit_of_measure_' + count).val(result.unit_of_measure);
                            $('#purchase_order_rate_' + count).val(result.unit_price);
                            $('#purchase_order_quantity_' + count).val(1);
                            calculatePurchaseOrder()
                        }
                    },
                    error: function() {
                        $('#response').text('An error occurred');
                    }
                });
            }

            function calculatePurchaseOrder() {
                $.ajax({
                    url: '<?= MODULES . '/purchase_history/ajax/calculate_purchase_order.php' ?>',
                    type: 'POST',
                    data: $('#purchase-order-form').serialize(),
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') { // Update form fields with the response data
                            $('#subtotal').val(response.subtotal);
                            $('#discount-amount').val(response.discount_amount);
                            $('#grand-total').val(response.grand_total);
                            $('#amount-in-words').val(response.amount_in_words + ' Only'); // Use .text() for plain text

                            response.amounts.forEach(function(amount, index) {
                                $('#purchase_order_amount_' + (index + 1)).val(amount);
                            });


                        } else {
                            $('#response').text('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        $('#response').text('An error occurred');
                    }
                });
            }
        </script>
        <script src="<?= MODULES . '/purchase_history/js/add_purchase_order.js' ?>"></script>

        <script>
            $.ajax({
                url: '<?= MODULES . '/purchase_history/ajax/fetch_view_purchase_order.php' ?>',
                type: 'GET',
                data: {
                    purchase_order_id: <?= $purchase_order_id ?>
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        const purchaseOrder = response.purchase_order_details;
                        const itemDetails = response.item_details;
                        // Populate purchase order details
                        $('#po-number').val(purchaseOrder.purchase_order_number);
                        $('#date').val(purchaseOrder.purchase_order_date);
                        $('#vendor-name').val(purchaseOrder.vendor_company_name);
                        $('#vendor-id').val(purchaseOrder.vendor_id);
                        $('#subtotal').val(purchaseOrder.subtotal);
                        $('#discount-percentage').val(purchaseOrder.discount);
                        $('#discount-amount').val(purchaseOrder.discount_amount);
                        $('#adjustment-amount').val(purchaseOrder.adjustment);
                        $('#grand-total').val(purchaseOrder.grand_total);
                        $('#amount-in-words').val(purchaseOrder.amount_in_words);
                        $('#title-po-code').text(purchaseOrder.purchase_order_number);
                        $('#purchase-order-status').val(purchaseOrder.purchase_order_status);

                        // Clear the existing rows in the cart
                        $('#cart-body').empty();

                        // Populate the item details in the table
                        itemDetails.forEach((item, index) => {
                            const statusBadge = item.purchase_order_item_status === 1 ?
                                '<span class="badge-circular purchase-order-status badge-success ">✔</span>' :
                                item.purchase_order_item_status === 2 ?
                                '<span class="badge-circular purchase-order-status badge-danger">✘</span>' :
                                '';

                            const rowHtml = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <div class="autocomplete">
                                            <input type="text" id="purchase_order_item_name_${index + 1}" name="purchase_order_item_name[]" class="autocomplete-input po-item-name" placeholder="Enter Item Name..." value="${item.product_name}" oninput="searchProductNameOnInput(event,${index + 1})" onclick="checkVendor()" readonly>
                                            <ul class="autocomplete-results" id="results-${index + 1}"></ul>
                                            <input type="hidden" id="purchase_order_item_id_${index + 1}" name="purchase_order_item_id[]" value="${item.purchase_order_item_id}">
                                            <input type="hidden" id="item_id_${index + 1}" name="item_id[]" value="${item.product_id}">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" id="product_unit_of_measure${index + 1}" name="product_unit_of_measure[]" value="${item.product_unit_of_measure}">
                                        <select id="purchase_order_unit_of_measure_${index + 1}" name="purchase_order_unit_of_measure[]" required>
                                            <option value="" disabled>Select unit of measure</option>
                                            <option value="piece" ${item.unit_of_measure === 'piece' ? 'selected' : ''}>Piece</option>
                                            <option value="tonne" ${item.unit_of_measure === 'tonne' ? 'selected' : ''}>Tonne</option>
                                            <option value="packets" ${item.unit_of_measure === 'packets' ? 'selected' : ''}>Packets</option>
                                            <option value="kg" ${item.unit_of_measure === 'kg' ? 'selected' : ''}>Kilogram</option>
                                            <option value="g" ${item.unit_of_measure === 'g' ? 'selected' : ''}>Gram</option>
                                            <option value="lb" ${item.unit_of_measure === 'lb' ? 'selected' : ''}>Pound</option>
                                            <option value="oz" ${item.unit_of_measure === 'oz' ? 'selected' : ''}>Ounce</option>
                                            <option value="l" ${item.unit_of_measure === 'l' ? 'selected' : ''}>Liter</option>
                                            <option value="ml" ${item.unit_of_measure === 'ml' ? 'selected' : ''}>Milliliter</option>
                                            <option value="m" ${item.unit_of_measure === 'm' ? 'selected' : ''}>Meter</option>
                                            <option value="cm" ${item.unit_of_measure === 'cm' ? 'selected' : ''}>Centimeter</option>
                                            <option value="mm" ${item.unit_of_measure === 'mm' ? 'selected' : ''}>Millimeter</option>
                                            <option value="ft" ${item.unit_of_measure === 'ft' ? 'selected' : ''}>Foot</option>
                                            <option value="in" ${item.unit_of_measure === 'in' ? 'selected' : ''}>Inch</option>
                                        </select>
                                    </td>
                                    <td><input type="number" id="purchase_order_rate_${index + 1}" name="purchase_order_rate[]" value="${item.unit_price}" oninput="calculatePurchaseOrder()"></td>
                                    <td><input type="number" id="purchase_order_quantity_${index + 1}" name="purchase_order_quantity[]" value="${item.quantity}" placeholder="Enter Quantity" oninput="calculatePurchaseOrder()"></td>
                                    <td><input type="number" id="purchase_order_amount_${index + 1}" name="purchase_order_amount[]" value="${item.amount}" readonly oninput="calculatePurchaseOrder()"></td>
                                    <td>
                                        <div class="form-row">
                                            <div class="remove-item">
                                                <button type="button" class="circular-button remove-row" style="display: ${!item.purchase_order_item_status ? 'block' : 'none'};">✘</button>
                                                <button type="button" class="circular-button accept-item" value="${index + 1}" onclick="acceptItem(this.value)" style="display: ${!item.purchase_order_item_status ? 'block' : 'none'};">✔</button>
                                               
                                            </div> 
                                        </div>
                                        ${statusBadge}
                                    </td>
                                </tr>
                            `;
                            $('#cart-body').append(rowHtml);
                        });
                    }
                },
                error: function() {
                    $('#response').text('An error occurred');
                }
            });

            function searchVendorNameOnInput(data) {
                let inputValue = data.target.value;
                if (inputValue.length > 0) {
                    $.ajax({
                        url: '<?= MODULES . '/purchase_history/json/vendors_list.php' ?>',
                        type: 'POST',
                        data: {
                            search_input: inputValue
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.status === 'success') {
                                searchVendorName(response.data);
                            }
                        },
                        error: function() {
                            $('#response').text('An error occurred');
                        }
                    });
                };
            }

            function searchProductNameOnInput(data, count) {
                let inputValue = data.target.value;
                if (inputValue.length > 0) {
                    $.ajax({
                        url: '<?= MODULES . '/invoice/json/items_list.php' ?>',
                        type: 'POST',
                        data: {
                            search_input: inputValue
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.status === 'success') {
                                searchProductName(response.data, count);

                            } else {
                                console.log('Error fetching products');
                            }
                        },
                        error: function() {
                            $('#response').text('An error occurred');
                        }
                    });
                }
            }

            function confirmOrder() {
                $.ajax({
                    url: '<?= MODULES . '/purchase_history/components/confirm_update_order_popup.php?type=confirm' ?>',
                    type: 'POST',
                    data: $('#purchase-order-form').serialize(),
                    success: function(response) {
                        $('#purchase-order-modal').html(response);
                        $('#confirmationPopup').css('display', 'block');
                        // Set URL query parameter ?type=confirm
                        var newUrl = window.location.pathname + '?type=confirm';
                        history.pushState({}, '', newUrl);

                        // Close modal on close button click
                        $('.close').on('click', function() {
                            $('#product-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=confirm
                        });

                        // Close modal on cancel button click
                        $('#cancelOrder').on('click', function() {
                            $('#product-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=confirm
                        });

                        // Close modal on outside click
                        $(window).on('click', function(event) {
                            if (event.target == document.getElementById('confirmationPopup')) {
                                $('#product-modal').html("");
                                history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=confirm
                            }
                        });
                    },
                    error: function() {
                        $('#response').text('An error occurred');
                    }
                });
            }

            function acceptItem(value) {
                $.ajax({
                    url: '<?= MODULES . '/purchase_history/ajax/accept_item.php' ?>',
                    type: 'POST',
                    data: {
                        purchase_order_item_id: $('#purchase_order_item_id_' + value).val()
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            showToast('success', response.messages);
                        } else {
                            showToast('error', response.messages);
                        }
                    },
                    error: function() {
                        $('#response').text('An error occurred');
                    }
                });
            }

            function fetchPrePurchaseDetails(item_id, count) {
                const vendor_id = $('#vendor-id').val()
                $.ajax({
                    url: '<?= MODULES . '/purchase_history/json/item_pre_purchase_details.php' ?>',
                    type: 'POST',
                    data: {
                        vendor_id: $('#vendor-id').val(),
                        item_id: $('#item_id_' + count).val()
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            const result = response.data;
                            $('#purchase_order_unit_of_measure_' + count).val(result.unit_of_measure);
                            $('#purchase_order_rate_' + count).val(result.unit_price);
                            $('#purchase_order_quantity_' + count).val(1);
                            calculatePurchaseOrder()
                        }
                    },
                    error: function() {
                        $('#response').text('An error occurred');
                    }
                });
            }
        </script>
<?php }
} ?>