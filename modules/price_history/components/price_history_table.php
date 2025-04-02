<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <table class="styled-table" id="price-history-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Product Name</th>
                    <th>Unit Of Measure</th>
                    <th>Current Price <?= CURRENCY_SYMBOL ?></th>
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
            $('#price-history-table-data').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/price_history/json/price_history_table_data.php' ?>",
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
                        "data": "unit_of_measure"
                    },
                    {
                        "data": "current_price"
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