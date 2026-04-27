<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Admin</h1>
    <a href="create-account.php">Create Account</a>
    <div>
        <h2>Current Queue</h2>
        <table border="1">
            <tr>
                <th>Created At</th>
                <th>Username</th>
                <th>Queue Number</th>
                <th>Status</th>
            </tr>
            <?php
                include ("database.php");
                viewAllQueue();
            ?>
        </table>
    </div>
</body>
</html>