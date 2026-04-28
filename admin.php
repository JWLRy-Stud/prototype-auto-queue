<?php
include ("database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["serve_next_customer"])) {
    $servedQueueNumber = serveNextCustomer();

    if ($servedQueueNumber !== false) {
        header("Location: admin.php?serve_status=served&served_queue_number={$servedQueueNumber}");
    } else {
        header("Location: admin.php?serve_status=empty");
    }
    exit;
}

$serveStatus = $_GET["serve_status"] ?? null;
$servedQueueNumber = $_GET["served_queue_number"] ?? null;
$currentlyServingQueueNumber = getCurrentlyServingQueueNumber();
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

    <?php if ($currentlyServingQueueNumber !== null): ?>
        <p><strong>Currently serving #<?php echo $currentlyServingQueueNumber; ?></strong></p>
    <?php else: ?>
        <p><strong>Currently serving: none yet</strong></p>
    <?php endif; ?>

    <?php if ($serveStatus === "served" && $servedQueueNumber !== null): ?>
        <p>Queue #<?php echo (int)$servedQueueNumber; ?> has been marked as served.</p>
    <?php elseif ($serveStatus === "empty"): ?>
        <p>No waiting customer to update.</p>
    <?php endif; ?>

    <div>
        <h2>Current Queue</h2>
        <form method="POST" action="admin.php">
            <button type="submit" name="serve_next_customer">Mark Served Customer</button>
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
