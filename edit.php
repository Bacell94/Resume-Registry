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

    // update profile data
    $sql = "UPDATE profile SET first_name = :first_name,
                               last_name = :last_name, 
                               email = :email,
                               headline = :headline,
                               summary = :summary
                         WHERE profile_id = :profile_id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':profile_id' => $_GET['profile_id']));
    
    // delete position data
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

    // insert position data
    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;

      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];
      $stmt = $pdo->prepare('INSERT INTO Position
        (profile_id, rank, year, description)
        VALUES ( :pid, :rank, :year, :desc)');

      $stmt->execute(array(
      ':pid' => $_REQUEST['profile_id'],
      ':rank' => $rank,
      ':year' => $year,
      ':desc' => $desc)
      );

      $rank++;

    }
    $_SESSION['success'] = 'Record updated';

    header( 'Location: index.php' ) ;
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :id");
$stmt->execute(array(":id" => $_GET['profile_id']));
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
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
    <div id="position_fields">

    <?php
        $stmt = $pdo->prepare('SELECT * FROM position WHERE profile_id = :profile_id');
        $stmt->execute(array(':profile_id' => $_GET['profile_id']));
        $countPos = 0;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $countPos++;
            echo '<div id="position'.$countPos.'">';
            echo '<p>Year: <input type="text" name="year'.$countPos.'" value="'.htmlentities($row['year']).'" />'; 
            echo '<input type="button" value="-"onclick="$(\'#position'.$countPos.'\').remove();return false;"></p>';
            echo '<textarea name="desc'.$countPos.'" rows="8" cols="80">'.htmlentities($row['description']).'</textarea>';
            echo '</div>';
        }
        
    ?>
    </div>
    <script>
        countPos = <?= $countPos ?>;

        // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
        $(document).ready(function(){
           
            $('#addPos').click(function(event){
                // http://api.jquery.com/event.preventdefault/
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
        });
    </script>
    <p><input type="submit" value="Save"/>
    <a href="index.php">Cancel</a></p>
    </form>
</body>
</html>