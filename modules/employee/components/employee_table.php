<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">
        <table class="styled-table" id="employee-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Employee Name</th>
                    <th>Employee ID</th>
                    <th>Contact Number</th>
                    <th>Designation</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Include jQuery and DataTables library -->
    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script>
        $('#employee-table-data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= MODULES . '/employee/json/employee_table_data.php' ?>", // Update URL for employee data
                "type": "POST",
            },
            "columns": [{
                    "data": "s_no",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "employee_name"
                },
                {
                    "data": "employee_id"
                },
                {
                    "data": "contact_number"
                },
                {
                    "data": "designation"
                },
                {
                    "data": "role"
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