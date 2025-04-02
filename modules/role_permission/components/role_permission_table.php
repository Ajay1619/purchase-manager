<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="table-wrapper">

        <div id="toast-container"></div>
        <table class="styled-table" id="role-table-data">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Role Name</th>
                    <th>Role Code</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>

    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script>
        $('#role-table-data').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= MODULES . '/role_permission/json/role_permission_table_data.php' ?>",
                "type": "POST",
            },
            "columns": [{
                    "data": "s_no",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "role_name"
                },
                {
                    "data": "role_code"
                },
                {
                    "data": "status"
                },
                {
                    "data": "action",
                    "orderable": false,
                    "searchable": false,
                }
            ]
        });
    </script>

<?php } ?>