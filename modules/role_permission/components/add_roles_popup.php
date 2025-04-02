<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'add') {
        // Your existing HTML modal code here
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">Add Role Form</h2>
                <div class="modal-body">
                    <form id="add-role-form">
                        <div class="section">
                            <h2 class="section-title">Role Details</h2>
                            <div class="flex-container">
                                <div class="flex-row">
                                    <div class="flex-column">
                                        <label for="role-name">Role Name:</label>
                                        <input type="text" id="role-name" name="role-name" placeholder="Enter role name" required>
                                    </div>
                                    <div class="flex-column">
                                        <label for="role-code">Role Code:</label>
                                        <input type="text" id="role-code" name="role-code" placeholder="Role-Code" readonly>
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
                url: '<?= MODULES . '/role_permission/ajax/fetch_pages_with_pre_role_code.php' ?>',
                type: 'POST',
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === 'success') {

                        $('#role-code').val(response.new_role_code);

                        // Populate pages dynamically
                        const pagesContainer = $('#pages-container');
                        const pages = response.pages;

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
                                const subPageElement = $(`
                            <label class="sub-page">
                                <input type="checkbox" id="subpage-${page.page_id}" class="sub-page-checkbox" name="page[]" value="${page.page_id}">
                                <span class="checkboxes"></span>${page.page_name}
                            </label>
                        `);
                                currentModule.find('.sub-pages').append(subPageElement);
                            }
                        });
                    }
                },
                error: function() {
                    $('#response').text('An error occurred');
                }
            });

            $('#add-role-form').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '<?= MODULES . '/role_permission/ajax/add_role.php' ?>',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            showToast('success', 'Role added successfully');
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