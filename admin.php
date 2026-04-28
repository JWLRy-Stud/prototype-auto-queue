<?php
include ("database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["serve_next_customer"])) {
    $serveResult = serveNextCustomer();
    $redirectStatus = $serveResult ? "served" : "empty";

    header("Location: admin.php?serve_status={$redirectStatus}");
    exit;
}

$serveStatus = $_GET["serve_status"] ?? null;
?>
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

    <?php if ($serveStatus === "served"): ?>
        <p>Next waiting customer has been marked as served.</p>
    <?php elseif ($serveStatus === "empty"): ?>
        <p>No waiting customer to update.</p>
    <?php endif; ?>

    <div>
        <h2>Current Queue</h2>
        <form method="POST">
            <button type="submit" name="serve_next_customer">Mark the next Served Customer</button>
        </form>
        <table border="1">
            <tr>
                <th>Created At</th>
                <th>Username</th>
                <th>Queue Number</th>
                <th>Status</th>
            </tr>
            <?php
                viewAllQueue();
            ?>
        </table>
    </div>
</body>
</html>
