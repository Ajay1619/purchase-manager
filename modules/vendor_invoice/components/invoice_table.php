<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <table class="styled-table" id="invoice-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Invoice Number</th>
                    <th>Date</th>
                    <th>Vendor Name</th>
                    <th>Status</th>
                    <th>Total Amount <br> ( <?= CURRENCY_SYMBOL ?> )</th>
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
        $('#invoice-table-data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= MODULES . '/vendor_invoice/json/invoice_table_data.php' ?>",
                "type": "POST",
            },
            "columns": [{
                    "data": "s_no",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "vendor_invoice_number"
                },
                {
                    "data": "invoice_date"
                },
                {
                    "data": "vendor_company_name"
                },
                {
                    "data": "status",
                    "searchable": false
                },
                {
                    "data": "total_amount"
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