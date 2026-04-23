<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Your Queue Number</h1>
</body>
</html>
<?php
    include ("database.php");
    session_start();
    if(checkDuplicateQueue($_SESSION['username'])){
        echo "You already have a queue number.";
        viewOwnQueue($_SESSION['username']);
    } else {
        getQueueNumber($_SESSION['username']);
        echo "Queue number assigned successfully.";
        viewOwnQueue($_SESSION['username']);
    }
    
?>