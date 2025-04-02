<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'add') {
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">Add Product | Inventory</h2>
                <div class="modal-body">
                    <div class="inventory-container">
                        <form method="POST" id="add-inventory-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <div class="autocomplete">
                                        <label for="product-name">Product Name</label>
                                        <input type="text" id="product-name" class="autocomplete-input product-name" placeholder="Enter Product Name..." oninput="searchProductNameOnInput(event)" name="product_name">
                                        <ul class="autocomplete-results" id="results"></ul>
                                        <input type="hidden" name="product-id" id="product-id">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="product-unit-of-measure" id="product-unit-of-measure">
                                    <label for="unit-of-quantity">Unit of Quantity</label>
                                    <select id="unit-of-quantity" name="unit-of-quantity" required disabled>
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
                                </div>
                            </div>
                            <div class="form-row">

                                <div class="form-group">
                                    <label for="quantity-added">Added Quantity </label>
                                    <input type="number" id="quantity-added" name="quantity-added" placeholder="Enter quantity added" required>
                                </div>
                            </div>
                            <input type="submit" value="Submit" />
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function searchProductNameOnInput(data) {
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
                                searchProductName(response.data);

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

            $('#add-inventory-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/inventory/ajax/add_inventory_form.php' ?>',
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