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
    <link rel="stylesheet" href="<?= MODULES . '/quotation/css/quotation.css' ?>">
    <link href="<?= PACKAGES . '/datatables/datatables.min.css' ?>" rel="stylesheet">
</head>

<body>
    <header>

        <!-- Top Nav Bar -->
        <div id="topNavBar"></div>

        <!-- Side Nav Bar -->
        <div id="sideNavBar"></div>
    </header>

    <main id="quotation"></main>
    <section id="quotation-modal"></section>

    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <!-- Include global JavaScript -->
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>



    <script>
        <?php if (isset($_GET['type']) && $_GET['type'] == 'add') { ?>
            add_quotation();
        <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'view') { ?>
            view_quotation();
        <?php } elseif (isset($_GET['type']) && $_GET['type'] == 'edit') { ?>
            edit_quotation();
        <?php } else { ?>
            quotation_home()
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

        function quotation_home() {
            //Load Purchase History Home Page
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/quotation/components/quotation_home.php?page_id=18&access=0' ?>',
                success: function(response) {
                    $('#quotation').html(response);
                },
                error: function(xhr, status, error) {
                    showToast('error', error);
                }
            });
        }

        function add_quotation() {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/quotation/components/add_quotation.php?type=add&page_id=19&access=0' ?>',
                success: function(response) {
                    $('#quotation').html(response);
                    var newUrl = window.location.pathname + '?type=add';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    showToast('error', error);
                }
            });
        }

        function edit_quotation(qo_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/quotation/components/edit_quotation.php?type=edit&page_id=20&access=0' ?>',
                data: {
                    qo_id: qo_id
                },
                success: function(response) {
                    $('#quotation').html(response);
                    var newUrl = window.location.pathname + '?type=edit';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    showToast('error', error);
                }
            });
        }

        function view_quotation(qo_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/quotation/components/view_quotation.php?type=view&page_id=21&access=0' ?>',
                data: {
                    qo_id: qo_id
                },
                success: function(response) {
                    $('#quotation').html(response);
                    var newUrl = window.location.pathname + '?type=view';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    showToast('error', error);
                }
            });
        }

        function cancel_quotation(quotation_id) {
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/quotation/components/cancel_quotation_popup.php?type=cancel&page_id=20&access=1' ?>',
                data: {
                    quotation_id: quotation_id
                },
                success: function(response) {
                    $('#quotation-modal').html(response);
                    $('#confirmationPopup').css('display', 'block');
                    // Set URL query parameter ?type=cancel
                    var newUrl = window.location.pathname + '?type=cancel';
                    history.pushState({}, '', newUrl);

                    // Close modal on close button click
                    $('.close').on('click', function() {
                        $('#quotation-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=cancel
                    });

                    // Close modal on cancel button click
                    $('#cancelCancel').on('click', function() {
                        $('#quotation-modal').html("");
                        history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=cancel
                    });

                    // Close modal on outside click
                    $(window).on('click', function(event) {
                        if (event.target == document.getElementById('confirmationPopup')) {
                            $('#quotation-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=cancel
                        }
                    });
                },
                error: function(xhr, status, error) {
                    showToast('error', error);
                }
            });
        }
    </script>
</body>

</html>