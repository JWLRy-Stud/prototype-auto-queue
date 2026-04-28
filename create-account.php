<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="style.css">
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">-->
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
    <div class="container">
    <div class="card">
        <h1>Create Account</h1>  
        <p class="muted"><a href="admin.php">Back to Admin</a></p>
        <form name="createAccountForm" action="testing.php" method="POST">
            <table class="form-table">
                <tr>
                    <td>
                        <label>Create Username:</label>
                    </td>
                    <td>
                        <input type="text" name="username" placeholder="Enter your username">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Create Password:</label>
                    </td>
                    <td>
                        <input type="password" name="password" placeholder="Enter your password">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input class="btn" type="submit" name="create_account" value="Create Account" onclick="return validateForm()">
                    </td>
                </tr>
            </table>
        </form>
    </div>
    </div>
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
