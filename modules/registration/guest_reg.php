<?php
include_once('../../config/sparrow.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Room Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="./css/guest_reg.css" rel="stylesheet">

</head>

<body>
    <div class="container"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        function toggleCardFields(value) {
            const cardFields = document.getElementById('credit_card_fields');
            const cardExpiry = document.getElementById('credit_card_expiry');

            if (value === 'credit_card') {
                cardFields.classList.remove('hidden');
                cardExpiry.classList.remove('hidden');
            } else {
                cardFields.classList.add('hidden');
                cardExpiry.classList.add('hidden');
            }
        }

        $.ajax({
            url: './components/guest_registration_form.php?type=123abcd',
            type: 'GET',
            success: function(response) {
                $('.container').html(response); // Inject room details into modal body
                $('.modal').css('display', 'flex'); // Display modal as flex container
            }
        });
    </script>
</body>

</html>