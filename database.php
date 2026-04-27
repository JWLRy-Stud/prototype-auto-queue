<?php
    function connection(){
        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "queue_system";

        $con = new mysqli($host, $username, $password, $database);

        if($con->connect_error){
            echo $con->connect_error;
        } else{
            return $con;
        }
    }

    function insertUser($username, $password){
        $conn = connection();
        if (!empty($username) && !empty($password)){
            $password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (usernames, passwords) VALUES ('$username', '$password')";
            if ($conn->query($sql) === TRUE) {
                echo "New user created successfully";
                $username = "";
                $password = "";
                $conn->close();
                return true;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
                $username = "";
                $password = "";
                $conn->close();
                return false;
            }
        } else {
            echo "Please fill in all fields.";
            $username = "";
            $password = "";
            $conn->close();
            return false;
        }
    }

    function verifyUser($username, $password){
        /* I-set lagi ang varchar size to 255 kase kapag below 255 ang size ng varchar, 
        hindi maverify ang password. Hindi ko alam kung ano yung nararapat na size ng varchar kapag 
        hinahash na yung password. Dto ko nakita yung solution 
        https://www.php.net/manual/en/function.password-hash.php
        https://stackoverflow.com/questions/19855715/php-password-hash-and-password-verify-issues-no-match#comment65333286_25583037*/
        $conn = connection();
        $sql = "SELECT usernames , passwords FROM users WHERE usernames='$username'";
        $result = mysqli_query($conn, $sql);
        $rows = mysqli_fetch_assoc($result);
        $ogpass = $rows['passwords'];
        if (password_verify($password, $ogpass)){
            //echo "username: {rows['usernames']} password: {rows['passwords']}  <br>";
            //echo "Login successful!";
            $conn->close();
            return true;
        } else {
            echo "Invalid username or password.";
            $conn->close();
            return false;
        }
    }

    function checkQueueNumber(){
        $conn = connection();
            $sql = "SELECT queue_number FROM queue ORDER BY queue_number DESC LIMIT 1";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $conn->close();    
                return $row['queue_number'] + 1;
            } else {
                $conn->close();
                return 1;
            }
    }

    function getQueueNumber($username){
        $conn = connection();
        $queue_number = checkQueueNumber();
        $sql = "INSERT INTO queue (username, queue_number, status, created_at) VALUES ('$username', '$queue_number', 'waiting', NOW())";
        if ($conn->query($sql) === TRUE) {
            echo "Your Queue Number is: " . $queue_number . "<br>";
            $conn->close();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            $conn->close();
        }
    }

    function checkDuplicateQueue($username){
        $conn = connection();
        $sql = "SELECT status FROM queue WHERE username='$username' AND status='waiting'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $conn->close();
            return true;
        } else {
            $conn->close();
            return false;
        }
    }

    function ensureQueueSettingsTable(){
        $conn = connection();
        $sql = "CREATE TABLE IF NOT EXISTS queue_settings (
                    id INT PRIMARY KEY,
                    service_time_seconds INT NOT NULL DEFAULT 60,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
        $conn->query($sql);
        $conn->query("INSERT IGNORE INTO queue_settings (id, service_time_seconds) VALUES (1, 60)");
        $conn->close();
    }

    function getServiceTimeSeconds(){
        ensureQueueSettingsTable();
        $conn = connection();
        $sql = "SELECT service_time_seconds FROM queue_settings WHERE id = 1 LIMIT 1";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $conn->close();
            return (int)$row["service_time_seconds"];
        }

        $conn->close();
        return 60;
    }

    function setServiceTimeSeconds($seconds){
        $seconds = (int)$seconds;
        if ($seconds < 5) {
            $seconds = 5;
        }
        $conn = connection();
        $sql = "UPDATE queue_settings SET service_time_seconds=$seconds WHERE id=1";
        $success = $conn->query($sql) === TRUE;
        $conn->close();
        return $success;
    }

    function syncQueueByTime(){
        $service_time = getServiceTimeSeconds();
        if ($service_time <= 0) {
            return;
        }

        $conn = connection();
        $first_sql = "SELECT created_at FROM queue ORDER BY queue_number ASC LIMIT 1";
        $first_result = mysqli_query($conn, $first_sql);
        if (!$first_result || mysqli_num_rows($first_result) === 0) {
            $conn->close();
            return;
        }

        $first_row = mysqli_fetch_assoc($first_result);
        $elapsed_seconds = time() - strtotime($first_row["created_at"]);
        if ($elapsed_seconds < $service_time) {
            $conn->close();
            return;
        }

        $total_should_be_served = (int) floor($elapsed_seconds / $service_time);
        $served_result = mysqli_query($conn, "SELECT COUNT(*) AS served_count FROM queue WHERE status='served'");
        $served_row = mysqli_fetch_assoc($served_result);
        $served_count = (int)$served_row["served_count"];
        $to_update = $total_should_be_served - $served_count;

        if ($to_update > 0) {
            $update_sql = "UPDATE queue SET status='served' WHERE status='waiting' ORDER BY queue_number ASC LIMIT $to_update";
            $conn->query($update_sql);
        }

        $conn->close();
    }

    function viewAllQueue(){
        $conn = connection();
        $sql = "SELECT * FROM queue ORDER BY queue_number ASC";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "
                    <tr>
                        <td>" . $row["created_at"]. "</td>
                        <td>" . $row["username"]. "</td>
                        <td>" . $row["queue_number"]. "</td>
                        <td>" . $row["status"]. "</td>
                    </tr>
                ";
            }
        } else {
            echo "No one is in the queue.";
        }
        $conn->close();
    }

    function viewOwnQueue($username){
        $conn = connection();
        $sql = "SELECT * FROM queue WHERE username='$username' AND status='waiting'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "
                    <tr>
                        <td>
                            <label>Created At:</label>
                        </td>
                        <td>" . $row["created_at"]. "</td>
                    </tr>
                    <tr>
                        <td>
                            <label>Username:</label>
                        </td>
                        <td>" . $row["username"]. "</td>
                    </tr>
                    <tr>
                        <td>
                            <label>Queue Number:</label>
                        </td>
                        <td>" . $row["queue_number"]. "</td>
                    </tr>
                    <tr>
                        <td>
                            <label>Status:</label>
                        </td>
                        <td>" . $row["status"]. "</td>
                    </tr>
                    ";
            }
        } else {
            echo "You are not in the queue.";
        }
        $conn->close();
    }

    function getCustomerCountdown($username){
        syncQueueByTime();
        $conn = connection();
        $service_time = getServiceTimeSeconds();

        $user_waiting_sql = "SELECT queue_number FROM queue WHERE username='$username' AND status='waiting' ORDER BY queue_number ASC LIMIT 1";
        $user_waiting_result = mysqli_query($conn, $user_waiting_sql);
        if ($user_waiting_result && mysqli_num_rows($user_waiting_result) > 0) {
            $user_row = mysqli_fetch_assoc($user_waiting_result);
            $queue_number = (int)$user_row["queue_number"];

            $position_sql = "SELECT COUNT(*) AS waiting_position FROM queue WHERE status='waiting' AND queue_number <= $queue_number";
            $position_result = mysqli_query($conn, $position_sql);
            $position_row = mysqli_fetch_assoc($position_result);
            $position = (int)$position_row["waiting_position"];

            // Waiting time is based on queue position:
            // first waiting customer waits 1 service slot, second waits 2 slots, etc.
            $seconds_left = $position * $service_time;
            $conn->close();
            return max(0, (int)$seconds_left);
        }

        $served_sql = "SELECT queue_number FROM queue WHERE username='$username' AND status='served' ORDER BY queue_number DESC LIMIT 1";
        $served_result = mysqli_query($conn, $served_sql);
        if ($served_result && mysqli_num_rows($served_result) > 0) {
            $conn->close();
            return 0;
        }

        $conn->close();
        return null;
    }

    function serveNextCustomer(){
        $conn = connection();
        $sql = "UPDATE queue SET status='served' WHERE status='waiting' ORDER BY queue_number ASC LIMIT 1";
        if ($conn->query($sql) === TRUE) {
            $updated_rows = $conn->affected_rows;
            $conn->close();
            return $updated_rows > 0;
        }

        $conn->close();
        return false;
    }
    
?>
