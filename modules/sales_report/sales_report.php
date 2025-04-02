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
    <link rel="stylesheet" href="<?= MODULES . '/sales_report/css/sales_report.css' ?>">

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

        <!-- Sale Report Statistical Card Data -->
        <section id="card-data"></section>

        <!-- Sales Report Table -->
        <section id="sales-report-table"></section>

        <section id="sales-report-modal"></section>

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

            // Load sale report Statistics Cards
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/sales_report/components/cards.php' ?>',
                success: function(response) {
                    $('#card-data').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Sales Report Statistics Card :', error);
                }
            });

            // Load sales-report table
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/sales_report/components/sales_report_table.php' ?>',
                success: function(response) {
                    $('#sales-report-table').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Out Of Stock Table :', error);
                }
            });

            function view_sales_report(product_id) {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/sales_report/components/view_sales_report_popup.php?type=view&?page_id=34&access=1' ?>',
                    data: {
                        product_id: product_id
                    },
                    success: function(response) {
                        $('#sales-report-modal').html(response);
                        $('#myModal').css('display', 'block');

                        // Set URL query parameter ?type=view
                        var newUrl = window.location.pathname + '?type=view';
                        history.pushState({}, '', newUrl);

                        // Close modal on close button click
                        $('.close').on('click', function() {
                            salesChart = null;
                            $('#sales-report-modal').html("");
                            history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=view
                        });

                        // Close modal on outside click
                        $(window).on('click', function(event) {
                            if (event.target == document.getElementById('myModal')) {
                                salesChart = null;
                                $('#sales-report-modal').html("");
                                history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=view
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Add sales_report Popup:', error);
                    }
                });
            }
        </script>
    </main>
</body>

</html>