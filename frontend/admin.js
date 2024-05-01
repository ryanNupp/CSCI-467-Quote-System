/******************************************/
/*                                        */
/*      Edit Associate Functionality      */
/*                                        */
/******************************************/

// fill edit modal with necessary data
$('#editModal').on('show.bs.modal', e => {
    // button that opened the modal
    var button = $(e.relatedTarget);

    // map of data from the table row the edit button was pressed in
    var data = button.closest('tr').children().map(function () {
        return $(this).text();
    });

    // dynamically update modal title header with assoc id and name 
    document.getElementById("editModalTitleText").innerHTML = "Associate #" + data[0] + " : " + data[1];

    // store id into data-associd for the submission button
    document.getElementById("finalEditButton").associd = data[0];

    // placeholder username, address, and commission with existing 
    document.getElementById("edit-username").setAttribute("placeholder", data[1]);
    document.getElementById("edit-address").setAttribute("placeholder", data[2]);
    document.getElementById("edit-commission").setAttribute("placeholder", data[3]);
});

// edit relative associate upon submitting edit form
document.querySelector('#editAssociateForm').addEventListener('submit', (e) => {
    // prevent default submission
    e.preventDefault()
    
    // arrange form entries into data map 
    var data = Object.fromEntries(new FormData(e.target).entries());
    data["id"] = document.getElementById("finalEditButton").associd;
    data["action"] = "edit_associate";

    // ajax to send to php backend associate_edit file
    $.ajax({
        type: "POST",
        url: "../backend/associate.php",
        data: data,
        success: function () {
            // upon success running associate_edit.php, reset form & reload the page
            document.getElementById('editAssociateForm').reset();
            location.reload();
        }
    });
});

/********************************************/
/*                                          */
/*      Delete Associate Functionality      */
/*                                          */
/********************************************/

// fill delete modal with necessary data
$('#deleteModal').on('show.bs.modal', e => {
    // var button, set equal to the relative button that triggered the modal to display
    var button = $(e.relatedTarget);

    // map of data from the table row the delete button was pressed in
    var data = button.closest('tr').children().map(function () {
        return $(this).text();
    });

    // dynamically update modal title header with assoc id and name 
    document.getElementById("deleteModalTitleText").innerHTML = "Associate #" + data[0] + " : " + data[1];

    // set finalDeleteButton data-associd to be equal to the associate id from the table row
    document.getElementById("finalDeleteButton").associd = data[0];
});


// delete associate when final delete button gets pressed
$('#finalDeleteButton').on('click', e => {
    // pull associate id from delete button data
    var info = {
        'id': document.getElementById("finalDeleteButton").associd,
        'action': "delete_associate"
    }

    // ajax call to run php script to delete employee given id
    $.ajax({
        type: "POST",
        url: "../backend/associate.php",
        data: info,
        success: function () {
            // upon success running associate_delete.php, reload the page
            location.reload();
        }
    });
});

/*****************************************/
/*                                       */
/*      Add Associate Functionality      */
/*                                       */
/*****************************************/

// add associate upon the add associate form submission 
document.querySelector('#addAssociateForm').addEventListener('submit', (e) => {
    // prevent default submission
    e.preventDefault()
    
    // arrange form entries into data map 
    const data = Object.fromEntries(new FormData(e.target).entries());
    data['action'] = "add_associate";

    // ajax to send to php backend associate file
    $.ajax({
        type: "POST",
        url: "../backend/associate.php",
        data: data,
        success: function () {
            // upon success running associate_add.php, reset form & reload the page
            document.getElementById('addAssociateForm').reset();
            location.reload();
        }
    });
});
