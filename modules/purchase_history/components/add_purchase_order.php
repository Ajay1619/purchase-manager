<?php require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'add') {
?>
        <section id="add-purchase-order">
            <form class="form-container" id="purchase-order-form" method="POST">
                <h2>Purchase Order Form</h2>
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
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" readonly>
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
                        <tbody id="cart-body">
                            <tr>
                                <td>1</td>
                                <td>
                                    <div class="autocomplete">
                                        <input type="text" id="purchase_order_item_name_1" name="purchase_order_item_name[]" class="autocomplete-input po-item-name" placeholder="Enter Item Name..." oninput="searchProductNameOnInput(event,1)" onclick="checkVendor()" readonly>
                                        <ul class="autocomplete-results" id="results-1"></ul>
                                        <input type="hidden" id="item_id_1" name="item_id[]">
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" id="product_unit_of_measure1" name="product_unit_of_measure[]">
                                    <select id="purchase_order_unit_of_measure_1" name="purchase_order_unit_of_measure[]" required>
                                        <option value="" disabled selected>Select unit of measure</option>
                                        <option value="piece">Piece</option>
                                        <option value="tonne">Tonne</option>
                                        <option value="packets">Packets</option>
                                        <option value="kg">Kilogram</option>
                                        <option value="g">Gram</option>
                                        <option value="lb">Pound</option>
                                        <option value="oz">Ounce</option>
                                        <option value="l">Liter</option>
                                        <option value="ml">Milliliter</option>
                                        <option value="m">Meter</option>
                                        <option value="cm">Centimeter</option>
                                        <option value="mm">Millimeter</option>
                                        <option value="ft">Foot</option>
                                        <option value="in">Inch</option>
                                    </select>
                                </td>

                                <td><input type="number" id="purchase_order_rate_1" name="purchase_order_rate[]" oninput="calculatePurchaseOrder()"></td>
                                <td><input type="number" id="purchase_order_quantity_1" name="purchase_order_quantity[]" placeholder="Enter Quantity" oninput="calculatePurchaseOrder()"></td>
                                <td><input type="number" id="purchase_order_amount_1" name="purchase_order_amount[]" readonly oninput="calculatePurchaseOrder()"></td>
                                <td>
                                    <div class="form-row">
                                        <div class="remove-item">
                                            <button type="button" class="circular-button remove-row">x</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
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
                        <label for="subtotal">Net Total</label>
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
                    </div>
                </div>

                <input type="button" value="Submit" class="submit" onclick="confirmOrder()" />
            </form>
        </section>

        <script>
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
                            console.log(response);
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
                url: '<?= MODULES . '/purchase_history/ajax/fetch_last_purchase_order_number.php' ?>',
                type: 'POST',
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        $('#po-number').val(response.new_purchase_order_number);
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
                    url: '<?= MODULES . '/purchase_history/components/confirm_order_popup.php?type=confirm' ?>',
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
                            $('#invoice-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=confirm
                        });

                        // Close modal on cancel button click
                        $('#cancelOrder').on('click', function() {
                            $('#invoice-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=confirm
                        });

                        // Close modal on outside click
                        $(window).on('click', function(event) {
                            if (event.target == document.getElementById('confirmationPopup')) {
                                $('#invoice-modal').html("");
                                history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=confirm
                            }
                        });
                    },
                    error: function() {
                        $('#response').text('An error occurred');
                    }
                });
            }
        </script>
<?php }
} ?>