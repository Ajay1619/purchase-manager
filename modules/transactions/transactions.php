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
    <link rel="stylesheet" href="<?= MODULES . '/transactions/css/transactions.css' ?>">
</head>

<body>
    <header>
        <!-- Top Nav Bar -->
        <div id="topNavBar"></div>

        <!-- Side Nav Bar -->
        <div id="sideNavBar"></div>
    </header>
    <main>

        <!-- Transactions Statistical Card Data -->
        <section id="card-data"></section>
        <!-- Transaction Chart -->
        <section id="transaction-chart"></section>
        <!-- Transactions Table -->
        <section id="transactions-table"></section>

        <section id="transactions-modal"></section>

        <!-- Include jQuery -->
        <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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

            // Load Transactions Statistics Cards
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/transactions/components/cards.php' ?>',
                success: function(response) {
                    $('#card-data').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Sales Report Statistics Card :', error);
                }
            });

            // Load Transactions chart
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/transactions/components/transaction_chart.php?page_id=31&access=1' ?>',
                success: function(response) {
                    $('#transaction-chart').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Sales Report Statistics Card :', error);
                }
            });


            // Load transactions table
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/transactions/components/transactions_table.php?page_id=32&access=1' ?>',
                success: function(response) {
                    $('#transactions-table').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Out Of Stock Table :', error);
                }
            });
        </script>
    </main>
</body>

</html>