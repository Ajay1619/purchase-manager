<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'add') {
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <div id="toast-container"></div>
                <span class="close">&times;</span>
                <h2 class="modal-title">Add Product </h2>
                <div class="modal-body">
                    <form id="add-product-form" method="POST">
                        <div class="form-section">
                            <div class="form-row">
                                <div class="form-column">
                                    <label for="productName">Product Name:</label>
                                    <input type="text" id="productName" name="product_name" placeholder="Enter product name" required>
                                </div>
                                <div class="form-column">
                                    <label for="hsnCode">HSN Code:</label>
                                    <input type="text" id="hsnCode" name="hsn_code" placeholder="Enter HSN code" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-column">
                                    <label for="type">Type:</label>
                                    <select id="type" name="product_type" required>
                                        <option value="">Select type...</option>
                                        <option value="0">Stock</option>
                                        <option value="1">Product</option>
                                    </select>
                                </div>
                                <div class="form-column">
                                    <label for="category">Category:</label>
                                    <select id="category" name="product_category" required>
                                        <option value="">Select category...</option>
                                        <option value="Accessories">Accessories</option>
                                        <option value="Automotive">Automotive</option>
                                        <option value="Beauty Products">Beauty Products</option>
                                        <option value="Books">Books</option>
                                        <option value="Clothing">Clothing</option>
                                        <option value="Edible">Edible</option>
                                        <option value="Electronics">Electronics</option>
                                        <option value="Footwear">Footwear</option>
                                        <option value="Furniture">Furniture</option>
                                        <option value="Gardening Tools">Gardening Tools</option>
                                        <option value="Health & Wellness">Health & Wellness</option>
                                        <option value="Home Appliances">Home Appliances</option>
                                        <option value="Sports Equipment">Sports Equipment</option>
                                        <option value="Stationery">Stationery</option>
                                        <option value="Toys">Toys</option>

                                    </select>

                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-column">
                                    <label for="unitOfMeasure">Unit of Measure:</label>
                                    <select id="unitOfMeasure" name="unit_of_measure" required>
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

                                <div class="form-column">
                                    <label for="price">Unit Price:</label>
                                    <input type="number" id="price" name="product_price" placeholder="Enter Unit price" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-column">
                                    <label for="pricingType">Pricing Type</label>
                                    <select id="pricingType" name="pricing_type" required>
                                        <option value="" disabled selected>Select Tax Type</option>
                                        <option value="0">Inclusive</option>
                                        <option value="1">Exclusive</option>
                                    </select>
                                </div>
                                <div class="form-column">
                                    <label for="taxPercentage">GST Tax Percentage</label>
                                    <select id="taxPercentage" name="tax_percentage" required>
                                        <option value="" disabled selected>Select Tax Percentage</option>
                                        <option value="0.00">0 %</option>
                                        <option value="5.00">5 %</option>
                                        <option value="12.00">12 %</option>
                                        <option value="18.00">18 %</option>
                                        <option value="28.00">28 %</option>
                                    </select>
                                </div>
                                <div class="form-column checkbox-column">
                                    <div class="form-group discount-checkbox">
                                        <label for="discount-enable">
                                            <input type="checkbox" id="discount-enable" name="discount_enable" value="1" checked>
                                            <span class="checkboxes"></span> Discountable
                                        </label>
                                    </div>
                                </div>

                            </div>


                            <div class="form-row">
                                <div class="form-column">
                                    <label for="bottomStock">Bottom Stock:</label>
                                    <input type="number" id="bottomStock" name="bottom_stock" placeholder="Enter bottom stock" required>
                                </div>
                                <div class="form-column">
                                    <label for="orderQuantity">Order Quantity:</label>
                                    <input type="number" id="orderQuantity" name="order_quantity" placeholder="Enter order quantity" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-column">
                                    <label>Description:</label>
                                    <div id="itemDescriptions">
                                        <h4>Item #1</h4>
                                        <div class="description">
                                            <div class="description-items">
                                                <div class="autocomplete">
                                                    <input type="text" id="dpname_1" name="dpname[]" class="autocomplete-input" placeholder="Enter Item Name..." oninput="searchProductNameOnInput(event,1)">
                                                    <ul class="autocomplete-results" id="results-1"></ul>
                                                    <input type="hidden" id="item_id_1" name="item_id[]">
                                                </div>
                                            </div>
                                            <div class="description-items">
                                                <input type="text" id="dpcode_1" name="dpcode[]" placeholder="Product Code" readonly>
                                            </div>
                                            <div class="description-items">
                                                <select id="dpuom_1" name="dpuom[]">
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
                                            <div class="description-items">
                                                <input type="text" id="dpq_1" name="dpq[]" placeholder="Quantity">
                                                <button type="button" class="circular-button remove-item">x</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="add-item">
                                    <h5>Add Item</h5>
                                    <button type="button" class="circular-button" onclick="add_item()">+</button>
                                </div>
                                <div class="line"></div>
                            </div>


                            <div class="form-row">
                                <div class="form-column">
                                    <label>Terms & Condition:</label>
                                    <textarea id="notes" name="product_notes" placeholder="Enter Product's Terms &  Condition" rows="4"></textarea>
                                </div>
                            </div>

                            <input type="submit" class="submit" value="Submit" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $('#add-product-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/products/ajax/add_product_form.php' ?>',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'error') {
                            showToast('error', response.message);
                        } else if (response.status === 'success') {

                            showToast('success', response.message);
                            var newUrl = window.location.pathname;
                            history.pushState({}, '', newUrl);
                            location.reload();
                        }
                    },
                    error: function() {
                        showToast('error', 'An error occurred. Please try again later.');
                    }
                });
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
                            showToast('error', 'An error occurred. Please try again later.');
                        }
                    });
                };
            }
        </script>
<?php }
} ?>