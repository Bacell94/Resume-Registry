<?php
$pdo = new PDO('mysql:host=host;port=port;dbname=yourdb', 
   'name', 'pass');
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
