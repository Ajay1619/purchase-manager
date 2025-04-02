<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'edit') {
        $product_id = $_POST['product_id'];
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <div id="toast-container"></div>
                <span class="close">&times;</span>
                <h2 class="modal-title">Edit Product</h2>
                <div class="modal-body">
                    <form id="update-product-form">
                        <div class="form-row">
                            <div class="form-column">
                                <label for="product_name">Product Name:</label>
                                <input type="text" id="product_name" name="product_name" placeholder="Enter product name" required>
                                <input type="hidden" id="product_id" name="product_id" value="<?= $product_id ?>">
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
                                <label for="price">Price:</label>
                                <input type="number" id="price" name="product_price" placeholder="Enter price" step="0.01" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-column">
                                <label for="pricingType">Pricing Type:</label>
                                <select id="pricingType" name="pricing_type" required>
                                    <option value="" disabled selected>Select Tax Type</option>
                                    <option value="0">Inclusive</option>
                                    <option value="1">Exclusive</option>
                                </select>
                            </div>
                            <div class="form-column">
                                <label for="taxPercentage">GST Tax Percentage:</label>
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
                                        <input type="checkbox" id="discount-enable" name="discount_enable" value="1">
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
                                <div id="itemDescriptions"></div>
                            </div>
                        </div>
                        <input type="hidden" name="item_length" id="item_length">
                        <div class="form-row">
                            <div class="add-item">
                                <h5>Add Item</h5>
                                <button type="button" class="circular-button" onclick="add_item_update(document.getElementById('item_length').value)">+</button>
                            </div>
                            <div class="line"></div>
                        </div>

                        <div class="form-row">
                            <div class="form-column">
                                <label>Terms & Condition:</label>
                                <textarea id="notes" name="product_notes" placeholder="Enter Product's Terms & Condition" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="form-row buttons">
                            <input type="submit" value="Submit" />
                            <button type="button" class="button cancel-button">Cancel</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $.ajax({
                    url: '<?= MODULES . '/products/ajax/fetch_view_product_details.php' ?>',
                    type: 'GET',
                    data: {
                        product_id: <?= $product_id ?>
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            const productDetails = response.product_details;
                            const itemDetails = response.item_details;
                            // Populate form fields with the retrieved data
                            $('#product_name').val(productDetails.product_name);
                            $('#hsnCode').val(productDetails.hsn_code);
                            $('#type').val(productDetails.product_type).change();
                            $('#category').val(productDetails.product_category).change();
                            $('#unitOfMeasure').val(productDetails.unit_of_measure).change();
                            $('#pricingType').val(productDetails.pricing_type).change();
                            $('#taxPercentage').val(productDetails.tax_percentage).change();
                            $('#discount-enable').prop('checked', productDetails.discountable).change();
                            $('#price').val(productDetails.unit_price);
                            $('#bottomStock').val(productDetails.bottom_stock);
                            $('#orderQuantity').val(productDetails.order_quantity);
                            $('#notes').val(productDetails.prouct_terms_and_conditions);
                            $('#item_length').val(itemDetails.length);

                            // Populate the descriptions
                            $('#itemDescriptions').empty();
                            if (itemDetails.length > 0) {
                                itemDetails.forEach((item, index) => {
                                    // Check if item properties are not null or undefined
                                    if (item.item_product_name && item.item_product_code && item.item_unit_of_measure && item.item_quantity_used) {
                                        var itemHtml = `
                                        <div id="item-${index + 1}">
                                        <h4>Item #${index + 1}</h4>
                                    <div class="description" id="item-${index + 1}">
                                  
                                        <div class="description-items">
                                            <div class="autocomplete">
                                                    <input type="text" id="dpname_${index + 1}" name="dpname[]" class="autocomplete-input" placeholder="Enter Item Name..." value="${item.item_product_name}" oninput="searchProductNameOnInput(event,${index + 1})">
                                                    <ul class="autocomplete-results" id="results-${index + 1}"></ul>
                                                </div>
                                            <input type="hidden" id="itemid_${index + 1}" name="itemid[]" value="${item.item_id}">
                                            <input type="hidden" id="usedproductid_${index + 1}" name="usedproductid[]" value="${item.used_product_id}">
                                        </div>
                                        <div class="description-items">
                                            <input type="text" id="dpcode_${index + 1}" name="dpcode[]" value="${item.item_product_code}" placeholder="Product Code" readonly>
                                        </div>
                                        <div class="description-items">
                                            <select id="dpuom" name="dpuom[]" required>
                                                <option value="" disabled ${!item.item_unit_of_measure ? 'selected' : ''}>Select unit of measure</option>
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
                                        </div>
                                        <div class="description-items">
                                            <input type="text" name="dpq[]" value="${item.item_quantity_used}" placeholder="Quantity">
                                            <button type="button" class="circular-button remove-item">x</button>
                                        </div>
                                    </div>
                                    </div>
                                `;
                                        $('#itemDescriptions').append(itemHtml);
                                    }
                                    const removeButton = document.querySelector(`#item-${index + 1} .remove-item`);
                                    if (removeButton) {
                                        removeButton.addEventListener('click', function() {
                                            document.querySelector(`#item-${index + 1}`).remove();
                                            updateItemNumbers();

                                        });
                                    }
                                });
                            }

                            // Show the modal
                            $('#myModal').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });

                $('#update-product-form').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: '<?= MODULES . '/products/ajax/update_product_form.php' ?>',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                showToast('success', response.message);
                                $('#myModal').hide();
                                var newUrl = window.location.pathname;
                                history.pushState({}, '', newUrl);
                                location.reload();

                            } else {
                                showToast('error', response.message);
                            }
                        },
                        error: function() {
                            alert('An error occurred. Please try again.');
                        }
                    });
                });

                // Close modal
                $('.close, .cancel-button').on('click', function() {
                    $('#myModal').hide();
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
                                searchProductNameUpdate(response.data, count);
                            }
                        },
                        error: function() {
                            $('#response').text('An error occurred');
                        }
                    });
                };
            }
        </script>
<?php }
} ?>