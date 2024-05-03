<?php

/*===============================================================*/

// ACCESS INFO FOR OUR DATABASE
$dsn = "mysql:host=REDACTED;dbname=REDACTED";
$username = "REDACTED";
$password = "REDACTED";

// ACCESS INFO FOR LEGACY DATABASE
$legDsn = "mysql:host=REDACTED;dbname=REDACTED";
$legUsername = "REDACTED";
$legPassword = "REDACTED";

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