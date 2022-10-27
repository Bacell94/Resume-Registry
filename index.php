<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if(isset($_SESSION['user_id'])){
    $stmt = $pdo->query("SELECT * FROM profile where user_id =".$_SESSION['user_id']);
    $profile = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $profile[] = $row;
    }
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
    <h1>My Resume Registry</h1>

    <?php

    flashMessages();

    if(!isset($_SESSION['user_id'])){
        
        echo "<p><a href='login.php'>Please log in</a></p>";

    }else{

        echo "<p><a href='add.php'>Add New Entry</a></p>";

        if(count($profile) !== 0){

            echo('<table border="1">');
            foreach($profile as $row){
    
                echo "<tr><td>";
                echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name'])." ".htmlentities($row['last_name']).'</a>');
                echo("</td><td>");
                echo(htmlentities($row['headline']));
                echo("</td><td>");
                echo(htmlentities($row['summary']));
                echo("</td><td>");
                echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
                echo('<a href="delete.php?profile_id='. $row['profile_id'].'">Delete</a>');
                echo("</td></tr>\n");       

            }
            echo '</table>';

        }else{

            echo "<p>Not entries found</p>";
        }

        echo "<p><a href='logout.php'>Logout</a></p>";
    }
    ?>
   
    
</body>
</html>