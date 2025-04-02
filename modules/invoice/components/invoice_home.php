<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>

    <!-- Invoice Statistical Card Data -->
    <section id="card-data"></section>

    <!-- Add Invoice Button -->
    <section id="button-container">
        <button id="popupButton" onclick="add_invoice()">
            Add Invoice
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                <path d="M360-360v-170l367-367q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L530-360H360Zm424-368 57-56-56-56-57 56 56 56ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h357L280-563v283h282l278-278v358q0 33-23.5 56.5T760-120H200Z" />
            </svg>

        </button>
    </section>
    <!-- invoice History Table -->
    <section id="invoice-table"></section>

    <section id="invoice-modal"></section>

    <script>
        // Load invoice History Statistics Cards
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/invoice/components/cards.php' ?>',
            success: function(response) {
                $('#card-data').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading invoice History Statistics Card :', error);
            }
        });

        // Load invoice table
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/invoice/components/invoice_table.php' ?>',
            success: function(response) {
                $('#invoice-table').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading Out Of Stock Table :', error);
            }
        });
    </script>
<?php } ?>