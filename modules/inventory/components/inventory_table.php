<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <table class="styled-table" id="inventory-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Unit Of Measure</th>
                    <th>Quantity In Stock</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Include jQuery and DataTables -->
    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script>
        $(document).ready(function() {
            $('#inventory-table-data').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/inventory/json/inventory_table_data.php' ?>",
                    "type": "POST"
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
                        "data": "quantity_in_stock"
                    },
                    {
                        "data": "last_updated"
                    },
                    {
                        "data": "action",
                        "orderable": false,
                        "searchable": false
                    }
                ]
            });
        });
    </script>

<?php } ?>