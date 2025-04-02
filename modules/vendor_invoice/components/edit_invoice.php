<?php require_once('../../../config/sparrow.php'); ?>
<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'edit') {
        $invoice_id = isset($_POST['invoice_id']) ? sanitizeInput($_POST['invoice_id'], 'int') : '';
?>

        <section id="add-invoice">
            <form class="form-container" id="invoice-form">
                <h2>Invoice Form</h2>
                <div class="form-row">
                    <div class="form-group">
                        <div class="autocomplete">
                            <label for="customer-name">Customer Name</label>
                            <input type="text" id="customer-name" name="customer_name" class="autocomplete-input" placeholder="Enter customer Name..." oninput="searchCustomerNameOnInput(event)">
                            <ul class="autocomplete-results" id="results"></ul>
                            <input type="hidden" id="invoice-id" name="invoice_id" value="<?= $invoice_id ?>">
                            <input type="hidden" id="customer-id" name="customer_id">
                            <input type="hidden" id="customer-state" name="customer_state">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="invoice-number">Invoice Number</label>
                        <input type="text" id="invoice-number" name="invoice_number" readonly>
                    </div>
                    <div class="form-group">
                        <label for="invoice_date">Invoice Date</label>
                        <input type="date" id="invoice_date" name="invoice_date" value="<?= date("Y-m-d") ?>">
                    </div>
                    <div class="form-group">
                        <label for="due-date">Due Date</label>
                        <input type="date" id="due-date" name="due-date" value="<?= date("Y-m-d") ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group gst-checkbox">
                        <label for="gst-enable">
                            <input type="checkbox" id="gst-enable" name="gst-enable" value="1" checked onchange="calculateInvoice()">

                            <span class="checkboxes"></span> GST
                        </label>
                    </div>
                </div>
                <input type="hidden" name="total_item_count" id="total-item-count" value="1">
                <div class="table-container">
                    <label>
                        <h2>Cart</h2>
                    </label>
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Item Name</th>
                                <th>Unit of Measure</th>
                                <th>Quantity</th>
                                <th>Rate</th>
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
                    <div class="net-total">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="subtotal">Net-Total</label>
                                <input type="text" id="nettotal" name="nettotal" placeholder="Net Total Amount" oninput="calculateInvoice()" readonly>
                            </div>

                        </div>
                    </div>

                    <div class="form-row" id="gst-details">
                        <div class="form-group">
                            <label for="sgst">S GST</label>
                            <input type="text" id="sgst" name="sgst" placeholder="S GST Amount" oninput="calculateInvoice()" readonly>
                        </div>
                        <div class="form-group">
                            <label for="cgst">C GST</label>
                            <input type="text" id="cgst" name="cgst" placeholder="C GST Amount" oninput="calculateInvoice()" readonly>
                        </div>
                        <div class="form-group">
                            <label for="igst">I GST</label>
                            <input type="text" id="igst" name="igst" placeholder="I GST Amount" oninput="calculateInvoice()" readonly>
                        </div>
                    </div>
                    <label for="summary">Summary</label>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="total-value">Total Amount</label>
                            <input type="text" id="total-value" name="total_value" oninput="calculateInvoice()" placeholder="Total Value Amount" readonly>
                            <input type="hidden" id="total-gst-amount" name="total-gst-amount">
                        </div>
                        <div class="form-group">
                            <label for="adjustment">Adjustment</label>
                            <input type="text" id="adjustment" name="adjustment" oninput="calculateInvoice()" value="0.00">
                        </div>
                        <div class="form-group">
                            <label for="grand-total">Grand Total</label>
                            <input type="text" id="grand-total" name="grand_total" oninput="calculateInvoice()" placeholder="Grand Total Amount" readonly>
                        </div>

                    </div>
                    <label for="payment">Transaction</label>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="amount-inwords">Amount In Words</label>

                            <textarea name="amount-inwords" id="amount-inwords" cols="30" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="payment-mode">Payment mode</label>
                            <select id="payment-mode" name="payment_mode">
                                <option value="" disabled selected>Select Payment Mode</option>
                                <option value="cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Debit/Credit Card">Debit/Credit Card</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="invoice-status">Status</label>
                            <select id="invoice-status" name="invoice_status" required>
                                <option value="" disabled>Select Status</option>
                                <option value="0" selected>Pending</option>
                                <option value="1">Paid</option>
                            </select>
                            <div id="delivery_date">
                                <label for="deliveried-date">Delivery Date</label>
                                <input type="date" name="delivery_date" id="deliveried-date" value="<?= date("Y-m-d") ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">

                    </div>
                    <input type="button" value="Submit" class="submit" onclick="confirmInvoice()" />
            </form>
        </section>

        <script src="<?= MODULES . '/vendor_invoice/js/add_invoice.js' ?>"></script>

        <script>
            var $statusSelect = $('#invoice-status');
            var $deliveryDateDiv = $('#delivery_date');

            // Function to toggle visibility of the purchase date div
            function toggleDeliveryDate() {
                if ($statusSelect.val() === '1') {
                    $deliveryDateDiv.show();
                } else {
                    $deliveryDateDiv.hide();
                }
            }

            // Initial check
            toggleDeliveryDate();

            // Add event listener for change event
            $statusSelect.change(toggleDeliveryDate);

            $.ajax({
                url: '<?= MODULES . '/vendor_invoice/json/fetch_view_invoice.php' ?>',
                type: 'POST',
                data: {
                    'invoice_id': <?= $_POST['invoice_id'] ?>,
                    'fetch_type': 'edit'
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    var invoiceDetails = data.invoice_details;
                    var itemDetails = data.item_details;
                    // Populate Invoice Details
                    $('#customer-name').val(invoiceDetails.customer_name);
                    $('#customer-id').val(invoiceDetails.customer_id);
                    $('#customer-state').val(invoiceDetails.address_state);
                    $('#invoice-number').val(invoiceDetails.invoice_number);
                    $('#invoice_date').val(invoiceDetails.edit_invoice_date);
                    $('#due-date').val(invoiceDetails.edit_invoice_due_date);
                    $('#status').val(invoiceDetails.invoice_status == 0 ? 'Unpaid' : 'Paid');
                    if (invoiceDetails.invoice_status == 0) {
                        $('#status').addClass('badge-alert');
                    } else {
                        $('#status').addClass('badge-success');
                    }
                    if (invoiceDetails.gst == 0) {
                        $('#gst-enable').prop('checked', false);
                    } else {
                        $('#gst-enable').prop('checked', true);
                    }
                    checkGstEnable(invoiceDetails.gst);
                    $('#nettotal').val(invoiceDetails.subtotal);
                    $('#total-value').val(invoiceDetails.subtotal);
                    $('#discount-percentage').val(invoiceDetails.discount_percentage + ' %');
                    $('#gst-percentage').val(invoiceDetails.sgst ? '18%' : '0%'); // Adjust this based on your tax structure
                    $('#discount-amount').val(invoiceDetails.discount_amount);

                    $('#sgst').val(invoiceDetails.sgst);
                    $('#cgst').val(invoiceDetails.cgst);
                    $('#igst').val(invoiceDetails.igst);
                    $('#adjustment').val(invoiceDetails.adjustments);
                    $('#grand-total').val(invoiceDetails.grand_total);
                    $('#payment-mode').val(invoiceDetails.payment_mode);
                    $('#amount-inwords').val(invoiceDetails.amount_in_words);

                    // Populate Items Ordered
                    var itemsHtml = '';
                    itemDetails.forEach(function(item, index) {
                        var rowIndex = index + 1;
                        $('#total-item-count').val(rowIndex);

                        itemsHtml += `<tr>
                            <td>${rowIndex}</td>
                            <td>
                                <div class="autocomplete">
                                    <input type="text" id="item-name-${rowIndex}" class="autocomplete-input in-item-name" placeholder="Enter Item Name..." value="${item.product_name}" onclick="checkCustomer()" oninput="searchProductNameOnInput(event,${rowIndex})" readonly>
                                    <ul class="autocomplete-results" id="results-${rowIndex}"></ul>
                                    <input type="hidden" name="invoice-item-id[]" id="invoice-item-id-${rowIndex}" value="${item.invoice_item_id}">
                                    <input type="hidden" name="item-id[]" id="item-id-${rowIndex}" value="${item.product_id}">
                                    <input type="hidden" name="item-gst-amount[]" id="item-gst-amount-${rowIndex}" value="${item.gst_amount}">
                                </div>
                            </td>
                            <td>
                                <input type="hidden" id="product_unit_of_measure${rowIndex}" name="product_unit_of_measure[]" value="${item.product_unit_of_measure}">
                                <select id="invoice_unit_of_measure-${rowIndex}" name="invoice_unit_of_measure[]" required onchange="calculateInvoice()">
                                    <option value="" disabled>Select unit of measure</option>
                                    <option value="piece" ${item.unit_of_measure == 'piece' ? 'selected' : ''}>Piece</option>
                                    <option value="tonne" ${item.unit_of_measure == 'tonne' ? 'selected' : ''}>Tonne</option>
                                    <option value="packets" ${item.unit_of_measure == 'packets' ? 'selected' : ''}>Packets</option>
                                    <option value="kg" ${item.unit_of_measure == 'kg' ? 'selected' : ''}>Kilogram</option>
                                    <option value="g" ${item.unit_of_measure == 'g' ? 'selected' : ''}>Gram</option>
                                    <option value="lb" ${item.unit_of_measure == 'lb' ? 'selected' : ''}>Pound</option>
                                    <option value="oz" ${item.unit_of_measure == 'oz' ? 'selected' : ''}>Ounce</option>
                                    <option value="l" ${item.unit_of_measure == 'l' ? 'selected' : ''}>Liter</option>
                                    <option value="ml" ${item.unit_of_measure == 'ml' ? 'selected' : ''}>Milliliter</option>
                                    <option value="m" ${item.unit_of_measure == 'm' ? 'selected' : ''}>Meter</option>
                                    <option value="cm" ${item.unit_of_measure == 'cm' ? 'selected' : ''}>Centimeter</option>
                                    <option value="mm" ${item.unit_of_measure == 'mm' ? 'selected' : ''}>Millimeter</option>
                                    <option value="ft" ${item.unit_of_measure == 'ft' ? 'selected' : ''}>Foot</option>
                                    <option value="in" ${item.unit_of_measure == 'in' ? 'selected' : ''}>Inch</option>
                                </select>
                            </td>
                            <td>
                            
                            <p class="quanity-in-stock" id="quantity-in-stock-1" style="display:none;"></p>
                            <input type="number" id="product-quantity-${rowIndex}" name="invoice_quantity[]" value="${item.quantity}" placeholder="Enter Quantity" oninput="calculateInvoice()">
                            </td>
                            <td>
                                <div class="form-group discount-checkboxs" id="discount-checkboxs-${rowIndex}" style="display: ${item.discount_enable ? 'block' : 'none'};">
                                    <label for="discount-enable-${rowIndex}">
                                        <input type="checkbox" id="discount-enable-${rowIndex}" name="discount-enable[]" onclick="toggleDiscountFields(${rowIndex})" onchange="calculateInvoice()" value="${item.discount_enable ? 1 : 0}" ${item.discount_enable ? 'checked' : ''}>
                                        <span class="checkboxes"></span> Discounts
                                        <input type="hidden" id="discount_enable_${rowIndex}" name="discount_enable[]" value="${item.discount_enable ? 1 : 0}">
                                    </label>
                                </div>
                                <div id="discount-fields-${rowIndex}" class="discount-fields" style="display: ${item.discount_enable ? 'block' : 'none'};">
                                    <input type="number" id="discount-rate-${rowIndex}" name="discount_rate[]" placeholder="Discount Rate" value="${item.discount_rate || ''}" oninput="calculateInvoice()">
                                    <input type="number" id="discount-amount-${rowIndex}" name="discount_amount[]" placeholder="Discount Amount" value="${item.discount_amount || ''}" oninput="calculateInvoice()">
                                </div>
                                <input type="number" id="product-rate-${rowIndex}" name="invoice_rate[]" value="${item.unit_price}" onchange="calculateInvoice()" oninput="changeUnitPrice(this.value,${rowIndex})">
                                <input type="hidden" name="unit-price[]" id="unit-price-${rowIndex}" value="${item.product_price}">
                            </td>
                            <td>
                                <div class="form-group gst-checkboxs" id="gst-checkboxs-${rowIndex}">
                                    <label for="tax-inclusive-enable">
                                        <input type="checkbox" id="tax-inclusive-enable-${rowIndex}" onchange="taxEnable(${rowIndex})" name="tax-inclusive-enable[]" ${item.tax_inclusive_enable ? 'checked' : ''}>
                                        <span class="checkboxes"></span> Tax inclusive
                                        <input type="hidden" id="tax_inclusive_enable_${rowIndex}" name="tax_inclusive_enable[]" value="${item.tax_inclusive_enable ? 1 : 0}">
                                        <input type="hidden" name="tax_percentage[]" id="tax-percentage-${rowIndex}" value="${item.tax_percentage}">
                                    </label>
                                </div>
                                <input type="number" id="product-amount-${rowIndex}" name="invoice_amount[]" value="${item.amount}" oninput="calculateInvoice()" readonly>
                            </td>
                            <td>
                                <div class="form-row">
                                    <div id="high-${rowIndex}" style="display: ${item.high ? 'block' : 'none'};">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#4CAF50">
                                            <path d="M440-160v-487L216-423l-56-57 320-320 320 320-56 57-224-224v487h-80Z" />
                                        </svg>
                                    </div>
                                    <div id="low-${rowIndex}" style="display: ${item.low ? 'block' : 'none'};">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#F44336">
                                            <path d="M440-800v487L216-537l-56 57 320 320 320-320-56-57-224 224v-487h-80Z" />
                                        </svg>
                                    </div>
                                    <div class="remove-item">
                                        <button type="button" class="circular-button remove-row">x</button>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                    });

                    $('#cart-body').html(itemsHtml);
                    //calculateInvoice();
                }
            });


            function searchProductNameOnInput(data, count) {
                let inputValue = data.target.value;
                if (inputValue.length > 0) {
                    $.ajax({
                        url: '<?= MODULES . '/vendor_invoice/json/items_list.php' ?>',
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
                        url: '<?= MODULES . '/vendor_invoice/json/customers_list.php' ?>',
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

            function toggleDiscountFields(count) {
                const discountCheckbox = document.getElementById('discount-enable-' + count);
                const discountFields = document.getElementById('discount-fields-' + count);


                if (discountCheckbox.checked) {
                    document.getElementById('discount_enable_' + count).value = 1;
                    discountFields.style.display = 'block';
                } else {
                    document.getElementById('discount_enable_' + count).value = 0;
                    discountFields.style.display = 'none';
                }
            }

            function changeUnitPrice(price, count) {
                const unitPrice = document.getElementById('unit-price-' + count);
                const productRate = document.getElementById('product-rate-' + count);
                unitPrice.value = productRate.value;
            }

            function confirmInvoice() {
                $.ajax({
                    url: '<?= MODULES . '/vendor_invoice/components/confirm_update_invoice_popup.php?type=confirm&page_id=8&access=1' ?>',
                    type: 'POST',
                    data: $('#invoice-form').serialize(),
                    success: function(response) {
                        $('#invoice-modal').html(response);
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
                        $('#cancelInvoice').on('click', function() {
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