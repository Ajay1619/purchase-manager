<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <table class="styled-table" id="out-of-stock-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Product Name</th>
                    <th>Unit Of Measure</th>
                    <th>Order Quantity</th>
                    <th>Vendors</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Include jQuery and DataTables -->
    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script>
        $(document).ready(function() {
            $('#out-of-stock-table-data').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/out_of_stock/json/out_of_stock_table_data.php' ?>",
                    "type": "POST"
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false,
                        "width": "5%" // Set the width for the S.No column
                    },
                    {
                        "data": "product_name",
                        "width": "20%" // Set the width for the Product Name column
                    },
                    {
                        "data": "unit_of_measure",
                        "orderable": false,
                        "searchable": false,
                        "width": "15%" // Set the width for the Unit of Measure column
                    },
                    {
                        "data": "order_quantity",
                        "orderable": false,
                        "searchable": false,
                        "width": "15%" // Set the width for the Order Quantity column
                    },
                    {
                        "data": "vendor_name",
                        "orderable": false,
                        "searchable": false,
                        "width": "25%" // Set the width for the Vendor Name column
                    },
                    {
                        "data": "action",
                        "orderable": false,
                        "searchable": false,
                        "width": "20%" // Set the width for the Action column
                    }
                ]
            });
        });
    </script>
<?php } ?>