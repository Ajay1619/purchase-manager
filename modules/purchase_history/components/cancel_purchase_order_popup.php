<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'cancel') {
        $purchase_order_id = $_POST['purchase_order_id'];
?>
        <!-- The Confirmation Popup -->
        <div id="confirmationPopup" class="confirmation-popup">
            <!-- Popup content -->
            <div class="popup-content">
                <span class="close">&times;</span>
                <h2 class="popup-title">Cancel Order</h2>
                <div class="popup-body">
                    <p>Are you sure you want to cancel this Order?</p>
                    <div class="popup-footer">
                        <button id="confirmCancel" onclick="confirmcancel(<?= $purchase_order_id ?>)" class="btn btn-danger">Confirm</button>
                        <button id="cancelCancel" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>



        <script>
            function confirmcancel(purchase_order_id) {
                $.ajax({
                    url: '<?= MODULES . '/purchase_history/ajax/cancel_purchase_order.php' ?>',
                    type: 'POST',
                    data: {
                        purchase_order_id: purchase_order_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            //showToast()
                            showToast('success', response.message);

                            location.reload();
                        } else {
                            showToast('error', response.message);

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