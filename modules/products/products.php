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
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/toast.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/confirmation popup.css' ?>">
    <link rel="stylesheet" href="<?= MODULES . '/products/css/products.css' ?>">

    <link href="<?= PACKAGES . '/datatables/datatables.min.css' ?>" rel="stylesheet">
</head>


<body>

    <div id="toast-container"></div>
    <header>
        <!-- Top Nav Bar -->
        <div id="topNavBar"></div>

        <!-- Side Nav Bar -->
        <div id="sideNavBar"></div>
    </header>

    <main>


        <!-- Products Statistical Card Data -->
        <section id="card-data"></section>

        <!-- Add Product Button -->
        <section id="button-container">
            <button id="popupButton" onclick="add_product()">
                Add Product
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                    <path d="M440-600v-120H320v-80h120v-120h80v120h120v80H520v120h-80ZM280-80q-33 0-56.5-23.5T200-160q0-33 23.5-56.5T280-240q33 0 56.5 23.5T360-160q0 33-23.5 56.5T280-80Zm400 0q-33 0-56.5-23.5T600-160q0-33 23.5-56.5T680-240q33 0 56.5 23.5T760-160q0 33-23.5 56.5T680-80ZM40-800v-80h131l170 360h280l156-280h91L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68.5-39t-1.5-79l54-98-144-304H40Z" />
                </svg>
            </button>
        </section>

        <!-- Product Table -->
        <section id="product-table"></section>

        <section id="product-modal"></section>

    </main>

    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>


    <!-- Include global JavaScript -->
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

    <script src="<?= MODULES . '/products/js/add_item.js' ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_GET['type']) && $_GET['type'] == 'add') { ?>
                add_product();
            <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'view') { ?>
                view_product();
            <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'edit') { ?>
                edit_product();
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

            // Load Products Statistics Cards
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/products/components/cards.php' ?>',
                success: function(response) {
                    $('#card-data').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Products Statistics Card :', error);
                }
            });

            // Load Products table
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/products/components/products_table.php' ?>',
                success: function(response) {
                    $('#product-table').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Products Statistics Card :', error);
                }
            });


        });

        // Open modal on button click
        function add_product() {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/products/components/add_product_popup.php?type=add&page_id=23&access=1' ?>',
                success: function(response) {
                    $('#product-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=add
                    var newUrl = window.location.pathname + '?type=add';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#product-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=add
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#product-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=add
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Add Product Popup:', error);
                }
            });
        }

        function view_product(product_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/products/components/view_product_popup.php?type=view&page_id=24&access=1' ?>',
                data: {
                    product_id: product_id
                },
                success: function(response) {
                    $('#product-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=view
                    var newUrl = window.location.pathname + '?type=view';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#product-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=view
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#product-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=view
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Add Product Popup:', error);
                }
            });
        }

        function edit_product(product_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/products/components/update_product_popup.php?type=edit&page_id=25&access=1' ?>',
                data: {
                    product_id: product_id
                },
                success: function(response) {
                    $('#product-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=edit
                    var newUrl = window.location.pathname + '?type=edit';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#product-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=edit
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#product-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=edit
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading edit Product Popup:', error);
                }
            });
        }

        function delete_product(product_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/products/components/delete_product_popup.php?type=delete&page_id=24&access=1' ?>',
                data: {
                    product_id: product_id
                },
                success: function(response) {
                    $('#product-modal').html(response);
                    $('#confirmationPopup').css('display', 'block');
                    // Set URL query parameter ?type=delete
                    var newUrl = window.location.pathname + '?type=delete';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#product-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=delete
                    });

                    // Close modal on cancel button click
                    $('#cancelDelete').on('click', function() {
                        $('#product-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=delete
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('confirmationPopup')) {
                            $('#product-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=delete
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading edit Product Popup:', error);
                }
            });
        }

        function checkStatus(product_id, status) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/products/ajax/update_product_status.php?type=check_status&page_id=24&access=1' ?>',
                data: {
                    product_id: product_id,
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
    </script>
</body>

</html>