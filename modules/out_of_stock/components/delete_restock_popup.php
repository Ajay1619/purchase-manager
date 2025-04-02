<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'confirm') {
        $out_of_stock_id = isset($_POST['out_of_stock_id']) ? sanitizeInput($_POST['out_of_stock_id'], 'int') : '';
?>
        <div id="confirmationPopup" class="confirmation-popup">
            <!-- Popup content -->
            <div class="popup-content">
                <span class="close">&times;</span>
                <h2 class="popup-title">Confirm Cancel Purchase Order </h2>
                <div class="popup-body">
                    <p>Are you sure you want to cancel this Purchase Order?</p>
                    <div class="popup-footer">
                        <button id="confirmOrder" class="btn btn-danger" onclick="confirmOrderDeletion()">Confirm</button>
                        <button id="cancelOrder" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function confirmOrderDeletion() {
                $.ajax({
                    url: '<?= MODULES . '/out_of_stock/ajax/confirm_update_restock.php' ?>',
                    type: 'POST',
                    data: {
                        out_of_stock_id: <?= $out_of_stock_id ?>,
                        update: 3
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            showToast('success', response.messages);
                            var newUrl = window.location.pathname;
                            history.pushState({}, '', newUrl);
                            location.reload();
                        } else {
                            showToast('error', response.messages);

                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            // Close the popup
            document.querySelector('.close').addEventListener('click', function() {
                document.getElementById('confirmationPopup').style.display = 'none';
            });

            // Cancel button functionality
            document.getElementById('cancelOrder').addEventListener('click', function() {
                document.getElementById('confirmationPopup').style.display = 'none';
            });
        </script>
<?php }
} ?>