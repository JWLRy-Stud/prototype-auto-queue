<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Queue</title>
</head>
<body>
    <h1>Your Queue Number</h1>
    <table border="1">
        <?php
            include ("database.php");
            session_start();
            if(checkDuplicateQueue($_SESSION['username'])){
                echo "You already have a queue number.";
                viewOwnQueue($_SESSION['username']);
            } else {
                echo "Queue number assigned successfully.";
                getQueueNumber($_SESSION['username']);
                viewOwnQueue($_SESSION['username']);
            }
        ?> 
    </table>
    <a href="index.php">Back to Login Page</a>
</body>
</html>
