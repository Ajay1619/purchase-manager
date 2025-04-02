<?php require_once('../../config/sparrow.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $currentLocation ?></title>

    <!-- Include CSS -->

    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/style.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/cards.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/buttons.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/table.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/input fields.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/modal.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/confirmation popup.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/badges.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/toast.css' ?>">
    <link rel="stylesheet" href="<?= MODULES . '/role_permission/css/role_permission.css' ?>">
    <link href="<?= PACKAGES . '/datatables/datatables.min.css' ?>" rel="stylesheet">
</head>

<body>
    <header>
        <!-- Top Nav Bar -->
        <div id="topNavBar"></div>

        <!-- Side Nav Bar -->
        <div id="sideNavBar"></div>
    </header>

    <main>


        <!-- Roles Statistical Card Data -->
        <section id="card-data"></section>

        <!-- Add role Button -->
        <section id="button-container">
            <button id="popupButton" onclick="add_role()">
                Add Role
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                    <path d="M680-119q-8 0-16-2t-15-7l-120-70q-14-8-21.5-21.5T500-249v-141q0-16 7.5-29.5T529-441l120-70q7-5 15-7t16-2q8 0 15.5 2.5T710-511l120 70q14 8 22 21.5t8 29.5v141q0 16-8 29.5T830-198l-120 70q-7 4-14.5 6.5T680-119ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18h14q6 0 12 2-29 72-24 143t48 135H80Zm320-320q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Zm186 74 94 55 94-55-94-54-94 54Zm124 208 90-52v-110l-90 53v109Zm-150-52 90 53v-109l-90-53v109Z" />
                </svg>
            </button>
        </section>

        <!-- role Table -->
        <section id="role-table"></section>

        <section id="role-modal"></section>

    </main>

    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>


    <!-- Include global JavaScript -->
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_GET['type']) && $_GET['type'] == 'add') { ?>
                add_role();
            <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'view') { ?>
                view_role();
            <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'edit') { ?>
                edit_role();
            <?php } ?>
            // Load Top Nav Bar
            $.ajax({
                type: 'POST',
                url: '<?= GLOBAL_PATH . '/components/topnavbar.php' ?>',

                success: function(response) {
                    $('#topNavBar').html(response);
                    initializeTopbar();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading top navbar:', error);
                }
            });

            // Load Side Nav Bar
            $.ajax({
                type: 'POST',
                url: '<?= GLOBAL_PATH . '/components/sidenavbar.php' ?>',
                data: {
                    getRewrittenUrl: '<?= $getRewrittenUrl ?>'
                },
                success: function(response) {
                    $('#sideNavBar').html(response);

                    // After loading sidenavbar.php content, initialize sidebar functionality
                    initializeSidebar();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading side navbar:', error);
                }
            });

            // Load role_permission Statistics Cards
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/role_permission/components/cards.php' ?>',
                success: function(response) {
                    $('#card-data').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading role_permission Statistics Card :', error);
                }
            });

            // Load role_permission table
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/role_permission/components/role_permission_table.php' ?>',
                success: function(response) {
                    $('#role-table').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading role_permission Statistics Card :', error);
                }
            });


        });

        // Open modal on button click
        function add_role() {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/role_permission/components/add_roles_popup.php?type=add' ?>',
                success: function(response) {
                    $('#role-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=add
                    var newUrl = window.location.pathname + '?type=add';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#role-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=add
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#role-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=add
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Add role_permission Popup:', error);
                }
            });
        }

        function edit_role(role_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/role_permission/components/edit_roles_popup.php?type=edit' ?>',
                data: {
                    role_id: role_id
                },
                success: function(response) {
                    $('#role-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=edit
                    var newUrl = window.location.pathname + '?type=edit';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#role-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=edit
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#role-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=edit
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading edit role_permission Popup:', error);
                }
            });
        }

        function delete_role(role_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/role_permission/components/delete_role_popup.php?type=delete' ?>',
                data: {
                    role_id: role_id
                },
                success: function(response) {
                    $('#role-modal').html(response);
                    $('#confirmationPopup').css('display', 'block');
                    // Set URL query parameter ?type=delete
                    var newUrl = window.location.pathname + '?type=delete';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#role-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=delete
                    });

                    // Close modal on cancel button click
                    $('#cancelDelete').on('click', function() {
                        $('#role-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=delete
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('confirmationPopup')) {
                            $('#role-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=delete
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading edit role Popup:', error);
                }
            });
        }

        function checkStatus(role_id, status) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/role_permission/ajax/update_role_status.php?type=check_status' ?>',
                data: {
                    role_id: role_id,
                    status: status
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        showToast('success', response.message);
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading edit role Popup:', error);
                }
            });
        }
    </script>
</body>

</html>