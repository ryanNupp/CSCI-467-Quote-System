<?php 
include("!query.php");
session_start();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/************************************/
/*                                  */
/*     Call the proper function     */
/*                                  */
/************************************/

if(isset($_POST['action'])) {
    // listing quotes in table format
    if ($_POST['action'] == "get_all_quote") { get_all_quotes($pdo, $pdoLegacy); }
    if ($_POST['action'] == "get_open_quote") { get_open_quotes($pdo, $pdoLegacy); }
    if ($_POST['action'] == "get_finalized_quote") { get_finalized_quotes($pdo, $pdoLegacy); }
    if ($_POST['action'] == "get_sanctioned_quote") { get_sanctioned_quotes($pdo, $pdoLegacy); }

    // adding quotes in the database 
    if ($_POST['action'] == "add_quote") { add_quotes($pdo, $_POST['customer_id'], $_POST['associateID']); }
    
    // changing quote status in the database
    if ($_POST['action'] == "finalize_quote") { finalize_quotes($pdo, $_POST['quote_id']); }
    if ($_POST['action'] == "sanction_quote") { sanction_quotes($pdo, $_POST['quote_id']); }
    if ($_POST['action'] == "process_quote") { process_quotes($pdo, $_POST['quote_id']); }

    //add customer email
    if ($_POST['action'] == "customer_email") { addCustomerEmail($pdo, $_POST['customeremail'], $_POST['quote_id']); }
    if ($_POST['action'] == "get_customer_email") { getCustomerEmail($pdo, $_POST['quote_id']); }

    // edit discount & final
    if ($_POST['action'] == "edit_discount") { edit_discount($pdo, $_POST['quote_id'], $_POST['discount']); }
    if ($_POST['action'] == "edit_final") { edit_final($pdo, $_POST['quote_id']); }

    // display total & final prices
    if ($_POST['action'] == "get_total") { get_total($pdo, $_POST['quote_id']); }
    if ($_POST['action'] == "get_final") { get_final($pdo, $_POST['quote_id']); }
}

/**************************************************************/
/*                                                            */
/*    Functions for interacting with database quote table     */
/*                                                            */
/**************************************************************/

function get_all_quotes($pdo, $pdoLegacy) {
    $quoteList = quoteListAll($pdo);
    $associateList = associateListAll($pdo);

    for ($i = 0; $i < sizeof($quoteList); $i++) {
        $customerDetails = getCustomerDetails($pdoLegacy, $quoteList[$i]['customer_id'])[0];

        // find correct row $associateList with associate that matches associateID
        $associateName = "";
        for ($j = 0; $j < sizeof($associateList); $j++) {
          if ($associateList[$j]['id'] == $quoteList[$i]['associateID']) {
            $associateName = $associateList[$j]['username'];
            break;
          }
        }

        echo 
        "<tr>
          <th scope=row>" . $quoteList[$i]['quote_id'] . "</th>
          <td id=\"col-cust-name\">" . $customerDetails['name'] . "</td>
          <td>" . $customerDetails['city'] . "<br>" . $customerDetails['street'] . "</td>
          <td>" . $customerDetails['contact'] . "</td>
          <td>" . $quoteList[$i]['discount'] . "</td>
          <td>" . $quoteList[$i]['totalprice'] . "</td>
          <td id=\"col-assoc-name\">" . $associateName . "</td>
          <td id=\"col-status\">" . $quoteList[$i]['quotestatus'] . "</td>
          <td id=\"col-date\">" . $quoteList[$i]['datefiled'] . "</td>
          <td><button type=\"button\" class=\"btn btn-primary\" id=\"viewBtn\" data-bs-toggle=\"modal\"
            data-bs-target=\"#adminViewModal\">View</button></td>
        <tr>";
    }
}

function get_open_quotes($pdo, $pdoLegacy) {
    $id = $_SESSION['associateSession']['id'];
    $quoteList = quoteListAssociate($pdo, "open", $id);

    for ($i = 0; $i < sizeof($quoteList); $i++) {
        $customerName = getCustomerDetails($pdoLegacy, $quoteList[$i]['customer_id'])[0]['name'];
        echo 
        "<tr>
          <th scope=row>" . $quoteList[$i]['quote_id'] . "</th>
          <td> " . $customerName . "</td>
          <td> " . $quoteList[$i]['totalprice'] . "</td>
          <td> " . $quoteList[$i]['quotestatus'] . "</td>
          <td> " . $quoteList[$i]['datefiled'] . "</td>
          <td><button type=\"button\" class=\"btn btn-primary\" id=\"editBtn\" data-bs-toggle=\"modal\"
            data-bs-target=\"#editQuoteModal\">Edit</button></td>
          <td><button type=\"button\" class=\"btn btn-success\" id=\"finalizeBtn\" data-bs-toggle=\"modal\"
            data-bs-target=\"#finalizeQuoteModal\">Finalize Order</button></td>
        <tr>";
    }
}

