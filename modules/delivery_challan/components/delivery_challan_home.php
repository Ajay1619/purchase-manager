<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>

    <!-- Vendor Statistical Card Data -->
    <section id="card-data"></section>

    <!-- purchase History Table -->
    <section id="delivery-challan-table"></section>

    <section id="delivery-challan-modal"></section>

    <script>
        // Load Purchase History Statistics Cards
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/delivery_challan/components/cards.php' ?>',
            success: function(response) {
                $('#card-data').html(response);
            },
            error: function(xhr, status, error) {
                showToast('error', error);
            }
        });

        // Load delivery-challan table
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/delivery_challan/components/delivery_challan_table.php' ?>',
            success: function(response) {
                $('#delivery-challan-table').html(response);
            },
            error: function(xhr, status, error) {
                showToast('error', error);
            }
        });
    </script>
<?php } ?>