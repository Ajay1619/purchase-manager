<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $product_id = $_POST['product_id'];
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">View Product</h2>
                <div class="modal-body">
                    <div class="view-product-container">
                        <!-- Product Details -->
                        <div class="entry-row">
                            <div class="entry">
                                <h4 class="light-color">Product Name</h4>
                                <h4 id="productName"></h4>
                            </div>
                            <div class="entry">
                                <h4 class="light-color">Product Code</h4>
                                <h4 id="productCode"></h4>
                            </div>
                        </div>
                        <div class="entry-row">
                            <div class="entry">
                                <h4 class="light-color">Type</h4>
                                <h4 id="type"></h4>
                            </div>
                            <div class="entry">
                                <h4 class="light-color">Category</h4>
                                <h4 id="category"></h4>
                            </div>
                        </div>
                        <div class="entry-row">
                            <div class="entry">
                                <h4 class="light-color">Unit of Measure</h4>
                                <h4 id="unitOfMeasure"></h4>
                            </div>
                            <div class="entry">
                                <h4 class="light-color">Price</h4>
                                <h4 id="price"></h4>
                            </div>
                        </div>
                        <div class="entry-row">
                            <div class="entry">
                                <h4 class="light-color">Bottom Stock</h4>
                                <h4 id="bottomStock"></h4>
                            </div>
                            <div class="entry">
                                <h4 class="light-color">Order Quantity</h4>
                                <h4 id="orderQuantity"></h4>
                            </div>
                        </div>
                        <!-- Product Description -->
                        <div class="product-description">
                            <h3>Description</h3>
                            <!-- Description Items will be dynamically added here -->
                        </div>
                        <div class="entry">
                            <h4 class="light-color">Notes</h4>
                            <p id="notes"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
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
                        // Populate modal with fetched product details
                        $('#productName').text(productDetails.product_name);
                        $('#productCode').text(productDetails.product_code);
                        $('#type').text(productDetails.product_type);
                        $('#category').text(productDetails.product_category);
                        $('#unitOfMeasure').text(productDetails.unit_of_measure);
                        $('#price').text(productDetails.unit_price);
                        $('#bottomStock').text(productDetails.bottom_stock);
                        $('#orderQuantity').text(productDetails.order_quantity);
                        $('#notes').text(productDetails.prouct_terms_and_conditions);

                        // // Populate product items dynamically
                        var itemsHtml = '';
                        $.each(itemDetails, function(index, item) {
                            itemsHtml += `
                                <div class="description-item">
                                    <h4><strong>Item #${index + 1}</strong></h4>
                                    <div class="item-row">
                                        <div class="item-detail">
                                            <h4 class="light-color">Item Name</h4>
                                            <h4>${item.item_product_name}</h4>
                                        </div>
                                        <div class="item-detail">
                                            <h4 class="light-color">Item Code</h4>
                                            <h4>${item.item_product_code}</h4>
                                        </div>
                                        <div class="item-detail">
                                            <h4 class="light-color">Unit of Measure</h4>
                                            <h4>${item.item_unit_of_measure}</h4>
                                        </div>
                                        <div class="item-detail">
                                            <h4 class="light-color">Quantity</h4>
                                            <h4>${item.item_quantity_used}</h4>
                                        </div>
                                    </div>
                                </div>`;
                        });
                        $('.product-description').html(itemsHtml);

                        // // Show the modal after populating data
                        // $('#myModal').show();
                    } else {
                        alert('Failed to fetch product details: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error fetching product details: ' + error);
                }
            });
        </script>

<?php }
} ?>