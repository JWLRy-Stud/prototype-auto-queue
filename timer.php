<?php
include ("database.php");
session_start();

$username = $_SESSION['username'] ?? null;
$queueStatus = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["request_queue"]) && $username !== null) {
    if (checkDuplicateQueue($username)) {
        $queueStatus = "existing";
    } else {
        $queueNumber = getQueueNumber($username);
        if ($queueNumber !== false) {
            header("Location: timer.php?queue_status=assigned");
        } else {
            header("Location: timer.php?queue_status=error");
        }
        exit;
    }
}

if ($queueStatus === null) {
    $queueStatus = $_GET["queue_status"] ?? null;
}

$currentlyServingQueueNumber = getCurrentlyServingQueueNumber();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Queue</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Your Queue Number</h1>

            <?php if ($currentlyServingQueueNumber !== null): ?>
                <p><strong>Currently serving #<?php echo $currentlyServingQueueNumber; ?></strong></p>
            <?php else: ?>
                <p><strong>Currently serving: none yet</strong></p>
            <?php endif; ?>

            <?php if ($username === null): ?>
                <p>Please log in first.</p>
            <?php elseif (checkDuplicateQueue($username)): ?>
                <p>You already have a queue number.</p>
                <table>
                    <?php viewOwnQueue($username); ?>
                </table>
            <?php else: ?>
                <?php if ($queueStatus === "assigned"): ?>
                    <p>Queue number assigned successfully.</p>
                    <table>
                        <?php viewOwnQueue($username); ?>
                    </table>
                <?php elseif ($queueStatus === "error"): ?>
                    <p>Unable to assign a queue number right now. Please try again.</p>
                <?php else: ?>
                    <p>Do you want to get a queue number now?</p>
                    <form method="POST" action="timer.php" onsubmit="return confirm('Do you want to get a queue number now?');">
                        <button type="submit" name="request_queue">Get Queue Number</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

            <p class="muted"><a href="index.php">Back to Login Page</a></p>
        </div>
    </div>
</body>
</html>
