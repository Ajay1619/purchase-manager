<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <table class="styled-table" id="quotation-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Quotation Number</th>
                    <th>Date</th>
                    <th>Customer Name</th>
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
        $('#quotation-table-data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= MODULES . '/quotation/json/quotation_table_data.php' ?>",
                "type": "POST",
            },
            "columns": [{
                    "data": "s_no",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "quotation_number"
                },
                {
                    "data": "quotation_date"
                },
                {
                    "data": "customer_name"
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