<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>

    <!-- Vendor Statistical Card Data -->
    <section id="card-data"></section>

    <!-- Add Product Button -->
    <section id="button-container">
        <button id="popupButton" onclick="add_quotation()">
            Add Quotation
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                <path d="M200-80q-33 0-56.5-23.5T120-160v-480q0-33 23.5-56.5T200-720h80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720h80q33 0 56.5 23.5T840-640v480q0 33-23.5 56.5T760-80H200Zm280-320q83 0 141.5-58.5T680-600h-80q0 50-35 85t-85 35q-50 0-85-35t-35-85h-80q0 83 58.5 141.5T480-400ZM360-720h240q0-50-35-85t-85-35q-50 0-85 35t-35 85Z" />
            </svg>
        </button>
    </section>
    <!-- quotation Table -->
    <section id="quotation-table"></section>

    <section id="quotation-modal"></section>

    <script>
        // Load quotation Statistics Cards
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/quotation/components/cards.php' ?>',
            success: function(response) {
                $('#card-data').html(response);
            },
            error: function(xhr, status, error) {
                showToast('error', error);
            }
        });

        // Load quotation table
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/quotation/components/quotation_table.php' ?>',
            success: function(response) {
                $('#quotation-table').html(response);
            },
            error: function(xhr, status, error) {
                showToast('error', error);
            }
        });
    </script>
<?php } ?>