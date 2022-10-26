<?php
require_once "pdo.php";
require_once "util.php";

session_start();

access();

// select profile data
$stmt = $pdo->query('SELECT * FROM profile where profile_id ='.$_GET['profile_id']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);

// select position data
$stmt = $pdo->prepare('SELECT * FROM position WHERE profile_id = :profile_id');
$stmt->execute(array(':profile_id' => $_GET['profile_id']));
$position = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $position[] = $row;
}

// select education data
$stmt = $pdo->prepare('SELECT * FROM education WHERE profile_id = :profile_id');
$stmt->execute(array(':profile_id' => $_GET['profile_id']));
$education = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $education[] = $row;
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
    <h1>Profile inforamtion</h1>

    <p>Frist Name: <?= $first_name ?></p>
    <p>Last Name: <?= $last_name ?></p>
    <p>Email: <?= $email ?></p>
    <p>Headline: <?= $headline ?></p>
    <p>Summary: 
        <br> 
        <?= $summary ?>
    </p>

    <?php
    // display position and education data if available

    if(count($position) !== 0){
        echo "<p>Positions:";
        echo "<ul>";
        foreach($position as $row){
            echo "<li>".htmlentities($row['year']).": ".htmlentities($row['description'])."</li>";
        }
        echo "</ul>";
        echo "</p>";
    }

    if(count($education) !== 0){
        echo "<p>Education:";
        echo "<ul>";
        foreach($education as $row){
            echo "<li>".htmlentities($row['year']).": ".htmlentities($row['name'])."</li>";
        }
        echo "</ul>";
        echo "</p>";
    }
    ?>
    <p><a href="index.php">Done</a></p>
    
</body>
</html>