<?php
require_once "pdo.php";

function flashMessages(){
    if(isset($_SESSION['error'])){
        echo "<p style='color:red;'>".$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
    if(isset($_SESSION['success'])){
        echo "<p style='color:green;'>".$_SESSION['success']."</p>\n";
        unset($_SESSION['success']);
    }
}

function access(){
    
    if(!isset($_SESSION['user_id'])){
        die('ACCESS DENIED');
    }
}

function validateProfile(){

    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email'])  < 1
    || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1 ) {

        return 'All fields are required';
    }
 
    if ( strpos($_POST['email'],'@') === false ) {
    
        return 'Email must contain @';
    } 

    return true;
}

function validatePosition() {
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;
  
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];
  
      if ( strlen($year) == 0 || strlen($desc) == 0 ) {
        return "All fields are required";
      }
  
      if ( ! is_numeric($year) ) {
        return "Position year must be numeric";
      }
    }
    return true;
  }

function validateEducation() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['institute'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $institute = $_POST['institute'.$i];

    if ( strlen($year) == 0 || strlen($institute) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Position year must be numeric";
    }
  }
  return true;
}

// fix????

// function insertPosition($pid){
//   $rank = 1;
  
//   for($i=1; $i<=9; $i++) {

//     if ( ! isset($_POST['year'.$i]) ) continue;
//     if ( ! isset($_POST['desc'.$i]) ) continue;
  
//     $year = $_POST['year'.$i];
//     $desc = $_POST['desc'.$i];

//     $stmt = $pdo->prepare('INSERT INTO Position
//       (profile_id, rank, year, description)
//       VALUES ( :pid, :rank, :year, :desc)');

//     $stmt->execute(array(
//     ':pid' => $pid,
//     ':rank' => $rank,
//     ':year' => $year,
//     ':desc' => $desc)
//     );
  
//     $rank++;
//   }
// }