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

    // set the 
    document.getElementById('editModalTitleText').quoteid = quoteid;
    document.getElementById('editModalTitleText').innerHTML = "Edit Quote #" + quoteid;

    // Ajax call to get line items & secret notes data from the database
    viewLineItems(quoteid);
    viewSecretNotes(quoteid);
    display_total(quoteid);
    display_final(quoteid);
});

/**************************************/
/*                                    */
/*        Save changes for all        */
/*        items in quote edit         */
/*                                    */
/**************************************/

$('#saveButton').on('click', function () {
    // pull in the quote id relataive to the quote to save modifications to
    var quoteid = document.getElementById('editModalTitleText').quoteid;

    //save the discount and final price
    saveDiscount(quoteid);

    //refresh display
    viewLineItems(quoteid);
    viewSecretNotes(quoteid);
    display_total(quoteid);
    display_final(quoteid);
});

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
                    success: function () { }
                });
            }
        });
    }
}

/**************************************/
/*                                    */
/*              Displays              */
/*                                    */
/*                                    */
/**************************************/

// Makes AJAX call to PHP file to display line items
function viewLineItems(input) {
    $.ajax({
        type: "POST",
        url: "../backend/line_items.php",
        data: { 'action': "view_line_item", 'quote_id': input },
        success: function (output) {
            $('#line-items-table').html(output);
        }
    });
}

// Makes AJAX call to PHP file to display secret notes
function viewSecretNotes(input) {
    $.ajax({
        type: "POST",
        url: "../backend/secret_notes.php",
        data: { 'action': "view_note", 'quote_id': input },
        success: function (output) {
            $('#secret-notes-table').html(output);
        }
    });
}

// Display the total price
function display_total(input) {
    $.ajax({
        type: "POST",
        url: "../backend/quotes.php",
        data: { 'action': "get_total", 'quote_id': input },
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
        data: { 'action': "get_final", 'quote_id': input },
        success: function (output) {
            document.getElementById('display-final').innerHTML = output;
        }
    });
}



/**************************************/
/*                                    */
/*           Process Quote            */
/*                                    */
/**************************************/

$('#processQuoteModal').on('show.bs.modal', e => {
    // var button, set equal to the relative button that triggered the modal to display
    var button = $(e.relatedTarget);

    // map of data from the table row the delete button was pressed in
    var data = button.closest('tr').children().map(function () {
        return $(this).text();
    });

    // dynamically update modal title header with assoc id and name 
    document.getElementById("processModalTitleText").innerHTML = "Quote #" + data[0] + "  -  Customer: " + data[1];

    // set finalSanctionButton data-associd to be equal to the associate id from the table row
    document.getElementById("finalProcessButton").quoteid = data[0];
});


// if final process button clicked, process the order
$('#finalProcessButton').on('click', e => {
    // get the quote id to be processed
    var quoteid = document.getElementById("finalProcessButton").quoteid;

    // ajax call to change order to processed
    $.ajax({
        type: "POST",
        url: "../backend/quotes.php",
        data: { 'action': "process_quote", 'quote_id': quoteid },
        success: function (output) {
            let json = $.parseJSON(output);
            // ajax call to send order to external system
            $.ajax({
                type: "POST",
                url: "https://blitz.cs.niu.edu/PurchaseOrder/",
                data: json,
                success: function (json) {
                    let percent = json['commission']
                    let commission = json['amount'] * (Number(percent.slice(0, -1)) / 100);
                    $.ajax({
                        type: "POST",
                        url: "../backend/associate.php",
                        data: {
                            'action': "edit_associate_commission",
                            'id': json['associate'],
                            'commission': commission
                        },
                        success: function () { }
                    });
                }
            });
        }
    });

    $.ajax({
        type: "POST",
        url: "../backend/mail.php",
        data: {
            'action': "process_email",
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