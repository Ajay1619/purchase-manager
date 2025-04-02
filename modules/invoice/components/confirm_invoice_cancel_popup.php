<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'cancel') {
        $invoice_id = isset($_POST['invoice_id']) ? sanitizeInput($_POST['invoice_id'], 'int') : '';
?>
        <div id="confirmationPopup" class="confirmation-popup">
            <!-- Popup content -->
            <div class="popup-content">
                <span class="close">&times;</span>
                <h2 class="popup-title">Confirm Delete</h2>
                <div class="popup-body">
                    <p>Are you sure you want to delete this Invoice?</p>
                    <div class="popup-footer">
                        <button id="confirmInvoice" class="btn btn-danger" onclick="confirmInvoiceCancelation()">Confirm</button>
                        <button id="cancelInvoice" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function confirmInvoiceCancelation() {
                $.ajax({
                    url: '<?= MODULES . '/invoice/ajax/cancel_invoice.php' ?>',
                    type: 'POST',
                    data: {
                        invoice_id: <?= $invoice_id ?>
                    },
                    success: function(response) {
                        var response = JSON.parse(response);
                        if (response.status === 'success') {

                            showToast('success', response.messages);
                            var newUrl = window.location.pathname;
                            history.pushState({}, '', newUrl);
                            location.reload();
                        } else if (response.status === 'error') {
                            var messages = response.messages;
                            if (messages.length > 0) {
                                messages.forEach(message => {
                                    showToast('error', message);
                                });

                            } else {
                                showToast('error', 'An error occurred while creating the invoice');
                            }
                            //$('#toast-container').html("");
                            // var newUrl = window.location.pathname + '?type=add';
                            // history.pushState({}, '', newUrl);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            // Close the popup
            document.querySelector('.close').addEventListener('click', function() {
                document.getElementById('toast-container').innerHTML = '';
            });

            // Cancel button functionality
            document.getElementById('cancelInvoice').addEventListener('click', function() {
                document.getElementById('toast-container').innerHTML = '';
            });
        </script>
<?php }
} ?>