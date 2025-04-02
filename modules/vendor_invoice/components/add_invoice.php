<?php require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'add') {
?>
        <section id="add-invoice">
            <form class="form-container" id="invoice-form">
                <h2>Invoice Form</h2>
                <div class="form-row">
                    <div class="form-group">
                        <div class="autocomplete">
                            <label for="vendor-name">Vendor Name</label>
                            <input type="text" id="vendor-name" name="vendor_name" class="autocomplete-input" placeholder="Enter vendor Name..." oninput="searchVendorNameOnInput(event)">
                            <ul class="autocomplete-results" id="results"></ul>
                            <input type="hidden" id="vendor-id" name="vendor_id">
                            <input type="hidden" id="vendor-state" name="vendor_state">
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
                        <tbody id="cart-body">
                            <input type="hidden" name="total_item_count" id="total-item-count" value="1">
                            <tr>
                                <td>1</td>
                                <td>
                                    <div class="autocomplete">
                                        <input type="text" id="item-name-1" class="autocomplete-input in-item-name" placeholder="Enter Item Name..." oninput="searchProductNameOnInput(event,1)" name="item-name[]" onclick="checkVendor()" readonly>
                                        <ul class="autocomplete-results" id="results-1"></ul>
                                        <input type="hidden" name="item-id[]" id="item-id-1">
                                        <input type="hidden" name="item-gst-amount[]" id="item-gst-amount-1">
                                    </div>

                                </td>
                                <td>
                                    <select id="invoice_unit_of_measure-1" name="invoice_unit_of_measure[]" required onchange="calculateInvoice()">
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
                                    <input type="hidden" id="product_unit_of_measure1" name="product_unit_of_measure[]">
                                </td>
                                <td>
                                    <p class="quanity-in-stock" id="quantity-in-stock-1" style="display:none;"></p>
                                    <input type="number" id="product-quantity-1" name="invoice_quantity[]" value="0" placeholder="Enter Quantity" oninput="calculateInvoice()">
                                </td>
                                <td>
                                    <div class="form-group discount-checkboxs" id="discount-checkboxs-1" style="display: none;">
                                        <label for="discount-enable-1">

                                            <input type="checkbox" id="discount-enable-1" name="discount-enable[]" onclick="toggleDiscountFields(1)" onchange="calculateInvoice()" value="0">
                                            <span class="checkboxes"></span> Discounts
                                            <input type="hidden" id="discount_enable_1" name="discount_enable[]" value="0">
                                        </label>
                                    </div>
                                    <div id="discount-fields-1" class="discount-fields" style="display: none;">
                                        <input type="number" id="discount-rate-1" name="discount_rate[]" placeholder="Discount Rate" oninput="calculateInvoice()" value="0.00">
                                        <input type="number" id="discount-amount-1" name="discount_amount[]" placeholder="Discount Amount" oninput="calculateInvoice()" value="0.00">
                                    </div>
                                    <input type="number" id="product-rate-1" name="invoice_rate[]" value="0.00" onchange="calculateInvoice()" oninput="changeUnitPrice(this.value,1)">
                                    <input type="hidden" name="unit-price[]" id="unit-price-1">
                                </td>
                                <td>
                                    <div class="form-group gst-checkboxs" id="gst-checkboxs-1">
                                        <label for="tax-inclusive-enable">
                                            <input type="checkbox" id="tax-inclusive-enable-1" onchange="taxEnable(1)" name="tax-inclusive-enable[]">
                                            <span class="checkboxes"></span> Tax inclusive
                                            <input type="hidden" id="tax_inclusive_enable_1" name="tax_inclusive_enable[]" value="0">
                                            <input type="hidden" name="tax_percentage[]" id="tax-percentage-1">
                                        </label>
                                    </div>
                                    <input type="number" id="product-amount-1" name="invoice_amount[]" value="0.00" oninput="calculateInvoice()" readonly>

                                </td>
                                <td>
                                    <div class="form-row">
                                        <div id="high-1" style="display: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#4CAF50">
                                                <path d="M440-160v-487L216-423l-56-57 320-320 320 320-56 57-224-224v487h-80Z" />
                                            </svg>
                                        </div>
                                        <div id="low-1" style="display: none;">

                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#F44336">
                                                <path d="M440-800v487L216-537l-56 57 320 320 320-320-56-57-224 224v-487h-80Z" />
                                            </svg>
                                        </div>
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
                            <input type="number" id="sgst" name="sgst" placeholder="S GST Amount" oninput="calculateInvoice()" readonly>
                        </div>
                        <div class="form-group">
                            <label for="cgst">C GST</label>
                            <input type="number" id="cgst" name="cgst" placeholder="C GST Amount" oninput="calculateInvoice()" readonly>
                        </div>
                        <div class="form-group">
                            <label for="igst">I GST</label>
                            <input type="number" id="igst" name="igst" placeholder="I GST Amount" oninput="calculateInvoice()" readonly>
                        </div>
                    </div>
                    <div class="form-row" id="charges-details">
                        <div class="form-group">
                            <label for="shipping_charges">Shipping Charges</label>
                            <input type="number" id="shipping_charges" name="shipping_charges" placeholder="Shipping Charges Amount" oninput="calculateInvoice()" value="0.00">
                        </div>
                        <div class="form-group">
                            <label for="handling_fees_amount">Handling Fees</label>
                            <input type="number" id="handling_fees_amount" name="handling_fees_amount" placeholder="Handling fees Amount" oninput="calculateInvoice()" value="0.00">
                        </div>
                        <div class="form-group">
                            <label for="storage_fees">Storage Fees</label>
                            <input type="number" id="storage_fees" name="storage_fees" placeholder="Storage Fees Amount" oninput="calculateInvoice()" value="0.00">
                        </div>
                    </div>
                    <label for="summary">Summary</label>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="total-value">Total Amount</label>
                            <input type="number" id="total-value" name="total_value" oninput="calculateInvoice()" placeholder="Total Value Amount" readonly>
                            <input type="hidden" id="total-gst-amount" name="total-gst-amount">
                        </div>
                        <div class="form-group">
                            <label for="adjustment">Adjustment</label>
                            <input type="number" id="adjustment" name="adjustment" oninput="calculateInvoice()" value="0.00">
                        </div>
                        <div class="form-group">
                            <label for="grand-total">Grand Total</label>
                            <input type="number" id="grand-total" name="grand_total" oninput="calculateInvoice()" placeholder="Grand Total Amount" readonly>
                        </div>

                    </div>
                    <label for="payment">Transaction</label>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="payment-mode">Amount In Words</label>

                            <textarea name="amount-inwords" id="amount-inwords" cols="30" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="payment-mode">Payment mode</label>
                            <select id="payment-mode" name="payment_mode">
                                <option value="cash" disabled selected>Select Payment Mode</option>
                                <option value="cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Debit/Credit Card">Debit/Credit Card</option>
                            </select>
                        </div>

                    </div>
                    <input type="button" value="Submit" class="submit" onclick="confirmInvoice()" />
            </form>
        </section>

        <script src="<?= MODULES . '/vendor_invoice/js/add_invoice.js' ?>"></script>
        <script>
            $.ajax({
                url: '<?= MODULES . '/vendor_invoice/ajax/fetch_last_invoice_number.php' ?>',
                type: 'POST',
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        $('#invoice-number').val(response.new_invoice_number);
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
                    url: '<?= MODULES . '/vendor_invoice/components/confirm_invoice_popup.php?type=confirm&page_id=7&access=1' ?>',
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