<?php
echo $_GET['page_id'];
require_once('../../config/sparrow.php');
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
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/confirmation popup.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/toast.css' ?>">
    <link rel="stylesheet" href="<?= MODULES . '/inventory/css/inventory.css' ?>">
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
        <!-- bread Crumb -->
        <section id="breadcrumb"></section>

        <!-- Products Statistical Card Data -->
        <section id="card-data"></section>

        <!-- Add Inventory Button -->
        <section id="button-container">
            <button id="popupButton" onclick="add_item()">
                Add Item
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                    <path d="M200-120q-33 0-56.5-23.5T120-200v-499q0-14 4.5-27t13.5-24l50-61q11-14 27.5-21.5T250-840h460q18 0 34.5 7.5T772-811l50 61q9 11 13.5 24t4.5 27v196q-19-7-39-11.5t-41-4.5q-33 0-63.5 7.5T640-488v-152H320v320l160-80 58 29q-8 21-13 43.5t-5 46.5q0 45 16 86.5t46 74.5H200Zm520 0v-120H600v-80h120v-120h80v120h120v80H800v120h-80ZM216-720h528l-34-40H250l-34 40Z" />
                </svg>
            </button>
        </section>

        <!-- Inventory Table -->
        <section id="inventory-table"></section>

        <section id="inventory-modal"></section>
    </main>

    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <!-- Include global JavaScript -->
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
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

        // Load BreadCrumb
        $.ajax({
            type: 'POST',
            url: '<?= GLOBAL_PATH . '/components/breadcrumb.php' ?>',
            data: {
                getRewrittenUrl: '<?= $getRewrittenUrl ?>'
            },
            success: function(response) {
                $('#breadcrumb').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading side navbar:', error);
            }
        });

        // Load Inventory Statistics Cards
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/inventory/components/cards.php' ?>',
            success: function(response) {
                $('#card-data').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading Inventory Statistics Card :', error);
            }
        });

        // Load Products table
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/inventory/components/inventory_table.php' ?>',
            success: function(response) {
                $('#inventory-table').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading Inventory Table :', error);
            }
        });

        function add_item() {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/inventory/components/add_item_popup.php?type=add&page_id=11&access=1' ?>',
                success: function(response) {
                    $('#inventory-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=add
                    var newUrl = window.location.pathname + '?type=add';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#inventory-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=add
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#inventory-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=add
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Add Product Popup:', error);
                }
            });
        }

        function view_inventory(inventory_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/inventory/components/view_inventory_popup.php?type=view&page_id=12&access=1' ?>',
                data: {
                    inventory_id: inventory_id
                },
                success: function(response) {
                    $('#inventory-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=view
                    var newUrl = window.location.pathname + '?type=view';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        chart = null;
                        $('#inventory-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=view
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            chart = null;
                            $('#inventory-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=view
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Add Inventory Popup:', error);
                }
            });
        }

        function update_inventory(inventory_id, product_id, product_name, unit_of_measure, quantity_in_stock) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/inventory/components/update_inventory_popup.php?type=edit&page_id=13&access=1' ?>',
                data: {
                    inventory_id: inventory_id,
                    product_id: product_id,
                    product_name: product_name,
                    unit_of_measure: unit_of_measure,
                    quantity_in_stock: quantity_in_stock
                },
                success: function(response) {
                    $('#inventory-modal').html(response);
                    $('#myModal').css('display', 'block');
                    // Set URL query parameter ?type=edit
                    var newUrl = window.location.pathname + '?type=edit';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#inventory-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=edit
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('myModal')) {
                            $('#inventory-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=edit
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Add Inventory Popup:', error);
                }
            });
        }

        function searchProductName(products) {
            const searchInput = document.getElementById(`product-name`);
            const resultsList = document.getElementById(`results`);
            const itemId = document.getElementById(`product-id`);
            const unitOfQuantity = document.getElementById(`unit-of-quantity`);
            const productUnitOfMeasure = document.getElementById(`product-unit-of-measure`);
            const searches = products;

            resultsList.innerHTML = '';

            searches.forEach(result => {
                const li = document.createElement('li');
                li.innerHTML = `<span class="list-name">${result.product_name}</span> <span class="list-code">${result.product_code}</span>`;
                resultsList.appendChild(li);

                li.addEventListener('click', function() {
                    searchInput.value = result.product_name;
                    itemId.value = result.product_id;
                    unitOfQuantity.value = result.unit_of_measure;
                    productUnitOfMeasure.value = result.unit_of_measure;
                    resultsList.style.display = 'none';
                    $('#unit-of-quantity').removeAttr('disabled');
                });
            });

            resultsList.style.display = searches.length > 0 ? 'block' : 'none';
        }
    </script>
</body>

</html>