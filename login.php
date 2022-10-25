<?php
require_once "pdo.php";
require_once "util.php";
session_start();

$salt= 'XyZzy12*_';


if(isset($_POST['email'])){
    if(!strlen($_POST['email']) < 1){

        $check = hash('md5', $salt.$_POST['pass']);

        $stmt = $pdo->prepare('SELECT user_id, name FROM users
   
            WHERE email = :em AND password = :pw');
   
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
   
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( $row !== false ) {

            $_SESSION['name'] = $row['name'];
   
            $_SESSION['user_id'] = $row['user_id'];
   
            // Redirect the browser to index.php
   
            header("Location: index.php");
            return;
        }else{
            $_SESSION['error'] = 'Incorrect email or password';
            header('location:login.php');
            return;
        }
    }else{
        $_SESSION['error'] = 'User name and password are required';
        header('location:login.php');
        return;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bacell Saleh eaa35402</title>
</head>
<body>
    <h1>Please Log In</h1>
    <?php
    flashMessages();
    ?>
    <form method="POST">
        <p>
            <label for="email">User Name:</label>
            <input type="text" name="email" id="email">
        </p>
        <p>
            <label for="pass">Password:</label>
            <input type="text" name="pass" id="pass">
        </p>
        <p>
            <input type="submit" onclick="return doValidate();" value="Log In">
            <a href="index.php">Cancel</a>
        </p>
    </form>
    <script type="text/javascript">
        function doValidate() {
            console.log('Validating...');
            try {
                pw = document.getElementById('pass').value;
                console.log("Validating pw="+pw);
                if (pw == null || pw == "") {
                    alert("Both fields must be filled out");
                    return false;
                }
                return true;
            } catch(e) {
                return false;
            }
            return false;
        }
    </script>
</body>
</html>