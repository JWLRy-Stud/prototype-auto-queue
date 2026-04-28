
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
    <div>
    <h1>Login to get a Queue Number</h1>  
    <form name="getQueueForm" action="index.php" method="post">
        <table>
            <tr>
                <td>
                    <label for="username">Username:</label>
                </td>
                <td>
                    <input type="text" name="username" placeholder="Enter your username">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="password">Password:</label>
                </td>
                <td>
                    <input type="password" name="password" placeholder="Enter your password">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="login" value="Login" onclick="return validateForm()">
                </td>
            </tr>
        </table>
    </form>
    </div>
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
        if(verifyUser($username, $password)){
            header("Location: timer.php");
        } 
    }
?>