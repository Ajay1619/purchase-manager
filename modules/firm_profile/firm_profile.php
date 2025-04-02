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
    <link rel="stylesheet" href="<?= MODULES . '/firm_profile/css/firm_profile.css' ?>">
</head>

<body>
    <header>
        <!-- Top Nav Bar -->
        <div id="topNavBar"></div>

        <!-- Side Nav Bar -->
        <div id="sideNavBar"></div>
    </header>

    <div id="toast-container"></div>
    <main id="firm_profile"></main>

    <!-- Include jQuery -->
    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <!-- Include global JavaScript -->
    <script src="<?= GLOBAL_PATH . '/js/global.js' ?>"></script>

    <script>
        <?php if (isset($_GET['type']) && $_GET['type'] == 'edit') { ?>
            edit_firm_profile();
        <?php } else { ?>
            view_profile();
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


        function view_profile() {
            //Load firm_profile Home Page
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/firm_profile/components/view_profile.php?type=view&page_id=35&access=0' ?>',
                success: function(response) {
                    $('#firm_profile').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading firm_profile Home Page:', error);
                }
            });
        }

        function edit_firm_profile() {
            //Load firm_profile Home Page
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/firm_profile/components/edit_profile.php?type=edit&page_id=36&access=0' ?>',
                success: function(response) {
                    $('#firm_profile').html(response);
                    // Set URL query parameter ?type=add
                    var newUrl = window.location.pathname + '?type=edit';
                    history.pushState({}, '', newUrl);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading firm_profile Home Page:', error);
                }
            });
        }

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('logoPreview');

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