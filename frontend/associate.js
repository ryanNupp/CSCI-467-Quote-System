/**************************************/
/*                                    */
/*   Add & Edit Quote Functionality   */
/*                                    */
/**************************************/

// for adding a quote, execute this upon form submission 
document.querySelector('#addQuoteForm').addEventListener('submit', (e) => {
    // prevent default submission
    e.preventDefault();
    
    // arrange form entries into data map 
    var data = {
        'action': "add_quote",
        'customer_id': $('#selectCustomer').find(':selected').attr('value'),
        'associateID': sessionId
    };

    if (data['customer_id'] != "default") {
        // ajax to send to php backend quote_add file
        $.ajax({
            type: "POST",
            url: "../backend/quotes.php",
            data: data,
            success: function () { 
                // upon success running associate_add.php reload the page
                location.reload();
            }
        });
    } else {
        alert('Please select a customer before filing.');
    }

    
});

// Upon the edit modal being shown, display the content & pull necessary data
$('#editQuoteModal').on('show.bs.modal', e => {
    // the edit button that triggered the modal to display
    var button = $(e.relatedTarget);

    // map of data from the table row the edit button was pressed in
    var data = button.closest('tr').children().map(function () {
        return $(this).text();
    });

    // grab the quote_id to be edited
    var quoteid = data[0];

    // place quote id into modal header
    document.getElementById('editModalTitleText').innerHTML = "Edit Quote #" + quoteid;

    // bind quote id to line items and secret notes display data for later use
    document.getElementById('line-items-display').quoteId = quoteid;
    document.getElementById('secret-notes-display').quoteId = quoteid;
    
    // Ajax call to get line items & secret notes data from the database
    getLineItems(quoteid);
    getSecretNotes(quoteid);
    display_total(quoteid);
    getCustomerEmail(quoteid);
});

function getCustomerEmail(quoteid) {
    $.ajax({
        type: "POST",
        url: "../backend/quotes.php",
        data: {'action': "get_customer_email", 'quote_id': quoteid},
        success: function (output) {
            document.getElementById('customer-email').placeholder = output;
        }
    });
}




// Display the total 
function display_total(input) {
    $.ajax({
        type: "POST",
        url: "../backend/quotes.php",
        data: {'action': "get_total", 'quote_id': input},
        success: function (output) {
            document.getElementById('display-total').innerHTML = output;
        }
    });
}

/**************************************/
/*                                    */
/*          Save changes for          */
/*         items in quote edit        */
/*                                    */
/**************************************/

function saveLineItems(quoteid) {
    // save the line items relative to its quote
    var lineItems = $('.lineitemgroup').serializeArray();
    for (var i = 0; i < lineItems.length; i = i + 3) { // TODO, IF WE WANT TO: potentially figure out how to chain multiple queries, this loop is probably bad for performance
        var item = {
            'action': "edit_line_item",
            'id': lineItems[i].value,
            'description': lineItems[i + 1].value,
            'price': lineItems[i + 2].value,
        };
        $.ajax({
            type: "POST",
            url: "../backend/line_items.php",
            data: item,
            success: function () {}
        });
    }
}

function saveSecretNotes(quoteid) {
    // save the secret notes relative to this quote
    var notes = $('.notegroup').serializeArray();
    for (var i = 0; i < notes.length; i = i + 2) { // TODO, IF WE WANT TO: potentially figure out how to chain multiple queries, this loop is probably bad for performance
        var ajax_data = {
            'action': "edit_note",
            'id': notes[i].value,
            'note': notes[i + 1].value
        };
        $.ajax({
            type: "POST",
            url: "../backend/secret_notes.php",
            data: ajax_data,
            success: function () {}
        });
    }
}

function saveCustomerEmail(quoteid){

    var customerEmail = $('#customer-email').val();

    if (customerEmail != "") {
        var ajax_data = {
            'action': "customer_email",
            'customeremail' : customerEmail,
            'quote_id': quoteid
        };
        $.ajax({
            type: "POST",
            url: "../backend/quotes.php",
            data: ajax_data,
            success: function () {}
        });
    }
}

$('#saveButton').on('click', function() {
    // quote id relative to the quote
    var quoteid = document.getElementById('line-items-display').quoteId;
    
    // save all changes
    saveLineItems(quoteid);
    saveSecretNotes(quoteid);
    saveCustomerEmail(quoteid);

    // refresh changes
    display_total(quoteid);
    getLineItems(quoteid);
    getSecretNotes(quoteid);
});

