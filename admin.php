<?php
include ("database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["serve_next_customer"])) {
    $serveResult = serveNextCustomer();

    if ($serveResult !== false) {
        $servedQueueNumber = $serveResult['served_queue_number'];
        $beingServedQueueNumber = $serveResult['being_served_queue_number'];

        $redirectParams = ["serve_status=updated"];
        if ($servedQueueNumber !== null) {
            $redirectParams[] = "served_queue_number={$servedQueueNumber}";
        }
        if ($beingServedQueueNumber !== null) {
            $redirectParams[] = "being_served_queue_number={$beingServedQueueNumber}";
        }

        header("Location: admin.php?" . implode("&", $redirectParams));
    } else {
        header("Location: admin.php?serve_status=empty");
    }
    exit;
}

$serveStatus = $_GET["serve_status"] ?? null;
$servedQueueNumber = $_GET["served_queue_number"] ?? null;
$beingServedQueueNumber = $_GET["being_served_queue_number"] ?? null;
$currentlyServingQueueNumber = getCurrentlyServingQueueNumber();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Admin</h1>
            <div class="actions">
                <a class="btn-link" href="create-account.php">Create Account</a>
            </div>

            <?php if ($currentlyServingQueueNumber !== null): ?>
                <p><strong>Currently serving #<?php echo $currentlyServingQueueNumber; ?></strong></p>
            <?php else: ?>
                <p><strong>Currently serving: none yet</strong></p>
            <?php endif; ?>

            <?php if ($serveStatus === "updated"): ?>
                <?php if ($servedQueueNumber !== null): ?>
                    <p>Queue #<?php echo (int)$servedQueueNumber; ?> has been marked as served.</p>
                <?php endif; ?>
                <?php if ($beingServedQueueNumber !== null): ?>
                    <p>Queue #<?php echo (int)$beingServedQueueNumber; ?> is now being served.</p>
                <?php endif; ?>
            <?php elseif ($serveStatus === "empty"): ?>
                <p>No waiting customer to update.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Current Queue</h2>
            <form method="POST" action="admin.php">
                <button type="submit" name="serve_next_customer">Mark Served Customer</button>
            </form>
            <table>
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
    </div>
</body>
</html>
