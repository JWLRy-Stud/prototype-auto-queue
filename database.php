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
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
                $username = "";
                $password = "";
                $conn->close();
            }
        } else {
            echo "Please fill in all fields.";
            $username = "";
            $password = "";
            $conn->close();
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
            $conn->close();
            return $queue_number;
        } else {
            $conn->close();
            return false;
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

    function serveNextCustomer(){
        $conn = connection();
        $sql = "UPDATE queue SET status='served' WHERE status='waiting' ORDER BY queue_number ASC LIMIT 1";

        if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
            $servedQueueNumber = getCurrentlyServingQueueNumber($conn);
            $conn->close();
            return $servedQueueNumber;
        }

        $conn->close();
        return false;
    }

    function getCurrentlyServingQueueNumber($conn = null){
        $useSharedConnection = $conn !== null;
        if (!$useSharedConnection) {
            $conn = connection();
        }
        $waitingSql = "SELECT queue_number FROM queue WHERE status='waiting' LIMIT 1";
        $waitingResult = mysqli_query($conn, $waitingSql);

        if (mysqli_num_rows($waitingResult) === 0) {
            if (!$useSharedConnection) {
                $conn->close();
            }
            return null;
        }

        $sql = "SELECT queue_number FROM queue WHERE status='served' ORDER BY queue_number DESC LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (!$useSharedConnection) {
                $conn->close();
            }
            return (int)$row['queue_number'];
        }

        if (!$useSharedConnection) {
            $conn->close();
        }
        return null;
    }
    
?>
