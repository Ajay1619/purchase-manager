<?php

include_once('./config/sparrow.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($currentLocation) ?></title>

    <!-- Include CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars(GLOBAL_PATH) . '/css/index.css' ?>">
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/toast.css' ?>">
</head>

<body>
    <main>
        <section id="login">
            <div id="toast-container"></div>
            <div class="container">
                <div class="login-box">
                    <img src="<?= GLOBAL_PATH . '/images/logo.png' ?>" class="logo" alt="">
                    <h2>Login</h2>
                    <form id="loginForm" method="POST" action="">
                        <div class="user-box">
                            <input type="text" name="username" required>
                            <label>Username</label>
                        </div>
                        <div class="user-box">
                            <input type="password" name="password" required>
                            <label>Password</label>
                        </div>
                        <input type="submit" value="LOGIN" class="submit">
                    </form>
                </div>
            </div>
        </section>
    </main>
    <!-- Include jQuery -->
    <script src="<?= htmlspecialchars(PACKAGES) . '/jquery/jquery.js' ?>"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '<?= GLOBAL_PATH . '/ajax/login.php' ?>', // The current PHP file
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.href = response.redirect;
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });

        // Toast
        let toastTimeout;

        function showToast(type, message) {
            const toastContainer = document.getElementById('toast-container');

            // Create toast element
            const toast = document.createElement('div');
            toast.classList.add('toast', `${type}-toast`);

            // Create icon based on type
            const icon = document.createElement('span');
            icon.classList.add('toast-icon');
            icon.textContent = getIcon(type);

            // Create message element
            const msg = document.createElement('span');
            msg.classList.add('toast-message');
            msg.textContent = message;

            // Append icon and message to toast
            toast.appendChild(icon);
            toast.appendChild(msg);

            // Append toast to container
            toastContainer.appendChild(toast);

            // Show toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 10); // Small delay to trigger CSS transition

            // Hide toast after 3 seconds
            startToastTimer(toast);

            // Pause the timer when hovering over the toast
            toast.addEventListener('mouseover', () => {
                clearTimeout(toastTimeout);
            });

            // Resume the timer when mouse leaves the toast
            toast.addEventListener('mouseout', () => {
                startToastTimer(toast);
            });
        }

        function startToastTimer(toast) {
            toastTimeout = setTimeout(() => {
                // Apply the hide class to trigger the fade-out effect
                toast.classList.add('hide');
                setTimeout(() => {
                    toast.remove(); // Remove toast from DOM
                }, 500); // Match CSS transition duration
            }, 3000); // 3 seconds
        }

        function getIcon(type) {
            switch (type) {
                case 'info':
                    return 'ℹ️'; // Info icon
                case 'success':
                    return '✅'; // Success icon
                case 'warning':
                    return '⚠️'; // Warning icon
                case 'error':
                    return '❌'; // Error icon
                default:
                    return 'ℹ️'; // Default to info icon
            }
        }
    </script>
</body>

</html>