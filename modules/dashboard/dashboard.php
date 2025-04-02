<?php

require_once('../../config/sparrow.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?= $currentLocation ?> </title>

    <!-- Include CSS -->
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/style.css' ?>">
    <link rel="stylesheet" href="<?= MODULES . '/dashboard/css/dashboard.css' ?>">
</head>

<body>
    <div id="topNavBar"></div>
    <div id="sideNavBar"></div>
    <div id="toast-container"></div>
    <main>
        <section id="top-section"></section>

        <section id="chart-container"></section>

        <section id="columns-charts"></section>

        <!-- invoice-purchase-cards -->
        <section id="invoice-purchase-cards"></section>

    </main>
    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>

    <!-- Include global JavaScript -->
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

    <script src="<?= PACKAGES . '/apexchart/apexchart.js' ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load Top Nav Bar
            $.ajax({
                type: 'GET',
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

            // Load Top Section
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/dashboard/components/top_section.php?page_id=2&access=1' ?>',
                success: function(response) {
                    $('#top-section').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Top Section:', error);
                }
            });

            // Load Middle Charts
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/dashboard/components/middle_charts.php?page_id=3&access=1' ?>',
                success: function(response) {
                    $('#chart-container').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Top Section:', error);
                }
            });

            // Load column Charts
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/dashboard/components/column_charts.php?page_id=4&access=1' ?>',
                success: function(response) {
                    $('#columns-charts').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Top Section:', error);
                }
            });

            // Load invoice purchase cards
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/dashboard/components/invoice_purchase_cards.php?page_id=5&access=1' ?>',
                success: function(response) {
                    $('#invoice-purchase-cards').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading Top Section:', error);
                }
            });

        });
    </script>
    <script>

    </script>
</body>

</html>