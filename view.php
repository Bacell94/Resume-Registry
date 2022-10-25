<?php
require_once "pdo.php";
require_once "util.php";

session_start();

access();

$stmt = $pdo->query('SELECT * FROM profile where profile_id ='.$_GET['profile_id']);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);

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
    <h1>Profile inforamtion</h1>

    <p>Frist Name: <?= $first_name ?></p>
    <p>Last Name: <?= $last_name ?></p>
    <p>Email: <?= $email ?></p>
    <p>Headline: <?= $headline ?></p>
    <p>Summary: 
        <br> 
        <?= $summary ?>
    </p>
    <p>Positions:
        <ul>
            <?php           

            $pos = false;
            $stmt = $pdo->prepare('SELECT * FROM position WHERE profile_id = :profile_id');
            $stmt->execute(array(':profile_id' => $_GET['profile_id']));
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $pos = true;
                echo "<li>".htmlentities($row['description'])." ".htmlentities($row['year'])."</li>";
            }
            if(!$pos){
                echo "<li>No positions added</li>";
            }
            ?>
        </ul>
    </p>
    <p><a href="index.php">Done</a></p>
    
</body>
</html>