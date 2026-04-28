<?php
include ("database.php");

$currentlyServingQueueNumber = getCurrentlyServingQueueNumber();

if (isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    session_start();
    $_SESSION['username'] = $username;

    if(verifyUser($username, $password)){
        header("Location: timer.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
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
    <div class="container">
        <div class="card">
            <h1>Login to get a Queue Number</h1>

            <?php if ($currentlyServingQueueNumber !== null): ?>
                <p><strong>Currently serving #<?php echo $currentlyServingQueueNumber; ?></strong></p>
            <?php else: ?>
                <p><strong>Currently serving: none yet</strong></p>
            <?php endif; ?>

            <form name="getQueueForm" action="index.php" method="post">
                <table class="form-table">
                    <tr>
                        <td><label for="username">Username:</label></td>
                        <td><input type="text" name="username" placeholder="Enter your username"></td>
                    </tr>
                    <tr>
                        <td><label for="password">Password:</label></td>
                        <td><input type="password" name="password" placeholder="Enter your password"></td>
                    </tr>
                    <tr>
                        <td><input class="btn" type="submit" name="login" value="Login" onclick="return validateForm()"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    </div>
</body>
</html>
