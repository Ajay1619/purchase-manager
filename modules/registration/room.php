<?php
include_once('../../config/sparrow.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <link rel="stylesheet" href="./css/room.css">
</head>

<body>
    <h1>Room Management</h1>
    <div class="room-container" id="room-container">


        <!-- More Room Cards -->
    </div>

    <!-- Modal for Room Details -->
    <div class="modal" id="modal"></div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <script>
        function showRoomDetails(roomId) {
            // AJAX call to fetch room details
            $.ajax({
                url: './components/room_modal.php',
                type: 'POST',
                data: {
                    roomId: roomId
                },
                success: function(response) {
                    $('#modal').html(response); // Inject room details into modal body
                    $('.modal').css('display', 'flex'); // Display modal as flex container
                }
            });
        }
        $.ajax({
            url: './components/room_list.php',
            type: 'POST',
            success: function(response) {
                $('#room-container').html(response); // Inject room details into modal body
            }
        });

        // Close modal function
        $(document).on('click', '.modal-close', function() {
            $('#modal .modal-content').html(""); // Clear modal content
            $('#modal').css('display', 'none'); // Hide modal
        });

        // Close modal when clicking outside the modal content
        $(document).on('click', '#modal', function(event) {
            if ($(event.target).is('#modal')) {
                $('#modal .modal-content').html(""); // Clear modal content
                $('#modal').css('display', 'none'); // Hide modal
            }
        });
    </script>
</body>

</html>