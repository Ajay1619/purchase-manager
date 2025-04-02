<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'delete') {
        $customer_id = $_POST['customer_id'];
?>
        <!-- The Confirmation Popup -->
        <div id="confirmationPopup" class="confirmation-popup">
            <div id="toast-container"></div>
            <!-- Popup content -->
            <div class="popup-content">
                <span class="close">&times;</span>
                <h2 class="popup-title">Delete Customer</h2>
                <div class="popup-body">
                    <p>Are you sure you want to delete this Customer?</p>
                    <div class="popup-footer">
                        <button id="confirmDelete" onclick="confirmDelete(<?= $customer_id ?>)" class="btn btn-danger">Confirm</button>
                        <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>



        <script>
            function confirmDelete(customer_id) {
                $.ajax({
                    url: '<?= MODULES . '/customer/ajax/delete_customer.php' ?>',
                    type: 'POST',
                    data: {
                        customer_id: customer_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
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