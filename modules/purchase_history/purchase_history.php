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
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/badges.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/toast.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/confirmation popup.css' ?>">
    <link rel="stylesheet" href="<?= MODULES . '/purchase_history/css/purchase_history.css' ?>">
    <link href="<?= PACKAGES . '/datatables/datatables.min.css' ?>" rel="stylesheet">
</head>

<body>
    <header>

        <!-- Top Nav Bar -->
        <div id="topNavBar"></div>

        <!-- Side Nav Bar -->
        <div id="sideNavBar"></div>
    </header>
    <div id="toast-container"></div>
    <main id="purchase-order"></main>
    <section id="purchase-order-modal"></section>

    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <!-- Include global JavaScript -->
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>



    <script>
        <?php if (isset($_GET['type']) && $_GET['type'] == 'add') { ?>
            add_purchase_order();
        <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'view') { ?>
            view_purchase_order();
        <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'edit') { ?>
            edit_purchase_order();
        <?php } else { ?>
            purchase_history_home()
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

        function purchase_history_home() {
            //Load Purchase History Home Page
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/purchase_history/components/purchase_history_home.php?page_id=18&access=0' ?>',
                success: function(response) {
                    $('#purchase-order').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Purchase History Home Page:', error);
                }
            });
        }

        function add_purchase_order() {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/purchase_history/components/add_purchase_order.php?type=add&page_id=19&access=0' ?>',
                success: function(response) {
                    $('#purchase-order').html(response);
                    var newUrl = window.location.pathname + '?type=add';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Add purchase order popup:', error);
                }
            });
        }

        function edit_purchase_order(po_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/purchase_history/components/edit_purchase_order.php?type=edit&page_id=20&access=0' ?>',
                data: {
                    po_id: po_id
                },
                success: function(response) {
                    $('#purchase-order').html(response);
                    var newUrl = window.location.pathname + '?type=edit';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Edit purchase order popup:', error);
                }
            });
        }

        function view_purchase_order(po_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/purchase_history/components/view_purchase_order.php?type=view&page_id=21&access=0' ?>',
                data: {
                    po_id: po_id
                },
                success: function(response) {
                    $('#purchase-order').html(response);
                    var newUrl = window.location.pathname + '?type=view';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading View purchase order popup:', error);
                }
            });
        }

        function cancel_purchase_order(purchase_order_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/purchase_history/components/cancel_purchase_order_popup.php?type=cancel&page_id=20&access=1' ?>',
                data: {
                    purchase_order_id: purchase_order_id
                },
                success: function(response) {
                    $('#purchase-order-modal').html(response);
                    $('#confirmationPopup').css('display', 'block');
                    // Set URL query parameter ?type=cancel
                    var newUrl = window.location.pathname + '?type=cancel';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#purchase-order-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=cancel
                    });

                    // Close modal on cancel button click
                    $('#cancelCancel').on('click', function() {
                        $('#purchase-order-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=cancel
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('confirmationPopup')) {
                            $('#purchase-order-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=cancel
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading edit purchase_order Popup:', error);
                }
            });
        }
    </script>
</body>

</html>