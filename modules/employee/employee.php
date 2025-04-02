<?php require_once('../../config/sparrow.php');

?>
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
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/toast.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/confirmation popup.css' ?>">
    <link rel="stylesheet" href="<?= MODULES . '/employee/css/employee.css' ?>">
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

        <div id="toast-container"></div>
        <!-- employee Statistical Card Data -->
        <section id="card-data"></section>

        <!-- Add account Button -->
        <section id="button-container">
            <button id="popupButton" onclick="add_employee()">
                Add Employee
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                    <path d="M720-400v-120H600v-80h120v-120h80v120h120v80H800v120h-80Zm-360-80q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Z" />
                </svg>
            </button>
        </section>

        <!-- account Table -->
        <section id="employee-table"></section>

        <section id="employee-modal"></section>
    </main>

    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>


    <!-- Include global JavaScript -->
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

    <script>
        <?php if (isset($_GET['type']) && $_GET['type'] == 'add') { ?>
            add_employee();
        <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'view') { ?>
            view_employee();
        <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'edit') { ?>
            edit_employee();
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
                initializeSidebar();
            },
            error: function(xhr, status, error) {
                console.error('Error loading side navbar:', error);
            }
        });

        // Load employee Statistics Cards
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/employee/components/cards.php' ?>',
            success: function(response) {
                $('#card-data').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading employee Statistics Card :', error);
            }
        });

        // Load employee table
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/employee/components/employee_table.php' ?>',
            success: function(response) {
                $('#employee-table').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading employee Statistics Card :', error);
            }
        });



        // Open modal on button click
        function add_employee() {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/employee/components/add_employee_popup.php?type=add' ?>',
                success: function(response) {
                    $('#employee-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=add
                    var newUrl = window.location.pathname + '?type=add';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#employee-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=add
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#employee-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=add
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Add account Popup:', error);
                }
            });
        }

        function view_employee(account_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/employee/components/view_employee_popup.php?type=view' ?>',
                data: {
                    employee_id: account_id
                },
                success: function(response) {
                    $('#employee-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=view
                    var newUrl = window.location.pathname + '?type=view';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#employee-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=view
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#employee-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=view
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Add account Popup:', error);
                }
            });
        }

        function edit_employee(account_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/employee/components/edit_employee_popup.php?type=edit' ?>',
                data: {
                    employee_id: account_id
                },
                success: function(response) {
                    $('#employee-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=edit
                    var newUrl = window.location.pathname + '?type=edit';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#employee-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=edit
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#employee-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=edit
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading edit account Popup:', error);
                }
            });
        }

        function delete_employee(account_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/employee/components/delete_employee_popup.php?type=delete' ?>',
                data: {
                    employee_id: account_id
                },
                success: function(response) {
                    $('#employee-modal').html(response);
                    $('#confirmationPopup').css('display', 'block');
                    // Set URL query parameter ?type=delete
                    var newUrl = window.location.pathname + '?type=delete';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#employee-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=delete
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#employee-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=delete
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading delete account Popup:', error);
                }
            });
        }

        function checkStatus(employee_id, status) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/employee/ajax/update_employee_status.php?type=check_status' ?>',
                data: {
                    employee_id: employee_id,
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
                    console.error('Error loading edit Product Popup:', error);
                }
            });
        }

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('logoPreview');
            $('#previous-logo').val("");
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block'; // Show the image
                };

                reader.readAsDataURL(input.files[0]); // Convert the file to a base64 string
            }
        }
    </script>

</body>

</html>