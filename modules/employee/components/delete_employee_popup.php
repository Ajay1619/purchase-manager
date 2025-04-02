<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'delete') {
        $employee_id = $_POST['employee_id'];
?>
        <!-- The Confirmation Popup -->
        <div id="confirmationPopup" class="confirmation-popup">
            <!-- Popup content -->
            <div class="popup-content">
                <div id="toast-container"></div>
                <span class="close">&times;</span>
                <h2 class="popup-title">Delete Employee</h2>
                <div class="popup-body">
                    <p>Are you sure you want to delete this employee?</p>
                    <div class="popup-footer">
                        <button id="confirmDelete" onclick="confirmDelete(<?= $employee_id ?>)" class="btn btn-danger">Confirm</button>
                        <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>



        <script>
            function confirmDelete(employee_id) {
                $.ajax({
                    url: '<?= MODULES . '/employee/ajax/delete_employee.php' ?>',
                    type: 'POST',
                    data: {
                        employee_id: employee_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showToast(response.status, response.message);
                            location.reload();
                        } else {
                            showToast(response.status, response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        </script>
<?php }
} ?>