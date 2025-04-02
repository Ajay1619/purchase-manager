<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'cancel') {
        $quotation_id = $_POST['quotation_id'];
?>
        <!-- The Confirmation Popup -->
        <div id="confirmationPopup" class="confirmation-popup">
            <!-- Popup content -->
            <div class="popup-content">
                <span class="close">&times;</span>
                <h2 class="popup-title">Cancel Quotation</h2>
                <div class="popup-body">
                    <p>Are you sure you want to cancel this Quotation?</p>
                    <div class="popup-footer">
                        <button id="confirmCancel" onclick="confirmcancel(<?= $quotation_id ?>)" class="btn btn-danger">Confirm</button>
                        <button id="cancelCancel" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>



        <script>
            function confirmcancel(quotation_id) {
                $.ajax({
                    url: '<?= MODULES . '/quotation/ajax/cancel_quotation.php' ?>',
                    type: 'POST',
                    data: {
                        quotation_id: quotation_id
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