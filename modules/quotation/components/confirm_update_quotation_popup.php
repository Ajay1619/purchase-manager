<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    echo $_GET['type'];
    if (isset($_GET['type']) && $_GET['type'] == 'confirm') {
        $data = $_POST;
?>
        <div id="confirmationPopup" class="confirmation-popup">
            <!-- Popup content -->
            <div class="popup-content">
                <span class="close">&times;</span>
                <h2 class="popup-title">Confirm Update Quotation</h2>
                <div class="popup-body">
                    <p>Are you sure you want to update this Quotation?</p>
                    <div class="popup-footer">
                        <button id="confirmQuotation" class="btn btn-success" onclick="confirmQuotationCreation()">Confirm</button>
                        <button id="cancelQuotation" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function confirmQuotationCreation() {
                $.ajax({
                    url: '<?= MODULES . '/quotation/ajax/update_quotation.php' ?>',
                    type: 'POST',
                    data: <?= json_encode($data) ?>,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            //showToast('success', response.messages);
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
            document.getElementById('cancelQuotation').addEventListener('click', function() {
                document.getElementById('confirmationPopup').style.display = 'none';
            });
        </script>
<?php }
} ?>