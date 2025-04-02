<?php require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'add') {
?>
        <section id="add-quotation">
            <form class="form-container" id="quotation-form" method="POST">
                <h2>Quotation Form</h2>
                <div class="form-row">
                    <div class="form-group">
                        <div class="autocomplete">
                            <label for="customer-name">Customer Name</label>
                            <input type="text" id="customer-name" name="customer_name" class="autocomplete-input" placeholder="Enter Customer Name..." oninput="searchCustomerNameOnInput(event)">
                            <ul class="autocomplete-results" id="results"></ul>
                            <input type="hidden" id="customer-id" name="customer_id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="qo-number">Quotation Number</label>
                        <input type="text" id="qo-number" name="qo_number" readonly>
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
                                        <input type="text" id="quotation_item_name_1" name="quotation_item_name[]" class="autocomplete-input qo-item-name" placeholder="Enter Item Name..." oninput="searchProductNameOnInput(event,1)" onclick="checkCustomer()" readonly>
                                        <ul class="autocomplete-results" id="results-1"></ul>
                                        <input type="hidden" id="item_id_1" name="item_id[]">
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" id="product_unit_of_measure1" name="product_unit_of_measure[]">
                                    <select id="quotation_unit_of_measure_1" name="quotation_unit_of_measure[]" required>
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

                                <td><input type="number" id="quotation_rate_1" name="quotation_rate[]" oninput="calculateQuotation()"></td>
                                <td><input type="number" id="quotation_quantity_1" name="quotation_quantity[]" placeholder="Enter Quantity" oninput="calculateQuotation()"></td>
                                <td><input type="number" id="quotation_amount_1" name="quotation_amount[]" readonly oninput="calculateQuotation()"></td>
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
                            <input type="number" id="discount-percentage" name="discount_percentage" value="0.00" oninput="calculateQuotation()">
                        </div>
                        <div class="form-group">
                            <label for="discount-amount">Discount Amount</label>
                            <input type="number" id="discount-amount" name="discount_amount" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="adjustment-amount">Adjustment Amount</label>
                            <input type="text" id="adjustment-amount" name="adjustment-amount" value="0.00" oninput="calculateQuotation()">
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

                <input type="button" value="Submit" class="submit" onclick="confirmQuotation()" />
            </form>
        </section>

        <script>
            function fetchPreSalesDetails(item_id, count) {
                $.ajax({
                    url: '<?= MODULES . '/quotation/json/item_pre_sales_details.php' ?>',
                    type: 'POST',
                    data: {
                        item_id: $('#item_id_' + count).val()
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            const result = response.data;
                            $('#quotation_unit_of_measure_' + count).val(result.unit_of_measure);
                            $('#quotation_rate_' + count).val(result.unit_price);
                            $('#quotation_quantity_' + count).val(1);
                            $('#quotation_amount_' + count).val(result.amount);
                            calculateQuotation()
                        }
                    },
                    error: function() {
                        showToast('error', error);
                    }
                });
            }

            function calculateQuotation() {
                $.ajax({
                    url: '<?= MODULES . '/quotation/ajax/calculate_quotation.php' ?>',
                    type: 'POST',
                    data: $('#quotation-form').serialize(),
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') { // Update form fields with the response data
                            $('#subtotal').val(response.subtotal);
                            $('#discount-amount').val(response.discount_amount);
                            $('#grand-total').val(response.grand_total);
                            $('#amount-in-words').text(response.amount_in_words + ' Only'); // Use .text() for plain text

                            response.amounts.forEach(function(amount, index) {
                                $('#quotation_amount_' + (index + 1)).val(amount);
                            });


                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error: function() {
                        showToast('error', error);
                    }
                });
            }
        </script>
        <script src="<?= MODULES . '/quotation/js/add_quotation.js' ?>"></script>

        <script>
            $.ajax({
                url: '<?= MODULES . '/quotation/ajax/fetch_last_quotation_number.php' ?>',
                type: 'POST',
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        $('#qo-number').val(response.new_quotation_number);
                    }
                },
                error: function() {
                    showToast('error', error);
                }
            });

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
                            }
                        },
                        error: function() {
                            showToast('error', error);
                        }
                    });
                };
            }

            function searchCustomerNameOnInput(data) {
                let inputValue = data.target.value;
                if (inputValue.length > 0) {
                    $.ajax({
                        url: '<?= MODULES . '/invoice/json/customers_list.php' ?>',
                        type: 'POST',
                        data: {
                            search_input: inputValue
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.status === 'success') {
                                searchCustomerName(response.data);
                            }
                        },
                        error: function() {
                            showToast('error', error);
                        }
                    });
                };
            }

            function confirmQuotation() {
                $.ajax({
                    url: '<?= MODULES . '/quotation/components/confirm_quotation_popup.php?type=confirm' ?>',
                    type: 'POST',
                    data: $('#quotation-form').serialize(),
                    success: function(response) {
                        $('#quotation-modal').html(response);
                        $('#confirmationPopup').css('display', 'block');
                        // Set URL query parameter ?type=confirm
                        var newUrl = window.location.pathname + '?type=confirm';
                        history.pushState({}, '', newUrl);

                        // Close modal on close button click
                        $('.close').on('click', function() {
                            $('#quotation-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=confirm
                        });

                        // Close modal on cancel button click
                        $('#cancelOrder').on('click', function() {
                            $('#quotation-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=confirm
                        });

                        // Close modal on outside click
                        $(window).on('click', function(event) {
                            if (event.target == document.getElementById('confirmationPopup')) {
                                $('#quotation-modal').html("");
                                history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=confirm
                            }
                        });
                    },
                    error: function() {
                        showToast('error', error);
                    }
                });
            }
        </script>
<?php }
} ?>