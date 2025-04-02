<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Total Roles</h3>
                <p class="statistic" id="total-roles-count"></p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#000000">
                <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h240l80 80h320q33 0 56.5 23.5T880-640v131q-35-25-76-38t-85-13q-118 0-198.5 82.5T440-281q0 32 7 62t21 59H160Zm400 0v-22q0-45 44-71.5T720-280q72 0 116 26.5t44 71.5v22H560Zm160-160q-33 0-56.5-23.5T640-400q0-33 23.5-56.5T720-480q33 0 56.5 23.5T800-400q0 33-23.5 56.5T720-320Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Active Roles</h3>
                <p class="statistic" id="total-active-roles-count"></p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#4CAF50">
                <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h240l80 80h320q33 0 56.5 23.5T880-640v131q-35-25-76-38t-85-13q-118 0-198.5 82.5T440-281q0 32 7 62t21 59H160Zm400 0v-22q0-45 44-71.5T720-280q72 0 116 26.5t44 71.5v22H560Zm160-160q-33 0-56.5-23.5T640-400q0-33 23.5-56.5T720-480q33 0 56.5 23.5T800-400q0 33-23.5 56.5T720-320Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Inactive Roles</h3>
                <p class="statistic" id="total-inactive-roles-count"></p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#FF5733">
                <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h240l80 80h320q33 0 56.5 23.5T880-640v131q-35-25-76-38t-85-13q-118 0-198.5 82.5T440-281q0 32 7 62t21 59H160Zm400 0v-22q0-45 44-71.5T720-280q72 0 116 26.5t44 71.5v22H560Zm160-160q-33 0-56.5-23.5T640-400q0-33 23.5-56.5T720-480q33 0 56.5 23.5T800-400q0 33-23.5 56.5T720-320Z" />
            </svg>
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/role_permission/ajax/fetch_role_permission_card_details.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const total_roles_count = response.total_roles_count;
                    const total_active_roles_count = response.total_active_roles_count;
                    const total_inactive_roles_count = response.total_inactive_roles_count;
                    // Populate modal with fetched purchase details
                    $('#total-roles-count').text(total_roles_count);
                    $('#total-active-roles-count').text(total_active_roles_count);
                    $('#total-inactive-roles-count').text(total_inactive_roles_count);

                } else {
                    alert('Failed to fetch roles data: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching roles data: ' + error);
            }
        });
    </script>
<?php } ?>