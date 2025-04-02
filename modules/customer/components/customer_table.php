<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <table class="styled-table" id="customer-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Customer Code</th>
                    <th>Customer Name</th>
                    <th>Contact Number</th>
                    <th>Status</th>
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
            $('#customer-table-data').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/customer/json/customers_table_data.php' ?>",
                    "type": "POST"
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "customer_code"
                    },
                    {
                        "data": "customer_name"
                    },
                    {
                        "data": "contact_number"
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
        });
    </script>

<?php } ?>