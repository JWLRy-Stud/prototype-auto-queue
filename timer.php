<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Queue</title>
</head>
<body>
    <h1>Your Queue Number</h1>
    <?php
        include ("database.php");
        session_start();
        syncQueueByTime();

        if(checkDuplicateQueue($_SESSION['username'])){
            echo "You already have a queue number.";
        } else {
            echo "Queue number assigned successfully.";
            getQueueNumber($_SESSION['username']);
        }

        $countdown_seconds = getCustomerCountdown($_SESSION['username']);
    ?>
    <h2>Timer</h2>
    <p id="queueTimer">
        <?php
            if ($countdown_seconds === null) {
                echo "No active queue entry found.";
            } elseif ($countdown_seconds === 0) {
                echo "You have been served.";
            } else {
                echo "Estimated time remaining: " . $countdown_seconds . " seconds.";
            }
        ?>
    </p>
    <table border="1">
        <?php
            viewOwnQueue($_SESSION['username']);
        ?> 
    </table>
    <a href="index.php">Back to Login Page</a>
    <script>
        let remainingSeconds = <?php echo $countdown_seconds === null ? "null" : (int)$countdown_seconds; ?>;

        function formatSeconds(totalSeconds) {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            return `${minutes}m ${seconds}s`;
        }

        function updateQueueTimer() {
            const timerEl = document.getElementById("queueTimer");
            if (remainingSeconds === null) {
                timerEl.textContent = "No active queue entry found.";
                return;
            }

            if (remainingSeconds <= 0) {
                timerEl.textContent = "You have been served.";
                return;
            }

            timerEl.textContent = `Estimated time remaining: ${formatSeconds(remainingSeconds)}`;
            remainingSeconds -= 1;
        }

        updateQueueTimer();
        setInterval(updateQueueTimer, 1000);
    </script>
</body>
</html>
