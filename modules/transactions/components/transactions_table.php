<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <h2 id="title">Income History</h2>
        <div id="income-expense-dropdown">
            <select id="year-select">
                <option value="2024">2024</option>
                <option value="2023">2023</option>
                <option value="2022">2022</option>
            </select>
            <select id="income-expense-select">
                <option value="0">Income</option>
                <option value="1">Expense</option>
            </select>
        </div>

        <table class="styled-table" id="transactions-table-data">
            <thead>
                <tr>
                <tr>
                    <th>Months</th>
                    <th>Dates</th>
                    <th>Invoice Number</th>
                    <th>Subtotal (₹)</th>
                    <th>GST Amount (₹)</th>
                    <th>Grand Total (₹)</th>
                </tr>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script>
        function updateTable() {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/transactions/json/fetch_transactions_table.php' ?>',
                data: {
                    year: $('#year-select').val(),
                    type: $('#income-expense-select').val()
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        $('#transactions-table-data tbody').html(response.data);
                        $('#title').text($('#income-expense-select option:selected').text() + ' History');
                    } else {
                        alert('Failed to fetch transaction details: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error fetching transaction details: ' + error);
                }
            });
        }

        $(document).ready(function() {
            $('#year-select, #income-expense-select').change(updateTable);
            $('#title').text($('#income-expense-select option:selected').text() + ' History');
            // Initial table load
            updateTable();
        });
    </script>

<?php } ?>