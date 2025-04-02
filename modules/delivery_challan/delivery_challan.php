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
    <link rel="stylesheet" href="<?= MODULES . '/delivery_challan/css/delivery_challan.css' ?>">
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
    <main id="delivery-challan"></main>
    <section id="delivery-challan-modal"></section>

    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <!-- Include global JavaScript -->
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>



    <script>
        <?php if (isset($_GET['type']) && $_GET['type'] == 'view') { ?>
            view_delivery_challan();
        <?php } else { ?>
            delivery_challan_home()
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
                showToast('error', error);
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
                showToast('error', error);
            }
        });

        function delivery_challan_home() {
            //Load Purchase History Home Page
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/delivery_challan/components/delivery_challan_home.php?page_id=18&access=0' ?>',
                success: function(response) {
                    $('#delivery-challan').html(response);
                },
                error: function(xhr, status, error) {
                    showToast('error', error);
                }
            });
        }

        function view_challan(dc_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/delivery_challan/components/view_delivery_challan.php?type=view&page_id=21&access=0' ?>',
                data: {
                    dc_id: dc_id
                },
                success: function(response) {
                    $('#delivery-challan').html(response);
                    var newUrl = window.location.pathname + '?type=view';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    showToast('error', error);
                }
            });
        }
    </script>
</body>

</html>