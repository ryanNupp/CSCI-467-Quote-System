<?php 
include("!query.php");
session_start();

/************************************/
/*                                  */
/*     Call the proper function     */
/*                                  */
/************************************/

if(isset($_POST['action'])) {
    if ($_POST['action'] == "get_associate") { get_associates($pdo); }
    if ($_POST['action'] == "edit_associate") { edit_associates($pdo, $_POST['username'], $_POST['password'], $_POST['address'], $_POST['commission'], $_POST['id']); }
    if ($_POST['action'] == "add_associate") { add_associates($pdo, $_POST['username'], $_POST['password'], $_POST['address']); }
    if ($_POST['action'] == "delete_associate") { delete_associates($pdo, $_POST['id']); }
    if ($_POST['action'] == "edit_associate_commission") { editAssociateCommission($pdo, $_POST['id'], $_POST['commission']); }
}

/**************************************************************/
/*                                                            */
/*    Functions for interacting with database quote table     */
/*                                                            */
/**************************************************************/

function get_associates($pdo) {
    $associateList = associateListAll($pdo);

    for ($i = 0; $i < sizeof($associateList); $i++) {
        echo 
        "<tr>
          <th scope=row>" . $associateList[$i]['id'] . "</th>
          <td>" . $associateList[$i]['username'] . "</td>
          <td>" . $associateList[$i]['homeaddress'] . "</td>
          <td>" . $associateList[$i]['commission'] . "</td>
          <td><button type=\"button\" class=\"btn btn-primary\" id=\"editBtn\" data-bs-toggle=\"modal\"
            data-bs-target=\"#editModal\">Edit</button></td>
          <td><button type=\"button\" class=\"btn btn-danger\" id=\"deleteBtn\" data-bs-toggle=\"modal\"
            data-bs-target=\"#deleteModal\">Delete</button></td>
        </tr>";
    }
}

function edit_associates($pdo, $username, $password, $address, $commission, $id) {
    $stmt = $pdo->prepare("UPDATE Associate SET username = ?, password = ?, homeaddress = ?, commission = ? WHERE id = ?");
    $stmt->execute([$username, $password, $address, $commission, $id]);
}

function add_associates($pdo, $username, $password, $address) {
    $stmt = $pdo->prepare("INSERT INTO Associate (username, password, homeaddress, commission) VALUES (?, ?, ?, 0)");
    $stmt->execute([$username, $password, $address]);
    
}

function delete_associates($pdo, $id) {
    $stmt = "DELETE from Associate WHERE id = '$id'";
    $pdo->query($stmt);
}

function editAssociateCommission($pdo, $associateid, $newCommission) {
    $existingCommission = associateListGivenId($pdo, $associateid)[0]['commission'];

    $totalCommission = $existingCommission + $newCommission;

    $stmt2 = $pdo->prepare("UPDATE Associate SET commission = ? WHERE id = ?");
    $stmt2->execute([$totalCommission, $associateid]);
}