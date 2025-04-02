<?php
require_once('../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>

    <section id="topNavBar">
        <nav class="navbar">
            <div class="navbar-user">
                <span class="username"><?= $_SESSION['employee_name'] ?></span>
                <img src="<?= GLOBAL_PATH . '/files/profile_pictures/' . $_SESSION['employee_pic'] ?>" alt="User Avatar" class="avatar" id="userAvatar">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="#" id="changePassword">Change Password</a>
                    <a href="#" id="logout">Logout</a>
                </div>
            </div>
        </nav>
    </section>
    <script>
        $(document).ready(function() {
            $('#logout').click(function(e) {
                e.preventDefault(); // Prevent the default action of the link

                // Send AJAX request to logout.php
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/ajax/logout.php' ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Redirect to login page or perform other actions after logout
                            window.location.href = '<?= BASEPATH ?>';
                        } else {
                            alert('Logout failed. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status + ' - ' + error);
                    }
                });
            });
        });
    </script>
<?php } ?>