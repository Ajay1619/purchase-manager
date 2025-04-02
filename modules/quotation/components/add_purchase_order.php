<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $product_details = $_POST['product_data'];
    $vendor_details = $_POST['vendor_details'];
?>
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div id="toast-container"></div>
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-title">Create Purchase Order</h2>
            <div class="modal-body">
                <form id="create-purchase-form" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-name">Product Name</label>
                            <input type="text" id="product-name" name="product_name" placeholder="Enter product name" value="<?= $product_details['product_name'] ?>" readonly required>
                            <input type="hidden" id="product-id" name="product_id" value="<?= $product_details['product_id'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="product_code">Product Code</label>
                            <input type="text" id="product_code" name="product_code" placeholder="Enter Product Code" value="<?= $product_details['product_code'] ?>" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="hidden" name="product-unit-of-measure" id="product-unit-of-measure" value="<?= $product_details['unit_of_measure'] ?>">
                            <label for="unit-of-measure">Unit of Measure</label>
                            <select id="unit-of-measure" name="unit_of_measure" required>
                                <option value="" disabled <?= empty($product_details['unit_of_measure']) ? 'selected' : '' ?>>Select unit of measure</option>
                                <option value="piece" <?= $product_details['unit_of_measure'] == 'piece' ? 'selected' : '' ?>>Piece</option>
                                <option value="tonne" <?= $product_details['unit_of_measure'] == 'tonne' ? 'selected' : '' ?>>Tonne</option>
                                <option value="packets" <?= $product_details['unit_of_measure'] == 'packets' ? 'selected' : '' ?>>Packets</option>
                                <option value="kg" <?= $product_details['unit_of_measure'] == 'kg' ? 'selected' : '' ?>>Kilogram</option>
                                <option value="g" <?= $product_details['unit_of_measure'] == 'g' ? 'selected' : '' ?>>Gram</option>
                                <option value="lb" <?= $product_details['unit_of_measure'] == 'lb' ? 'selected' : '' ?>>Pound</option>
                                <option value="oz" <?= $product_details['unit_of_measure'] == 'oz' ? 'selected' : '' ?>>Ounce</option>
                                <option value="l" <?= $product_details['unit_of_measure'] == 'l' ? 'selected' : '' ?>>Liter</option>
                                <option value="ml" <?= $product_details['unit_of_measure'] == 'ml' ? 'selected' : '' ?>>Milliliter</option>
                                <option value="m" <?= $product_details['unit_of_measure'] == 'm' ? 'selected' : '' ?>>Meter</option>
                                <option value="cm" <?= $product_details['unit_of_measure'] == 'cm' ? 'selected' : '' ?>>Centimeter</option>
                                <option value="mm" <?= $product_details['unit_of_measure'] == 'mm' ? 'selected' : '' ?>>Millimeter</option>
                                <option value="ft" <?= $product_details['unit_of_measure'] == 'ft' ? 'selected' : '' ?>>Foot</option>
                                <option value="in" <?= $product_details['unit_of_measure'] == 'in' ? 'selected' : '' ?>>Inch</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="vendor_name">Vendor</label>
                            <input type="hidden" name="unit_price" id="unit_price">
                            <select id="vendor_name" name="vendor_name" required>
                                <option value="" disabled selected>Select Vendor</option>
                                <?php foreach ($vendor_details as $vendor) { ?>
                                    <option value="<?= $vendor['vendor_id'] ?>"
                                        data-unit-price="<?= $vendor['unit_price'] ?>"
                                        data-unit-measure="<?= $vendor['unit_of_measure'] ?>">
                                        <?= $vendor['vendor_company_name'] ?> (<?= CURRENCY_SYMBOL . formatNumberIndian($vendor['unit_price'], 2) ?> per <?= $vendor['unit_of_measure'] ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="order-quantity">Order Quantity</label>
                            <input type="text" id="order-quantity" name="order_quantity" placeholder="Enter quantity used" value="<?= $product_details['order_quantity'] ?>" required>
                        </div>
                    </div>
                    <input type="submit" value="Submit" />
                </form>
            </div>
        </div>
    </div>

    <script>
        // Update the unit price when a vendor is selected
        document.getElementById('vendor_name').addEventListener('change', function() {
            var selectedVendor = this.options[this.selectedIndex];
            var unitPrice = selectedVendor.getAttribute('data-unit-price');
            document.getElementById('unit_price').value = unitPrice;
        });

        // Handle form submission via AJAX
        $('#create-purchase-form').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: '<?= MODULES . '/quotation/ajax/add_purchase_order.php' ?>', // URL for the form submission
                type: 'POST',
                data: formData,
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status == 'success') {
                        showToast('success', response.messages);
                        history.pushState({}, '', window.location.pathname);
                        location.reload();
                    } else {
                        showToast('error', response.messages);

                    }
                },
                error: function() {
                    showToast('error', "There is some Error");
                }
            });
        });
    </script>
<?php } ?>