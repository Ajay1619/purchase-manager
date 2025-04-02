<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <table class="styled-table" id="sales-report-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Unit Of Measure</th>
                    <th>Total Bills</th>
                    <th>Total Quantity Sold</th>
                    <th>Revenue</th>
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
        $('#sales-report-table-data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= MODULES . '/sales_report/json/sales_report_table_data.php' ?>",
                "type": "POST",
            },
            "columns": [{
                    "data": "s_no",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "product_name"
                },
                {
                    "data": "product_code"
                },
                {
                    "data": "unit_of_measure"
                },
                {
                    "data": "total_bills"
                },
                {
                    "data": "total_quantity_sold"
                },
                {
                    "data": "revenue"
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