<?php 
include("!query.php");
session_start();

/************************************/
/*                                  */
/*     Call the proper function     */
/*                                  */
/************************************/

if(isset($_POST['action'])) {
    if ($_POST['action'] == "get_note") { get_notes($pdo, $_POST['quote_id']); }
    if ($_POST['action'] == "view_note") { view_notes($pdo, $_POST['quote_id']); }
    if ($_POST['action'] == "edit_note") { edit_notes($pdo, $_POST['note'], $_POST['id']); }
    if ($_POST['action'] == "delete_note") { delete_notes($pdo, $_POST['id']); }
    if ($_POST['action'] == "add_note") { add_notes($pdo, $_POST['quote_id'], $_POST['note']); }
}

/***********************************************************/
/*                                                         */
/*     The function for secret note database functions     */
/*                                                         */
/***********************************************************/

function get_notes($pdo, $quoteId) {
    $secretNoteList = getSecretNotes($pdo, $quoteId);
    for ($i = 0; $i < sizeof($secretNoteList); $i++) {
              echo " 
              <form class=\"notegroup input-group\" id=\"note-form\">
                <input type=\"hidden\" name=\"lineItemId\" value=\"" . $secretNoteList[$i]['id'] . "\">
                <input type=\"text\" name=\"secretNoteContent\" class=\"form-control\" value=\"" . $secretNoteList[$i]['note'] . "\">
                <button type=\"button\" class=\"btn btn-outline-danger\" id=\"deleteSecretNoteButton\" data-note-id=\"" . $secretNoteList[$i]['id'] . "\">Delete</button>
              </form>";
    }
}

function view_notes($pdo, $quoteId) {
    $secretNoteList = getSecretNotes($pdo, $quoteId);
    for ($i = 0; $i < sizeof($secretNoteList); $i++) {
        echo "
            <tr>
                <td>" . $secretNoteList[$i]['note'] . "</td>
            <tr>
        ";
    }
}

function edit_notes($pdo, $note, $id) {    
    $stmt = $pdo->prepare("UPDATE SecretNotes SET note=? WHERE id=?");  
    $stmt->execute([$note, $id]);
}

function delete_notes($pdo, $id) {
    $pdo->query("DELETE from SecretNotes WHERE id = '$id'");
}

function add_notes($pdo, $quoteId, $note) {
    $stmt = $pdo->prepare('INSERT INTO SecretNotes (quote_id, note) VALUES (?, ?)');
    $stmt->execute([$quoteId, $note]);
}