function get_finalized_quotes($pdo, $pdoLegacy) {
    $quoteList = quoteListGivenType($pdo, "finalized");

    for ($i = 0; $i < sizeof($quoteList); $i++) {
        // variable holds all details for customer this quote is for
        $customerDeets = getCustomerDetails($pdoLegacy, $quoteList[$i]['customer_id'])[0];
        echo 
        "<tr>
          <th scope=row>" . $quoteList[$i]['quote_id'] . "</th>
          <td> " . $customerDeets['name'] . "</td>
          <td> " . $customerDeets['city'] . "<BR>" . $customerDeets['street'] . "</td>
          <td> " . $customerDeets['contact'] . "</td>
          <td> " . $quoteList[$i]['totalprice'] . "</td>
          <td> " . $quoteList[$i]['discount'] . "</td>
          <td> " . $quoteList[$i]['finalprice'] . "</td>
          <td> " . $quoteList[$i]['quotestatus'] . "</td>
          <td> " . $quoteList[$i]['datefiled'] . "</td>
          <td><button type=\"button\" class=\"btn btn-primary\" id=\"editBtn\" data-bs-toggle=\"modal\"
            data-bs-target=\"#editQuoteModal\">Edit</button></td>
          <td><button type=\"button\" class=\"btn btn-success\" id=\"sanctionBtn\" data-bs-toggle=\"modal\"
            data-bs-target=\"#sanctionQuoteModal\">Sanction</button></td>
        <tr>";
    }
}

function get_sanctioned_quotes($pdo, $pdoLegacy) {
    $quoteList = quoteListGivenType($pdo, "sanctioned");

    for ($i = 0; $i < sizeof($quoteList); $i++) {
        // variable holds all details for customer this quote is for
        $customerDeets = getCustomerDetails($pdoLegacy, $quoteList[$i]['customer_id'])[0];
        echo 
        "<tr>
          <th scope=row>" . $quoteList[$i]['quote_id'] . "</th>
          <td>" . $customerDeets['name'] . "</td>
          <td>" . $customerDeets['city'] . "
          <BR>" . $customerDeets['street'] . "</td>
          <td>" . $customerDeets['contact'] . "</td>
          <td>" . $quoteList[$i]['discount'] . "</td>
          <td>" . $quoteList[$i]['totalprice'] . "</td>
          <td>" . $quoteList[$i]['quotestatus'] . "</td>
          <td>" . $quoteList[$i]['datefiled'] . "</td>
          <td><button type=\"button\" class=\"btn btn-primary\" id=\"editBtn\" data-bs-toggle=\"modal\"
            data-bs-target=\"#editQuoteModal\">Edit</button></td>
          <td><button type=\"button\" class=\"btn btn-success\" id=\"sanctionBtn\" data-bs-toggle=\"modal\"
            data-bs-target=\"#processQuoteModal\">Process</button></td>
        <tr>";
    }
}

function edit_discount($pdo, $quoteid, $amount) {    
    // update discount in db
    $stmt = $pdo->prepare("UPDATE Quote SET discount=? WHERE quote_id=?");
    $stmt->execute([$amount, $quoteid]);
}

function get_total($pdo, $quoteid) {
    // retrieve the total price
    $totalprice = quoteGivenId($pdo, $quoteid)[0]['totalprice'];

    // display the total price
    echo "<p id=\"totalprice\" data-total-price=\"".$totalprice."\">Total Price: $totalprice</p>";
}

function get_final($pdo, $quoteid) {
  // retrieve the total price
  $finalprice = quoteGivenId($pdo, $quoteid)[0]['finalprice'];

  // display the final price
  echo "<p id=\"finalprice\" data-final-price=\"" . $finalprice . "\">Final Price: " . $finalprice . "</p>";
}

function edit_final($pdo, $quoteid) {
    // retrieve quote, get its totalprice & discount 
    $quote = quoteGivenId($pdo, $quoteid)[0];

    // calculate final price
    $finalprice = $quote['totalprice'] - $quote['discount'];

    // update discount in db
    $stmt = $pdo->prepare("UPDATE Quote SET finalprice=? WHERE quote_id=?");
    $stmt->execute([$finalprice, $quoteid]);
}

function add_quotes($pdo, $customer_id, $associateId) {
    // set timezone and grab current date
    date_default_timezone_set('America/Chicago');
    $today = date("Y-m-d H:i:s");

    // insert to db
    $stmt = $pdo->prepare("INSERT INTO Quote (customer_id, associateID, datefiled) VALUES (?, ?, ?)");
    $stmt->execute([$customer_id, $associateId, $today]);
}

function finalize_quotes($pdo, $quoteid) {
    $stmt = $pdo->prepare("UPDATE Quote SET quotestatus='finalized' WHERE quote_id=?");
    $stmt->execute([$quoteid]);
    edit_final($pdo, $quoteid);
}

function sanction_quotes($pdo, $quoteid) {
    $stmt = $pdo->prepare("UPDATE Quote SET quotestatus='sanctioned' WHERE quote_id=?");
    $stmt->execute([$quoteid]);
}

function process_quotes($pdo, $quoteid) {
    $stmt = $pdo->prepare("UPDATE Quote SET quotestatus='ordered' WHERE quote_id=?");
    $stmt->execute([$quoteid]);

    // grab the relative row for this quote
    $quote = quoteGivenId($pdo, $quoteid)[0];

    // encode json data
    $returnData = array(
      'order'=>$quote['quote_id'],
      'associate'=>$quote['associateID'],
      'custid'=>$quote['customer_id'],
      'amount'=>$quote['finalprice']
    );

    echo json_encode($returnData);
}


//Add customer email
function addCustomerEmail($pdo, $email, $quoteid){
  $stmt = $pdo->prepare("UPDATE Quote SET customeremail=? WHERE quote_id=?");
  $stmt->execute([$email, $quoteid]);
}

function getCustomerEmail($pdo, $quoteid){
  $email = quoteGivenId($pdo, $quoteid)[0]['customeremail'];
  echo $email;
}