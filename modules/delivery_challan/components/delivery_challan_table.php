<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <table class="styled-table" id="delivery-challan-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Challan Number</th>
                    <th>Challan Date</th>
                    <th>Customer Name</th>
                    <th>Delivery Date</th>
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
        $('#delivery-challan-table-data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= MODULES . '/delivery_challan/json/delivery_challan_table_data.php' ?>", // Update URL for delivery challan data
                "type": "POST",
            },
            "columns": [{
                    "data": "s_no",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "delivery_challan_number"
                }, // Updated column name
                {
                    "data": "delivery_challan_date"
                }, // Updated column name
                {
                    "data": "customer_name"
                }, // Fetch customer name
                {
                    "data": "delivery_date"
                }, // Delivery date
                {
                    "data": "action",
                    "orderable": false,
                    "searchable": false
                }
            ]
        });
    </script>
<?php } ?>