<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <script type="text/javascript">
        function validateForm(){
            var username = document.createAccountForm.username.value;
            var password = document.createAccountForm.password.value;
            if (username == "" || password == ""){
                alert("Please fill in all fields.");
                return false;
            }
        }
    </script>
</head>
<body>
        ~<h1>Create Account</h1>  
        <a href="dashboard.php">Back to Dashboard</a>
    <form name="createAccountForm" action="testing.php" method="post">
        <label for="username">Create Username:</label>
        <input type="text" name="username" placeholder="Enter your username"> <br>
        <label for="password">Create  Password:</label>
        <input type="password" name="password" placeholder="Enter your password"> <br>
        <input type="submit" name="create_account" value="Create Account" onclick="return validateForm()">
    </form>
</body>
</html>


<?php
    include ("database.php");

    if (isset($_POST['create_account'])){
        $username = $_POST['username'];
        $password = $_POST['password']; 
        if(!empty($username) && !empty($password)){
            insertUser($username, $password);
        } else {
            echo "Please fill in all fields.";

        }
    }
?>