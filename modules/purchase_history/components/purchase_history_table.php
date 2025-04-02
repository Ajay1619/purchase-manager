<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <table class="styled-table" id="order-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Order Number</th>
                    <th>Date</th>
                    <th>Vendor Company Name</th>
                    <th>Total Amount <br> ( <?= CURRENCY_SYMBOL ?> )</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script>
        $('#order-table-data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= MODULES . '/purchase_history/json/orders_table_data.php' ?>",
                "type": "POST",
            },
            "columns": [{
                    "data": "s_no",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "purchase_order_number"
                },
                {
                    "data": "purchase_order_date"
                },
                {
                    "data": "vendor_company_name"
                },
                {
                    "data": "grand_total"
                },
                {
                    "data": "status",
                    "searchable": false
                },
                {
                    "data": "action",
                    "orderable": false,
                    "searchable": false
                }
            ]
        });
    </script>
<?php } ?>