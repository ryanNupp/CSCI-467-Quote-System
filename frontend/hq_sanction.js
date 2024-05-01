// upon any modals closing, reset all forms and reload the location
$('.modal').on('hidden.bs.modal', function() {
    $('.modal input').each(function () {
        $(this).val('');
    });
    location.reload();
});

/**************************************/
/*                                    */
/*      Edit Quote Functionality      */
/*                                    */
/**************************************/

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

    // ??? gotta remember why I did this tbh
    document.getElementById('line-items-display').quoteId = quoteid;
    document.getElementById('secret-notes-display').quoteId = quoteid;

    // Ajax call to get line items & secret notes data from the database
    getLineItems(quoteid);
    getSecretNotes(quoteid);
    display_total(quoteid);
    display_final(quoteid);
});

// Upon exiting quote modal, reset the form. 
$('#editQuoteModal').on('hidden.bs.modal', e => {
    //    TODO
});

// Display the total price
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

// Display the final price 
function display_final(input) {
    $.ajax({
        type: "POST",
        url: "../backend/quotes.php",
        data: {'action': "get_final", 'quote_id': input},
        success: function (output) {
            document.getElementById('display-final').innerHTML = output;
        }
    });
}

/**************************************/
/*                                    */
/*        Save changes for all        */
/*        items in quote edit         */
/*                                    */
/**************************************/

function saveDiscount(quoteid) {
    // grab user input discount
    var discount;
    var totalprice = $('#totalprice').attr('data-total-price');

    var bypass_ajax = false;
    if (document.getElementById('amount-radio').checked) {
        discount = document.getElementById('discount-amount').value
    } else if (document.getElementById('percent-radio').checked) {
        discount = ((document.getElementById('discount-percent').value) * totalprice) / 100;
        
    } else {
        bypass_ajax = true;
    }

    if (!bypass_ajax) {
        var info = {
            'action': "edit_discount",
            'quote_id': quoteid,
            'discount': discount
        };
        $.ajax({
            type: "POST",
            url: "../backend/quotes.php",
            data: info,
            success: function () {
                $.ajax({
                    type: "POST",
                    url: "../backend/quotes.php",
                    data: { 'action': "edit_final", 'quote_id': quoteid },
                    success: function () {}
                });
            }
        });
    }
    
}

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

$('#saveButton').on('click', function () {
    // pull in the quote id relataive to the quote to save modifications to
    var quoteid = document.getElementById('line-items-display').quoteId;

    // save all related items
    saveLineItems(quoteid);
    saveSecretNotes(quoteid);

    //save the discount and final price
    saveDiscount(quoteid);


    // refresh display
    display_total(quoteid);
    display_final(quoteid);
    getLineItems(quoteid);
    getSecretNotes(quoteid);
});

/**************************************/
/*                                    */
/*        Line Items Handling:        */
/*        Display, Add, Delete        */
/*                                    */
/**************************************/

// Makes AJAX call to PHP file to display line items
function getLineItems(input) {
    $.ajax({
        type: "POST",
        url: "../backend/line_items.php",
        data: { 'action': "get_line_item", 'quote_id': input },
        success: function (output) {
            document.getElementById('line-items-display').innerHTML = output;
        }
    });
}

// Add line item
$('#addLineItemButton').on('click', function () {
    // grab the id of the quote the line item is related to
    var quoteid = document.getElementById('line-items-display').quoteId;

    // use php script to delete lineItem from the database table
    $.ajax({
        type: "POST",
        url: "../backend/line_items.php",
        data: { 'action': "add_line_item", 'quote_id': quoteid },
        success: function () {
            getLineItems(quoteid);
        }
    });
});

// Delete line item
$('#editQuoteModal').on('click', '#deleteLineItemButton', function () {
    // grab quote id for the quote related to the line item
    var quoteid = document.getElementById('secret-notes-display').quoteId;

    // grab the id from the line item to be deleted
    var lineItemId = $(this).data('lineItemId');

    // use php script to delete lineItem from the database table
    $.ajax({
        type: "POST",
        url: "../backend/line_items.php",
        data: { 'action': "delete_line_item", 'id': lineItemId },
        success: function () {
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
        data: { 'action': "get_note", 'quote_id': input },
        success: function (output) {
            document.getElementById('secret-notes-display').innerHTML = output;
        }
    });
}

// Add secret note
$('#addSecretNoteButton').on('click', function () {
    // grab quote id relative to secret note to be deleted
    var quoteid = document.getElementById('secret-notes-display').quoteId;

    // use php script to delete lineItem from the database table
    $.ajax({
        type: "POST",
        url: "../backend/secret_notes.php",
        data: { 'action': "add_note", 'quote_id': quoteid },
        success: function () {
            // now upon successful deletion refresh the modal
            getSecretNotes(quoteid);
        }
    });
});

// Delete secret note
$('#editQuoteModal').on('click', '#deleteSecretNoteButton', function () {
    // grab the id of the quote the note is related to
    var quoteid = document.getElementById('secret-notes-display').quoteId;

    // grab the id of the note to be deleted
    var noteId = $(this).data('noteId')

    // use php script to delete note from the database table
    $.ajax({
        type: "POST",
        url: "../backend/secret_notes.php",
        data: { 'action': "delete_note", 'id': noteId },
        success: function () {
            // now upon successful deletion refresh the modal
            getSecretNotes(quoteid);
        }
    });
});

/**************************************/
/*                                    */
/*           Sanction Quote           */
/*                                    */
/**************************************/

$('#sanctionQuoteModal').on('show.bs.modal', e => {
    // var button, set equal to the relative button that triggered the modal to display
    var button = $(e.relatedTarget);

    // map of data from the table row the delete button was pressed in
    var data = button.closest('tr').children().map(function () {
        return $(this).text();
    });

    // dynamically update modal title header with assoc id and name 
    document.getElementById("sanctionModalTitleText").innerHTML = "Quote #" + data[0] + "  -  Customer: " + data[1];

    // set finalSanctionButton data-associd to be equal to the associate id from the table row
    document.getElementById("finalSanctionButton").quoteid = data[0];
});

// if final delete button pressed, delete that shit brah
$('#finalSanctionButton').on('click', e => {
    // pull the curr associate id & pass thru ajax to GET, process with delete.php
    var info = {
        'action': "sanction_quote",
        'quote_id': document.getElementById("finalSanctionButton").quoteid
    }
    // ajax call to run php script to delete employee given id
    $.ajax({
        type: "POST",
        url: "../backend/quotes.php",
        data: info,
        success: function () {
            $('#finalSanctionButton').modal('hide');
        }
    });

    $.ajax({
        type: "POST",
        url: "../backend/mail.php",
        data: {
            'action': "sanction_email",
            'quote_id': quoteid
        },
        success: function (output) {
            console.log(output);
            console.log($.parseJSON(output));
            console.log(JSON.stringify($.parseJSON(output)));

            // send the email using the data
            const options = {
                method: 'POST',
                headers: { accept: 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify($.parseJSON(output))
            };

            fetch('https://api.smtp2go.com/v3/email/send', options)
                .then(response => response.json())
                .then(response => console.log(response))
                .catch(err => console.error(err));
        }
    });
});

