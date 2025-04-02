<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'delete') {
        $product_id = $_POST['product_id'];
?>
        <!-- The Confirmation Popup -->
        <div id="confirmationPopup" class="confirmation-popup">
            <!-- Popup content -->
            <div class="popup-content">
                <div id="toast-container"></div>
                <span class="close">&times;</span>
                <h2 class="popup-title">Delete Product</h2>
                <div class="popup-body">
                    <p>Are you sure you want to delete this product?</p>
                    <div class="popup-footer">
                        <button id="confirmDelete" onclick="confirmDelete(<?= $product_id ?>)" class="btn btn-danger">Confirm</button>
                        <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>



        <script>
            function confirmDelete(product_id) {
                $.ajax({
                    url: '<?= MODULES . '/products/ajax/delete_product.php' ?>',
                    type: 'POST',
                    data: {
                        product_id: product_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        </script>
<?php }
} ?>