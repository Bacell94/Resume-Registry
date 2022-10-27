<?php

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
    if ( ! isset($_POST['school'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $school = $_POST['school'.$i];

    if ( strlen($year) == 0 || strlen($school) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Education year must be numeric";
    }
  }
  return true;
}


function insertPosition($pdo,$pid){
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
    ':pid' => $pid,
    ':rank' => $rank,
    ':year' => $year,
    ':desc' => $desc)
    );
  
    $rank++;
  }
}

function insertEducation($pdo,$pid){
  $rank = 1;
  
  for($i=1; $i<=9; $i++) {

    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['school'.$i]) ) continue;
  
    $year = $_POST['year'.$i];
    $name = $_POST['school'.$i];

    // check if institution name already exists in table
    $institution_id = false;
    $stmt = $pdo->prepare('SELECT * FROM institution WHERE name = :name');
    $stmt->execute(array( ':name' => $name));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row !== false) $institution_id = $row['institution_id'];

    // if id doesn't exist insert it in to the table
    if($institution_id === false){
      
      $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
      $stmt->execute(array(':name' => $name));
      $institution_id =  $pdo->lastInsertId();
    }
   

    // insert education data
    $stmt = $pdo->prepare('INSERT INTO education
      (profile_id, rank, year, institution_id)
      VALUES ( :pid, :rank, :year, :institution_id)');

    $stmt->execute(array(
    ':pid' => $pid,
    ':rank' => $rank,
    ':year' => $year,
    ':institution_id' => $institution_id)
    );
  
    $rank++;
  }
}