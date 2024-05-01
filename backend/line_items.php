<?php 
include("!query.php");
session_start();

/************************************/
/*                                  */
/*     Call the proper function     */
/*                                  */
/************************************/

if(isset($_POST['action'])) {
    if ($_POST['action'] == "get_line_item") { get_line_items($pdo, $_POST['quote_id']); }
    if ($_POST['action'] == "view_line_item") { view_line_items($pdo, $_POST['quote_id']); }
    if ($_POST['action'] == "edit_line_item") { edit_line_items($pdo, $_POST['id'], $_POST['description'], $_POST['price']); }
    if ($_POST['action'] == "delete_line_item") { delete_line_items($pdo, $_POST['id']); }
    if ($_POST['action'] == "add_line_item") { add_line_items($pdo, $_POST['quote_id']); }
}

/***************************************************************/
/*                                                             */
/*     Functions for interactinv with line items database      */
/*                                                             */
/***************************************************************/

function get_line_items($pdo, $quoteId) {
    $lineItemList = getLineItems($pdo, $quoteId);
    $totalprice = 0;
    for ($i = 0; $i < sizeof($lineItemList); $i++) {
        echo " 
        <form class=\"lineitemgroup input-group\" id=\"line-item-form\">
          <input type=\"hidden\" name=\"lineItemId\" value=\"" . $lineItemList[$i]['id'] . "\">
          <input type=\"text\" class=\"form-control\" id=\"\" name=\"lineItemDescription\" value = \"" . $lineItemList[$i]['description'] . "\">
          <input type=\"number\" step=\".01\" class=\"form-control\" id=\"\" name=\"lineItemPrice\" value = \"" . $lineItemList[$i]['price'] . "\">
          <button type=\"button\" class=\"btn btn-outline-danger\" id=\"deleteLineItemButton\" data-line-item-id=\"" . $lineItemList[$i]['id'] . "\">Delete</button>
        </form>";

        $totalprice += $lineItemList[$i]['price'];
    }
    $stmt = $pdo->prepare("UPDATE Quote Set totalprice = ? WHERE quote_id = ?");
    $stmt->execute([$totalprice, $quoteId]);
}

function view_line_items($pdo, $quoteId) {
    $lineItemList = getLineItems($pdo, $quoteId);
    for ($i = 0; $i < sizeof($lineItemList); $i++) {
        echo "
        <tr>
          <td>" . $lineItemList[$i]['description'] . "</td>
          <td>" . $lineItemList[$i]['price'] . "</td>
        <tr>";
    }
}

function edit_line_items($pdo, $id, $description, $price) {    
    $stmt = $pdo->prepare("UPDATE LineItems SET description=?, price=? WHERE id=?");
    $stmt->execute([$description, $price, $id]);
}

function delete_line_items($pdo, $id) {
    $pdo->query("DELETE from LineItems WHERE id = '$id'");
}

function add_line_items($pdo, $quoteId) {
    $stmt = $pdo->prepare("INSERT INTO LineItems (description, price, quote_id) VALUES (?, ?, ?)");
    $stmt->execute(["Line Item Name", 0, $quoteId]);
}