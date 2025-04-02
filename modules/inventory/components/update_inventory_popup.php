<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'edit') {
        $inventory_id = isset($_POST['inventory_id']) ? sanitizeInput($_POST['inventory_id'], 'int') : '';
        $product_id = isset($_POST['product_id']) ? sanitizeInput($_POST['product_id'], 'int') : '';
        $product_name = isset($_POST['product_name']) ? sanitizeInput($_POST['product_name'], 'string') : '';
        $unit_of_measure = isset($_POST['unit_of_measure']) ? sanitizeInput($_POST['unit_of_measure'], 'string') : '';
        $quantity_in_stock = isset($_POST['quantity_in_stock']) ? sanitizeInput($_POST['quantity_in_stock'], 'string') : '';
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">Product Usage Form</h2>
                <div class="modal-body">
                    <div class="inventory-container">
                        <form id="update-inventory-form" method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="product-name">Product Name</label>
                                    <input type="text" id="product-name" name="product-name" placeholder="Enter product name" value="<?= $product_name ?>" readonly required>
                                    <input type="hidden" id="product-id" name="product-id" value="<?= $product_id ?>">
                                    <input type="hidden" id="inventory-id" name="inventory-id" value="<?= $inventory_id ?>">
                                </div>
                                <div class="form-group">
                                    <label for="quantity-in-stock">Quantity In Stock</label>
                                    <input type="number" id="quantity-in-stock" name="quantity-in-stock" placeholder="Enter quantity in stock" value="<?= $quantity_in_stock ?>" readonly>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <input type="hidden" name="product-unit-of-measure" id="product-unit-of-measure" value="<?= $unit_of_measure ?>">
                                    <label for="unit-of-quantity">Unit of Quantity Used</label>
                                    <select id="unit-of-quantity" name="unit-of-quantity" value="<?= $unit_of_measure ?>" required>
                                        <option value="" disabled selected>Select unit of measure</option>
                                        <option value=" piece">Piece</option>
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
                                </div>
                                <div class="form-group">
                                    <label for="quantity-used">Quantity Used</label>
                                    <input type="text" id="quantity-used" name="quantity-used" placeholder="Enter quantity used" required>
                                </div>
                            </div>
                            <input type="submit" value="Submit" />
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $('#update-inventory-form').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/inventory/ajax/update_inventory_form.php' ?>',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'error') {
                            showToast('error', response.messages);
                        } else if (response.status === 'success') {
                            showToast('success', response.messages);
                            var newUrl = window.location.pathname;
                            history.pushState({}, '', newUrl);
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        </script>
<?php }
} ?>