<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'edit') {
        $qo_id = $_POST['qo_id'];

?>
        <section id="add-quotation-order">
            <form class="form-container" id="quotation-form" method="POST">
                <h2>Quotation Form | <span id="title-qo-code"></span></h2>
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
                        <input type="hidden" name="qo-id" value="<?= $qo_id ?>">
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
                        <div class="form-group">
                            <label for="quotation-status">Status</label>
                            <select id="quotation-status" name="quotation-status" required>
                                <option value="" disabled>Select Status</option>
                                <option value="0">Pending</option>
                                <option value="1">Finalized</option>
                            </select>
                            <div id="invoice_date">
                                <label for="quotation-status">Invoice Date</label>
                                <input type="date" name="invoiced_date" id="invoiced-date" value="<?= date("Y-m-d") ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <input type="button" value="Submit" class="submit" onclick="confirmQuotation()" />
            </form>
        </section>

        <script>
            $(document).ready(function() {
                // Cache the elements
                var $statusSelect = $('#quotation-status');
                var $invoiceDateDiv = $('#invoice_date');

                // Function to toggle visibility of the invoice date div
                function toggleInvoiceDate() {
                    if ($statusSelect.val() === '1') {
                        $invoiceDateDiv.show();
                    } else {
                        $invoiceDateDiv.hide();
                    }
                }

                // Initial check
                toggleInvoiceDate();

                // Add event listener for change event
                $statusSelect.change(toggleInvoiceDate);
            });

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
                        $('#response').text('An error occurred');
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
                        console.log(response)
                        if (response.status === 'success') { // Update form fields with the response data

                            $('#subtotal').val(response.subtotal);
                            $('#discount-amount').val(response.discount_amount);
                            $('#grand-total').val(response.grand_total);
                            $('#amount-in-words').text(response.amount_in_words + ' Only'); // Use .text() for plain text

                            response.amounts.forEach(function(amount, index) {
                                $('#quotation_amount_' + (index + 1)).val(amount);
                            });


                        } else if (response.status === 'warning') {
                            $.ajax({
                                url: '<?= MODULES . '/quotation/components/add_purchase_order.php' ?>',
                                type: 'POST',
                                data: response.data,
                                success: function(modal_response) {

                                    $('#quotation-modal').html(modal_response);
                                    $('#myModal').css('display', 'block');

                                    showToast('warning', response.message);
                                    // Set URL query parameter ?type=edit
                                    var newUrl = window.location.pathname + '?type=edit';
                                    history.pushState({}, '', newUrl);

                                    // Close modal on close button click
                                    $('.close').on('click', function() {
                                        $('#inventory-modal').html("");
                                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=edit
                                        location.reload();
                                    });

                                    // Close modal on outside click
                                    $(window).on('click', function(event) {
                                        if (event.target == document.getElementById('myModal')) {
                                            $('#inventory-modal').html("");
                                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=edit
                                            location.reload();
                                        }
                                    });
                                },
                                error: function() {
                                    $('#modal_response').text('An error occurred');
                                }
                            });
                        }
                    },
                    error: function() {
                        $('#response').text('An error occurred');
                    }
                });
            }
        </script>
        <script src="<?= MODULES . '/quotation/js/add_quotation.js' ?>"></script>

        <script>
            $.ajax({
                url: '<?= MODULES . '/quotation/ajax/fetch_view_quotation.php' ?>',
                type: 'GET',
                data: {
                    quotation_id: <?= $qo_id ?>
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        const quotation = response.quotation_details;
                        const itemDetails = response.item_details;
                        // Populate Quotation details
                        $('#qo-number').val(quotation.quotation_number);
                        $('#date').val(quotation.quotation_date);
                        $('#customer-name').val(quotation.customer_name);
                        $('#customer-id').val(quotation.customer_id);
                        $('#subtotal').val(quotation.subtotal);
                        $('#discount-percentage').val(quotation.discount);
                        $('#discount-amount').val(quotation.discount_amount);
                        $('#adjustment-amount').val(quotation.adjustment);
                        $('#grand-total').val(quotation.grand_total);
                        $('#amount-in-words').val(quotation.amount_in_words);
                        $('#title-qo-code').text(quotation.quotation_number);
                        $('#quotation-status').val(quotation.quotation_status);

                        // Clear the existing rows in the cart
                        $('#cart-body').empty();

                        // Populate the item details in the table
                        itemDetails.forEach((item, index) => {
                            const statusBadge = item.quotation_item_status === 1 ?
                                '<span class="badge-circular quotation-status badge-success ">✔</span>' :
                                item.quotation_item_status === 2 ?
                                '<span class="badge-circular quotation-status badge-danger">✘</span>' :
                                '';

                            const rowHtml = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <div class="autocomplete">
                                            <input type="text" id="quotation_item_name_${index + 1}" name="quotation_item_name[]" class="autocomplete-input qo-item-name" placeholder="Enter Item Name..." value="${item.product_name}" oninput="searchProductNameOnInput(event,${index + 1})" onclick="checkCustomer()" readonly>
                                            <ul class="autocomplete-results" id="results-${index + 1}"></ul>
                                            <input type="hidden" id="quotation_item_id_${index + 1}" name="quotation_item_id[]" value="${item.quotation_item_id}">
                                            <input type="hidden" id="item_id_${index + 1}" name="item_id[]" value="${item.product_id}">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" id="product_unit_of_measure${index + 1}" name="product_unit_of_measure[]" value="${item.product_unit_of_measure}">
                                        <input type="hidden" id="inventory_unit_of_measure${index + 1}" name="inventory_unit_of_measure[]" value="${item.inventory_unit_of_measure}">
                                        <input type="hidden" id="product_code${index + 1}" name="product_code[]" value="${item.product_code}">
                                        <input type="hidden" id="quantity_in_stock${index + 1}" name="quantity_in_stock[]" value="${item.quantity_in_stock}">
                                        <select id="quotation_unit_of_measure_${index + 1}" name="quotation_unit_of_measure[]" required>
                                            <option value="" disabled>Select unit of measure</option>
                                            <option value="piece" ${item.item_unit_of_measure === 'piece' ? 'selected' : ''}>Piece</option>
                                            <option value="tonne" ${item.item_unit_of_measure === 'tonne' ? 'selected' : ''}>Tonne</option>
                                            <option value="packets" ${item.item_unit_of_measure === 'packets' ? 'selected' : ''}>Packets</option>
                                            <option value="kg" ${item.item_unit_of_measure === 'kg' ? 'selected' : ''}>Kilogram</option>
                                            <option value="g" ${item.item_unit_of_measure === 'g' ? 'selected' : ''}>Gram</option>
                                            <option value="lb" ${item.item_unit_of_measure === 'lb' ? 'selected' : ''}>Pound</option>
                                            <option value="oz" ${item.item_unit_of_measure === 'oz' ? 'selected' : ''}>Ounce</option>
                                            <option value="l" ${item.item_unit_of_measure === 'l' ? 'selected' : ''}>Liter</option>
                                            <option value="ml" ${item.item_unit_of_measure === 'ml' ? 'selected' : ''}>Milliliter</option>
                                            <option value="m" ${item.item_unit_of_measure === 'm' ? 'selected' : ''}>Meter</option>
                                            <option value="cm" ${item.item_unit_of_measure === 'cm' ? 'selected' : ''}>Centimeter</option>
                                            <option value="mm" ${item.item_unit_of_measure === 'mm' ? 'selected' : ''}>Millimeter</option>
                                            <option value="ft" ${item.item_unit_of_measure === 'ft' ? 'selected' : ''}>Foot</option>
                                            <option value="in" ${item.item_unit_of_measure === 'in' ? 'selected' : ''}>Inch</option>
                                        </select>
                                    </td>
                                    <td><input type="number" id="quotation_rate_${index + 1}" name="quotation_rate[]" value="${item.unit_price}" oninput="calculateQuotation()"></td>
                                    <td><input type="number" id="quotation_quantity_${index + 1}" name="quotation_quantity[]" value="${item.quantity}" placeholder="Enter Quantity" oninput="calculateQuotation()"></td>
                                    <td><input type="number" id="quotation_amount_${index + 1}" name="quotation_amount[]" value="${item.amount}" readonly oninput="calculateQuotation()"></td>
                                    <td>
                                        <div class="form-row">
                                            <div class="remove-item">
                                                <button type="button" class="circular-button remove-row" style="display: ${!item.quotation_item_status ? 'block' : 'none'};">✘</button>
                                                <button type="button" class="circular-button accept-item" value="${index + 1}" onclick="acceptItem(this.value)" style="display: ${!item.quotation_item_status ? 'block' : 'none'};">✔</button>
                                               
                                            </div> 
                                        </div>
                                        ${statusBadge}
                                    </td>
                                </tr>
                            `;
                            $('#cart-body').append(rowHtml);
                        });
                        calculateQuotation();
                    }
                },
                error: function() {
                    $('#response').text('An error occurred');
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
                            $('#response').text('An error occurred');
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
                            $('#response').text('An error occurred');
                        }
                    });
                };
            }

            function confirmQuotation() {
                $.ajax({
                    url: '<?= MODULES . '/quotation/components/confirm_update_quotation_popup.php?type=confirm' ?>',
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
                        $('#response').text('An error occurred');
                    }
                });
            }

            function acceptItem(value) {
                $.ajax({
                    url: '<?= MODULES . '/purchase_history/ajax/accept_item.php' ?>',
                    type: 'POST',
                    data: {
                        quotation_item_id: $('#quotation_item_id_' + value).val()
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
        </script>
<?php }
} ?>