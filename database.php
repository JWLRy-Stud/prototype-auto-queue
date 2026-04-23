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
                return true;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
                $username = "";
                $password = "";
                return false;
            }
        } else {
            echo "Please fill in all fields.";
            $username = "";
            $password = "";
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
        $typedpass = password_hash($password, PASSWORD_DEFAULT);
        $ogpass = $rows['passwords'];
        if (password_verify($password, $ogpass)){
            echo "username: {rows['usernames']} password: {rows['passwords']}  <br>";
            echo "Login successful!";
            return true;
        } else {
            echo "Invalid username or password.";
            return false;
        }
    }

    function checkQueueNumber(){
        $conn = connection();
        try{
            $sql = "SELECT queue_number FROM queue ORDER BY queue_number DESC LIMIT 1";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                return $row['queue_number'] + 1;              
            } else {
                return 1;
            }
        }catch(Exception $e){
            echo "Error: " . $e->getMessage();
        }
        
    }

    function getQueueNumber($username){
        $conn = connection();
        $queue_number = checkQueueNumber();
        $sql = "INSERT INTO queue (username, queue_number, status, created_at) VALUES ('$username', '$queue_number', 'waiting', NOW())";
        if ($conn->query($sql) === TRUE) {
            echo "Your Queue Number is: " . $queue_number . "<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    function checkDuplicateQueue($username){
        $conn = connection();
        $sql = "SELECT status FROM queue WHERE username='$username' AND status='waiting'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function viewAllQueue($order = "ASC"){
        $conn = connection();
        $sql = "SELECT * FROM queue ORDER BY queue_number $order";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "<br>"."created at: " . $row["created_at"]. " - Username: " . $row["username"]. " - Status: " . $row["status"]. "<br>";
            }
        } else {
            echo "No one is in the queue.";
        }
    }

    function viewOwnQueue($username){
        $conn = connection();
        $sql = "SELECT * FROM queue WHERE username='$username' ORDER BY queue_number ASC";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "<br>"."created at: " . $row["created_at"]. " - Username: " . $row["username"]. " - Status: " . $row["status"]. "<br>";
            }
        } else {
            echo "You are not in the queue.";
        }
    }
?>