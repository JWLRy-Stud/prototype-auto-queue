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

    <?php
        include ("database.php");
<<<<<<< codex/add-button-to-update-customer-status-iu9chp
        syncQueueByTime();
        $service_time_seconds = getServiceTimeSeconds();
        $message = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST["set_service_time"])) {
                $new_time = isset($_POST["service_time_seconds"]) ? (int)$_POST["service_time_seconds"] : $service_time_seconds;
                if (setServiceTimeSeconds($new_time)) {
                    $service_time_seconds = getServiceTimeSeconds();
                    $message = "Service time updated successfully.";
                } else {
                    $message = "Failed to update service time.";
                }
            } elseif (isset($_POST["serve_next_customer"])) {
                if (serveNextCustomer()) {
                    $message = "Next waiting customer has been marked as served.";
                } else {
                    $message = "No waiting customer to update.";
                }
=======

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["serve_next_customer"])) {
            if (serveNextCustomer()) {
                echo "<p>Next waiting customer has been marked as served.</p>";
            } else {
                echo "<p>No waiting customer to update.</p>";
>>>>>>> my-feature
            }
        }
    ?>

    <div>
        <h2>Clock</h2>
        <p id="adminClock"></p>
        <?php if (!empty($message)) { echo "<p>" . $message . "</p>"; } ?>
        <h2>Queue Timer Settings</h2>
        <form method="POST">
            <label for="service_time_seconds">Seconds per customer:</label>
            <input type="number" name="service_time_seconds" min="5" value="<?php echo $service_time_seconds; ?>">
            <button type="submit" name="set_service_time">Update Time</button>
        </form>
        <form method="POST">
            <button type="submit" name="serve_next_customer">Mark Served Customer</button>
        </form>
        <h2>Current Queue</h2>
        <form method="POST">
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
    <script>
        function updateAdminClock() {
            const now = new Date();
            document.getElementById("adminClock").textContent = now.toLocaleString();
        }
        updateAdminClock();
        setInterval(updateAdminClock, 1000);
    </script>
</body>
</html>