/**************************************/
/*                                    */
/*        Line Items Handling         */
/*        Display, Add, Delete        */
/*                                    */
/**************************************/

// Makes AJAX call to PHP file to display line items
function getLineItems(input) {
    $.ajax({
        type: "POST",
        url: "../backend/line_items.php",
        data: {'action': "get_line_item" , 'quote_id': input},
        success: function (output) {
            document.getElementById('line-items-display').innerHTML = output;
        }
    });
}

// Add line item
$('#addLineItemButton').on('click', function() {
    // grab the id of the quote the line item is related to
    var quoteid = document.getElementById('line-items-display').quoteId;

    // use php script to delete lineItem from the database table
    $.ajax({
        type: "POST",
        url: "../backend/line_items.php",
        data: {'action': "add_line_item", 'quote_id': quoteid},
        success: function () {
            // now upon successful deletion refresh the modal
            getLineItems(quoteid);
        }
    });
});

// Delete line item
$('#editQuoteModal').on('click', '#deleteLineItemButton', function() {
    // grab quote id for the quote related to the line item
    var quoteid = document.getElementById('line-items-display').quoteId;

    // grab the id from the line item to be deleted
    var lineItemId = $(this).data('lineItemId');

    // use php script to delete lineItem from the database table
    $.ajax({
        type: "POST",
        url: "../backend/line_items.php",
        data: {'action': "delete_line_item" , 'id': lineItemId},
        success: function () {
            // now upon successful deletion refresh the modal
            var quoteid = document.getElementById('line-items-display').quoteId;
            getLineItems(quoteid);
        }
    });
});

/**************************************/
/*                                    */
/*       Secret Notes Handling:       */
/*        Display, Add, Delete        */
/*                                    */
/**************************************/

// Makes AJAX call to PHP file to display line items
function getSecretNotes(input) {
    $.ajax({
        type: "POST",
        url: "../backend/secret_notes.php",
        data: {'action': "get_note" , 'quote_id': input},
        success: function (output) {
            document.getElementById('secret-notes-display').innerHTML = output;
        }
    });
}

// Add secret note
$('#addSecretNoteButton').on('click', function() {
    // grab quote id relative to secret note to be deleted
    var quoteid = document.getElementById('secret-notes-display').quoteId;

    // use php script to delete lineItem from the database table
    $.ajax({
        type: "POST",
        url: "../backend/secret_notes.php",
        data: {'action': "add_note", 'quote_id': quoteid},
        success: function () {
            // now upon successful deletion refresh the modal
            getSecretNotes(quoteid);
        }
    });
});

// Delete secret note
$('#editQuoteModal').on('click', '#deleteSecretNoteButton', function() {
    // grab the id of the quote the note is related to
    var quoteid = document.getElementById('secret-notes-display').quoteId;

    // grab the id of the note to be deleted
    var noteId = $(this).data('noteId')

    // use php script to delete note from the database table
    $.ajax({
        type: "POST",
        url: "../backend/secret_notes.php",
        data: {'action': "delete_note" , 'id': noteId},
        success: function () {
            // now upon successful deletion refresh the modal
            getSecretNotes(quoteid);
        }
    });
});

/**************************************/
/*                                    */
/*           Finalize Quote           */
/*                                    */
/**************************************/

$('#finalizeQuoteModal').on('show.bs.modal', e => {
    // var button, set equal to the relative button that triggered the modal to display
    var button = $(e.relatedTarget);

    // map of data from the table row the delete button was pressed in
    var data = button.closest('tr').children().map(function () {
        return $(this).text();
    });

    // dynamically update modal title header with assoc id and name 
    document.getElementById("finalizeModalTitleText").innerHTML = "Quote #" + data[0] + "  -  Customer: " + data[1];

    // set finalFinalizeButton data-associd to be equal to the associate id from the table row
    document.getElementById("finalFinalizeButton").quoteid = data[0];
});


// if final finalize button pressed, delete that shit brah
$('#finalFinalizeButton').on('click', e => {
    // quote id
    quoteId = document.getElementById("finalFinalizeButton").quoteid;

    // finalize the quote, also initialize the final price for HQ page
    $.ajax({
        type: "POST",
        url: "../backend/quotes.php",
        data: { 'action': "finalize_quote", 'quote_id': quoteId },
        success: function () {
            // upon success running associate_delete.php, reload the page
            location.reload();
        }
    });
});