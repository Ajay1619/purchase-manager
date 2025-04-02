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
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/confirmation popup.css' ?>">
    <link rel="stylesheet" href="<?= MODULES . '/out_of_stock/css/out_of_stock.css' ?>">

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
        <!-- Products Statistical Card Data -->
        <section id="card-data"></section>


        <!-- Product Table -->
        <section id="out-of-stock-table"></section>

        <section id="out-of-stock-modal"></section>

        <!-- Include jQuery -->
        <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
        <!-- Include global JavaScript -->
        <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

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

            // Load Products Statistics Cards
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/out_of_stock/components/cards.php' ?>',
                success: function(response) {
                    $('#card-data').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Out Of Stock Statistics Card :', error);
                }
            });

            // Load out-of-stock table
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/out_of_stock/components/out_of_stock_table.php' ?>',
                success: function(response) {
                    $('#out-of-stock-table').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Out Of Stock Table :', error);
                }
            });

            function restock_product(count, out_of_stock_id) {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/out_of_stock/components/confirm_restock_popup.php?type=confirm&page_id=15&access=1' ?>',
                    data: {
                        order_id: $(`#vendor_name_${count}`).val(),
                        out_of_stock_id: out_of_stock_id
                    },
                    success: function(response) {
                        $('#out-of-stock-modal').html(response);
                        $('#confirmationPopup').css('display', 'block');
                        // Set URL query parameter ?type=cancel
                        var newUrl = window.location.pathname + '?type=cancel';
                        history.pushState({}, '', newUrl);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Out Of Stock Modal :', error);
                    }
                });
            }

            function delete_out_of_stock(out_of_stock_id) {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/out_of_stock/components/delete_restock_popup.php?type=confirm&page_id=15&access=1' ?>',
                    data: {
                        out_of_stock_id: out_of_stock_id
                    },
                    success: function(response) {
                        $('#out-of-stock-modal').html(response);
                        $('#confirmationPopup').css('display', 'block');
                        // Set URL query parameter ?type=cancel
                        var newUrl = window.location.pathname + '?type=cancel';
                        history.pushState({}, '', newUrl);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Out Of Stock Modal :', error);
                    }
                });
            }
        </script>
    </main>
</body>

</html>