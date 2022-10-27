<?php
require_once "pdo.php";
require_once "util.php";

session_start();

access();

if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {

    $header = 'location:add.php';

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



    $sql = "INSERT INTO profile (first_name, last_name, email, headline, summary, user_id)
              VALUES (:first_name, :last_name, :email, :headline, :summary, :user_id)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':user_id' => $_SESSION['user_id']));

    $profile_id = $pdo->lastInsertId();

    insertPosition($pdo,$profile_id);

    insertEducation($pdo,$profile_id);

    $_SESSION['success'] = 'Record Added';

    header( 'Location: index.php' ) ;
    return;
}


?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta first_name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
    <title>Bacell Saleh 02fc743e</title>
</head>
<body>
    <h1>Add new profile</h1>
    <?php
    flashMessages();
    ?>
    <form method="post">
    <p>First name:
    <input type="text" name="first_name"></p>
    <p>Last name:
    <input type="text" name="last_name"></p>
    <p>email:
    <input type="text" name="email"></p>
    <p>headline:
    <input type="text" name="headline"></p>
    <p>Summary: 
        <br>
        <textarea name="summary" cols="80" rows="8"></textarea>
    </p>
    <p>Position:
    <input type="submit" value="+" id="addPos">
    </p>
    <div id="position_fields"></div>
    <p>Education:
    <input type="submit" value="+" id="addEdu">
    </p>
    <div id="education_fields"></div>
    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
    <input type="submit" value="Add"/>
    <a href="index.php">Cancel</a>
    </form>
    <script type="text/javascript">

        countPos = 0;
        countEdu = 0;


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
                    <p>Institute: <input class="school" type="text" name="school'+countEdu+'" value=""></p>\
                    </div>');

                $('.school').autocomplete({ source: "school.php" });
                
            });

        });

    </script>
</body>
</html>