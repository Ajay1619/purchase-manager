<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'delete') {
        $role_id = $_POST['role_id'];
?>
        <!-- The Confirmation Popup -->
        <div id="confirmationPopup" class="confirmation-popup">
            <div id="toast-container"></div>
            <!-- Popup content -->
            <div class="popup-content">
                <span class="close">&times;</span>
                <h2 class="popup-title">Delete Role</h2>
                <div class="popup-body">
                    <p>Are you sure you want to delete this Role?</p>
                    <div class="popup-footer">
                        <button id="confirmDelete" onclick="confirmDelete(<?= $role_id ?>)" class="btn btn-danger">Confirm</button>
                        <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>



        <script>
            function confirmDelete(role_id) {
                $.ajax({
                    url: '<?= MODULES . '/role_permission/ajax/delete_role.php' ?>',
                    type: 'POST',
                    data: {
                        role_id: role_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            showToast('success', response.message);
                            var newUrl = window.location.pathname;
                            history.pushState({}, '', newUrl);
                            location.reload();
                        } else {
                            showToast('error', response.message);
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