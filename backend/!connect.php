<?php

/*===============================================================*/

// ACCESS INFO FOR OUR DATABASE
$dsn = "mysql:host=courses;dbname=z1828609";
$username = "z1828609";
$password = "1999Mar31";

// ACCESS INFO FOR LEGACY DATABASE
$legDsn = "mysql:host=blitz.cs.niu.edu;port=3306;dbname=csci467";
$legUsername = "student";
$legPassword = "student";

/*===============================================================*/

// CONNECT TO OUR DATABASE
try {
  $pdo = new PDO($dsn, $username, $password);
} catch (PDOexception $e) {
  echo "Connection to database failed: " . $e->getMessage();
}

// CONNECT TO LEGACY DATABASE
try {
  $pdoLegacy = new PDO($legDsn, $legUsername, $legPassword);
} catch (PDOexception $e) {
  echo "Connection to database failed: " . $e->getMessage();
}

/*===============================================================*/