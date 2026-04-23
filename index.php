
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get a Queue Number</title>
    <script type="text/javascript">
        function validateForm(){
            var username = document.getQueueForm.username.value;
            var password = document.getQueueForm.password.value;
            if (username == "" || password == ""){
                alert("Please fill in all fields.");
                return false;
            }
        }
    </script>
</head>
<body>
    <h1>Login to get a Queue Number</h1>  
    <form name="getQueueForm" action="index.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" placeholder="Enter your username"> <br>
        <label for="password">Password:</label>
        <input type="password" name="password" placeholder="Enter your password"> <br>
        <input type="submit" name="login" value="Login" onclick="return validateForm()">
    </form>
</body>
</html>

<?php
    include ("database.php");

    $conn = connection();

    if (isset($_POST['login'])){
        $username = $_POST['username'];
        $password = $_POST['password'];
        session_start();
        $_SESSION['username'] = $username;
        /*$conn = connection();
        $sql = "SELECT usernames , passwords FROM users WHERE usernames='$username' AND passwords='$password'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $hpass = $row['passwords'];
        echo $hpass . "<br>";
        */
        //getQueueNumber($username);

        if(verifyUser($username, $password)){
            header("Location: timer.php");
        } 
        //viewQueue();
        
        
        
        exit();
    }
?>