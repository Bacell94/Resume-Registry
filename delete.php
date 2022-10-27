<?php
require_once "pdo.php";
require_once "util.php";

session_start();
access();


if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {

    $stmt = $pdo->prepare("DELETE FROM profile WHERE profile_id = :id");
    $stmt->execute(array(':id' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

$stmt = $pdo->prepare("SELECT first_name, last_name FROM profile where profile_id = :id");
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bacell Saleh 02fc743e</title>
</head>
<body>
    
    <p>Confirm: Deleting <?= htmlentities($row['first_name']." ".$row['last_name']); ?></p>
    
    <form method="post">
        <input type="hidden"name="profile_id" value="<?= $_GET['profile_id'] ?>">
        <input type="submit" value="Delete" name="delete">
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>