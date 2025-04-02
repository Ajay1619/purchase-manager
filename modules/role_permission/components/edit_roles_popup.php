<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'edit') {
        $role_id = isset($_POST['role_id']) ? sanitizeInput($_POST['role_id'], 'int')  : 0;
?>

        <!-- The Modal -->
        <div id="myModal" class="modal">
            <div id="toast-container"></div>
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">Edit Role Form</h2>
                <div class="modal-body">
                    <form id="edit-role-form">
                        <div class="section">
                            <h2 class="section-title">Role Details</h2>

                            <div class="flex-container">
                                <div class="flex-row">
                                    <div class="flex-column">
                                        <label for="role-name">Role Name:</label>
                                        <input type="text" id="role-name" name="role-name" placeholder="Enter role name" required>
                                        <input type="hidden" name="role-id" id="role-id">
                                    </div>
                                    <div class="flex-column">
                                        <label for="role-code">Role Code:</label>
                                        <input type="text" id="role-code" name="role-code" placeholder="Auto-generated" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section">
                            <h3 class="section-title">Allowed Access</h3>
                            <div class="container" id="pages-container"></div>
                            <input type="submit" value="Submit" class="button submit-button" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/role_permission/json/fetch_roles_details.php?' ?>',
                data: {
                    role_id: <?= $role_id ?>
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status == 'success') {

                        const role_details = data.role_details;
                        $('#role-name').val(role_details.role_name);
                        $('#role-code').val(role_details.role_code);
                        $('#role-id').val(<?= $role_id ?>);
                        // Extract role pages from the response
                        const pages = data.pages_details;
                        const userRoleDetails = data.user_role_details; // Array of pages with role_id

                        // Create a map of page_id for easy lookup
                        const userRolePageIds = new Set(userRoleDetails.map(page => page.page_id));

                        // Populate pages dynamically
                        const pagesContainer = $('#pages-container');
                        let currentModule = null;

                        pages.forEach(page => {
                            if (page.page_type === 0) {
                                // Create a new module
                                currentModule = $(`
                                    <div class="module">
                                        <div class="module-header">
                                            <h3 class="module-title">${page.page_name}</h3>
                                            <label class="module-page">
                                                <input type="checkbox" id="module-${page.page_id}" class="module-checkbox" name="page[]" value="${page.page_id}">
                                                <span class="checkboxes"></span>
                                            </label>
                                        </div>
                                        <div class="sub-pages">
                                        </div>
                                    </div>
                                `);
                                pagesContainer.append(currentModule);

                                // Add event listener to toggle subpages
                                $('#module-' + page.page_id).on('change', function() {
                                    const isChecked = $(this).is(':checked');
                                    // Toggle all subpage checkboxes based on module checkbox state
                                    $(this).closest('.module').find('.sub-page-checkbox').each(function() {
                                        $(this).prop('checked', isChecked);
                                    });
                                });

                            } else if (page.page_type === 1 && currentModule) {
                                // Add subpage to the current module
                                const isChecked = userRolePageIds.has(page.page_id);
                                const subPageElement = $(`
                                    <label class="sub-page">
                                        <input type="checkbox" id="subpage-${page.page_id}" class="sub-page-checkbox" name="page[]" value="${page.page_id}" ${isChecked ? 'checked' : ''}>
                                        <span class="checkboxes"></span>${page.page_name}
                                    </label>
                                `);
                                currentModule.find('.sub-pages').append(subPageElement);
                            }
                        });

                        // Update module checkboxes based on subpage checkboxes
                        $('.module-checkbox').each(function() {
                            const moduleId = $(this).attr('id').replace('module-', '');
                            const isChecked = $(this).closest('.module').find('.sub-page-checkbox:checked').length > 0;
                            $(this).prop('checked', isChecked);
                        });

                    } else {
                        console.error('Error fetching role details:', data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading edit role_permission Popup:', error);
                }
            });

            $('#edit-role-form').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '<?= MODULES . '/role_permission/ajax/edit_role.php' ?>',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            showToast('success', response.message);
                            var newUrl = window.location.pathname;
                            history.pushState({}, '', newUrl);
                            location.reload();
                        } else {
                            $('#response').text('An error occurred');
                        }
                    },
                    error: function() {
                        $('#response').text('An error occurred');
                    }
                });
            });
        </script>

<?php }
} ?>