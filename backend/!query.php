<?php

include ('!connect.php');

/************************************/
/*                                  */
/*     Legacy Database Queries      */
/*                                  */
/************************************/

// query all customers
function customerList($pdoLegacy) {
  $queryCustomer = 'SELECT * FROM customers';
  $resultCustomer = $pdoLegacy->query($queryCustomer);
  $customerList = $resultCustomer->fetchAll(PDO::FETCH_ASSOC);
  $resultCustomer->closeCursor();

  return $customerList;
}
//changing this from pdo to pdolegacy fixed it. IDK why, its supposed to just be a function name!
function getCustomerDetails($pdoLegacy, $customerId) {
  $prep = $pdoLegacy->prepare('SELECT * FROM customers WHERE id = ?');
  $prep->execute(array($customerId));
  $fetch = $prep->fetchAll(PDO::FETCH_ASSOC);
  $prep->closeCursor();
  return $fetch;
}


/************************************/
/*                                  */
/*       Quote Table Queries        */
/*                                  */
/************************************/

// query all quotes and put into list
function quoteListAll ($pdo) {
  $queryQuote = 'SELECT * FROM Quote';
  $resultQuote = $pdo->query($queryQuote);
  $quotes = $resultQuote->fetchAll(PDO::FETCH_ASSOC);
  $resultQuote->closeCursor();
  return $quotes;
}

// query all quotes given a type and put into list
function quoteListGivenType($pdo, $type) {
  $prep = $pdo->prepare('SELECT * FROM Quote WHERE quotestatus = ?');
  $prep->execute(array($type));
  $fetch = $prep->fetchAll(PDO::FETCH_ASSOC);
  $prep->closeCursor();
  return $fetch;
}

// query all open quotes relative to whichever associate session is active
function quoteListAssociate($pdo, $type, $id) {
  $prep = $pdo->prepare('SELECT * FROM Quote WHERE quotestatus = ? AND associateID = ?');
  $prep->execute(array($type, $id));
  $fetch = $prep->fetchAll(PDO::FETCH_ASSOC);
  $prep->closeCursor();
  return $fetch;
}

// get a quote given its id
function quoteGivenId($pdo, $quoteid) {
  $prep = $pdo->prepare('SELECT * FROM Quote WHERE quote_id = ?');
  $prep->execute(array($quoteid));
  $fetch = $prep->fetchAll(PDO::FETCH_ASSOC);
  $prep->closeCursor();
  return $fetch;
}

/************************************/
/*                                  */
/*    Secret Notes & Line Items     */
/*                                  */
/************************************/

function getLineItems($pdo, $id){
  $prep = $pdo->prepare('SELECT * FROM LineItems WHERE quote_id = ?');
  $prep->execute(array($id));
  $fetch = $prep->fetchAll(PDO::FETCH_ASSOC);
  $prep->closeCursor();
  return $fetch;
}

function getSecretNotes($pdo, $id){
  $prep = $pdo->prepare('SELECT * FROM SecretNotes WHERE quote_id = ?');
  $prep->execute(array($id));
  $fetch = $prep->fetchAll(PDO::FETCH_ASSOC);
  $prep->closeCursor();
  return $fetch;
}

/************************************/
/*                                  */
/*     Associate Table Queries      */
/*                                  */
/************************************/

// query all associates and put into list
function associateListAll($pdo) {
  $queryAssociate = 'SELECT * FROM Associate WHERE accounttype = 0';
  $resultAssociates = $pdo->query($queryAssociate);
  $associateList = $resultAssociates->fetchAll(PDO::FETCH_ASSOC);
  $resultAssociates->closeCursor();
  return $associateList;
}

function associateListGivenId($pdo, $id) {
  $queryAssociate = $pdo->prepare('SELECT * FROM Associate WHERE id = ?');
  $queryAssociate->execute(array($id));
  $associateList = $queryAssociate->fetchAll(PDO::FETCH_ASSOC);
  $queryAssociate->closeCursor();
  return $associateList;
}