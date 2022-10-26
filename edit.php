<?php
require_once "pdo.php";
require_once "util.php";

session_start();
access();



if ( isset($_POST['first_name']) && isset($_POST['last_name'])
    && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {

    $header = 'location:edit.php?profile_id?'.$_GET['profile_id'];

    // data validation from util.php

    if(is_string(validateProfile())){
        $_SESSION['error'] = validateProfile();
        header($header);
        return;
    }
    
    if(is_string(validatePosition())){
        $_SESSION['error'] = validatePosition();
        header($header);
        return;
    }

    if(is_string(validateEducation())){
        $_SESSION['error'] = validateEducation();
        header($header);
        return;
    }

    // update profile data
    $sql = "UPDATE profile SET first_name = :first_name,
                               last_name = :last_name, 
                               email = :email,
                               headline = :headline,
                               summary = :summary
                         WHERE profile_id = :profile_id
                           AND user_id = :uid";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':profile_id' => $_GET['profile_id'],
        ':uid' => $_SESSION['user_id']));
    
    // delete position data
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
    
    // delete education data
    $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

    // insert position data
    insertPosition($pdo,$_REQUEST['profile_id']);

    $_SESSION['success'] = 'Record updated';

    header( 'Location: index.php' ) ;
    return;
}

// select profile data
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :pid AND user_id = :uid");
$stmt->execute(array(':pid' => $_GET['profile_id']),
                    ':uid' => $_SESSION['uid']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

$mk = htmlentities($row['first_name']);
$ml = htmlentities($row['last_name']);
$yr = htmlentities($row['email']);
$mg = htmlentities($row['headline']);
$sy = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

// select position data
$stmt = $pdo->prepare('SELECT * FROM position WHERE profile_id = :pid');
$stmt->execute(array(':pid' => $_GET['profile_id']));
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
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 
    
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
    <title>Bacell Saleh eaa35402</title>
</head>
<body>
    
    <h1>Edit Profile</h1>
    <?php
    flashMessages();
    ?>
    <form method="post">
    <p>First name:
    <input type="text" name="first_name" value="<?= $mk ?>"></p>
    <p>Last name:
    <input type="text" name="last_name" value="<?= $ml ?>"></p>
    <p>Email:
    <input type="text" name="email" value="<?= $yr ?>"></p>
    <p>Headline:
    <input type="text" name="headline" value="<?= $mg ?>"></p>
    <p>Summary:
    <textarea name="summary" id="" cols="30" rows="10"><?= $sy ?></textarea>
    </p>
    <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
    <p>Position:
    <input type="submit" value="+" id="addPos">
    </p>
    <p>Education:
    <input type="submit" value="+" id="addEdu">
    </p>
    <div id="education_fields"></div>
    <div id="position_fields">

    <?php
        //display previosly added education and position data 

        $countPos = 0;
        $countEdu = 0;

        if(count($position !== 0)){

            foreach($position as $row){
                $countPos++;
                echo '<div id="position'.$countPos.'">';
                echo '<p>Year: <input type="text" name="year'.$countPos.'" value="'.htmlentities($row['year']).'" />'; 
                echo '<input type="button" value="-"onclick="$(\'#position'.$countPos.'\').remove();return false;"></p>';
                echo '<textarea name="desc'.$countPos.'" rows="8" cols="80">'.htmlentities($row['description']).'</textarea>';
                echo '</div>';
            }
        }

        if(count($education !== 0)){

            foreach($education as $row){
                $countEdu++;
                echo '<div id="education'.$countEdu.'">';
                echo '<p>Year: <input type="text" name="year'.$countEdu.'" value="'.htmlentities($row['year']).'" />'; 
                echo '<input type="button" value="-"onclick="$(\'#education'.$countEdu.'\').remove();return false;"></p>';
                echo '<p>Institute: <input type="text" class="school" name="school'.$countEdu.'" value ="'.htmlentities($row['name']).'">';
                echo '</div>';
            }
        }
        
    ?>
    </div>
    <script>
        //  add new position and education data

        countPos = <?= $countPos ?>;
        countEdu = <?= $countEdu ?>;


        $(document).ready(function(){
           
            $('#addPos').click(function(event){

                event.preventDefault();
                if ( countPos >= 9 ) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                
                $('#position_fields').append(
                    '<div id="position'+countPos+'"> \
                    <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                    <input type="button" value="-" \
                        onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                    <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                    </div>');
            });


            $('#addEdu').click(function(event){
                // http://api.jquery.com/event.preventdefault/
                event.preventDefault();

                if ( countEdu >= 9 ) {
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
                
                countEdu++;
                
                $('#education_fields').append(
                    '<div id="education'+countEdu+'"> \
                    <p>Year: <input type="text" name="year'+countEdu+'" value="" /> \
                    <input type="button" value="-" \
                        onclick="$(\'#education'+countEdu+'\').remove();return false;"></p> \
                    <p>Institute: <input type="text" class="school" name="school'+countEdu+'"></p>\
                    </div>');
            });

            $('.school').autocomplete({ source: "school.php" });

        });
    </script>
    <p><input type="submit" value="Save"/>
    <a href="index.php">Cancel</a></p>
    </form>
</body>
</html>