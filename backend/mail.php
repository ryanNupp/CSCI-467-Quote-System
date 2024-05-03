<?php
include ("!query.php");

if ($_POST['action'] == "sanction_email") { sanction_email($pdo, $pdoLegacy, $_POST['quote_id']); }
if ($_POST['action'] == "process_email") { process_email($pdo, $pdoLegacy, $_POST['quote_id']); }

/***************************************/
/*                                     */
/*  Send email for sanctioned quotes   */
/*                                     */
/***************************************/
function sanction_email($pdo, $pdoLegacy, $quote_id) {
  // grab the relative information for this quote
  $quote = quoteGivenId($pdo, $_POST['quote_id'])[0];
  $customer = getCustomerDetails($pdoLegacy, $quote['customer_id'])[0];
  $items = getLineItems($pdo, $quote_id);
  $associateName = associateListGivenId($pdo, $quote['associateID'])[0]['username'];

  $stringLineItems = "";
  foreach ($items as $L) {
    $stringLineItems .= "  Description: " . $L['description'] . "  :  Price: " . $L['price'] . "\n";
  }
  // encode json data
  $returnData = array(
    'api_key' => 'REDACTED',
    'sender' => 'REDACTED',
    'to' => "[\'" . $quote['customeremail'] . "\']",
    'subject' => "Your Order # " . $quote_id . " was placed",
    'text_body' => "
        Your Order Information: \n\n
        Quote ID: " . $quote_id . "\n
        Name: " . $customer['name'] . "\n
        Address: " . $customer['street'] . ", " . $customer['city'] . "\n
        Contact: " . $customer['contact'] . "\n
        Line Items: \n" . $stringLineItems . "\n
        Running Total: " . $quote['totalprice'] . "\n
        Discount: " . $quote['discount'] . "\n
        Price: " . $quote['finalprice'] . "\n
        Associate ID: " . $quote['associateID'] . "\n
        Associate Name: " . $associateName . "\n
        Order Status: " . $quote['quotestatus'] . "\n
        Date Filled: " . $quote['datefiled'] . "\n"
  );

  echo json_encode($returnData);
}


/***************************************/
/*                                     */
/*   Send email for processed quotes   */
/*                                     */
/***************************************/
function process_email($pdo, $pdoLegacy, $quote_id) {
  // grab the relative information for this quote
  $quote = quoteGivenId($pdo, $_POST['quote_id'])[0];
  $customer = getCustomerDetails($pdoLegacy, $quote['customer_id'])[0];
  $items = getLineItems($pdo, $quote_id);
  $associateName = associateListGivenId($pdo, $quote['associateID'])[0]['username'];

  $stringLineItems = "";
  foreach ($items as $L) {
    $stringLineItems .= "  Description: " . $L['description'] . "  :  Price: " . $L['price'] . "\n";
  }



  // encode json data
  $returnData = array(
    'api_key' => 'REDACTED',
    'sender' => 'REDACTED',
    'to' => [ $quote['customeremail'] ],
    'subject' => "Your Order # " . $quote_id . " Was Processed!",
    'text_body' => "
        Your Order Information: \n\n
        Quote ID: " . $quote_id . "\n
        Name: " . $customer['name'] . "\n
        Address: " . $customer['street'] . ", " . $customer['city'] . "\n
        Contact: " . $customer['contact'] . "\n
        Line Items: \n" . $stringLineItems . "\n
        Running Total: " . $quote['totalprice'] . "\n
        Discount: " . $quote['discount'] . "\n
        Final Price: " . $quote['finalprice'] . "\n
        Associate ID: " . $quote['associateID'] . "\n
        Associate Name: " . $associateName . "\n
        Quote Status: " . $quote['quotestatus'] . "\n
        Date Filed: " . $quote['datefiled'] . "\n"
  );

  echo json_encode($returnData);
}