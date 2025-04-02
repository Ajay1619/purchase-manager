<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'delete') {
        $vendor_id = $_POST['vendor_id'];
?>
        <!-- The Confirmation Popup -->
        <div id="confirmationPopup" class="confirmation-popup">
            <div id="toast-container"></div>
            <!-- Popup content -->
            <div class="popup-content">
                <span class="close">&times;</span>
                <h2 class="popup-title">Delete Vendor</h2>
                <div class="popup-body">
                    <p>Are you sure you want to delete this Vendor?</p>
                    <div class="popup-footer">
                        <button id="confirmDelete" onclick="confirmDelete(<?= $vendor_id ?>)" class="btn btn-danger">Confirm</button>
                        <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>



        <script>
            function confirmDelete(vendor_id) {
                $.ajax({
                    url: '<?= MODULES . '/vendor/ajax/delete_vendor.php' ?>',
                    type: 'POST',
                    data: {
                        vendor_id: vendor_id
                    },
                    beforeSend: function() {
                        // Show the loading content
                        $('#loading').fadeIn();
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
                    },

                    complete: function() {
                        // Hide the loading content after the request is complete
                        $('#loading').fadeOut();
                    }
                });
            }
        </script>
<?php }
} ?>