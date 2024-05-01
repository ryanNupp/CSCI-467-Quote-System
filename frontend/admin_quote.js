/*****************************************/
/*                                       */
/*      Search Filter Functionality      */
/*                                       */
/*****************************************/

$('.input-daterange input').each(function () {
    $(this).datepicker({
        format: 'yyyy-mm-dd'
    });
});

// search table upon submitting the form for searching table
$('#searchForm').submit( function(e) {
    e.preventDefault();
    
    // user input search variables
    var custName = $('#searchCust').val();
    var rowAssoc = $('#searchAssoc').val();
    var statusSelect = document.getElementById('searchStatus').value;
    var dates = $('.input-daterange').children('input').map(function() {
        return $(this).val();
    });

    // default date values (if dates are left blank)
    var startDate = new Date(1, 1, 1);
    var endDate = new Date(4000, 1, 1);

    // if user gave input for both dates, overwrite default dates with these
    if (dates[0] != "" && dates[1] != "") {
        let startDateParts = dates[0].split('-');
        startDate = new Date(startDateParts[0], startDateParts[1] - 1, startDateParts[2]); 
        let endDateParts = dates[1].split('-');
        endDate = new Date(endDateParts[0], endDateParts[1] - 1, endDateParts[2]); 
    }

    // go through every table row and display if matches users search terms
    $("#quote-table tr").each( function(rowindex) {
        if (rowindex != 0) {
            $row = $(this);

            // row variables
            var rowCustomer = $row.find("#col-cust-name").text();
            var rowAssociate = $row.find("#col-assoc-name").text();
            var rowStatus = $row.find("#col-status").text();

            // row date
            var rowDateParts = $row.find("#col-date").text().substr(0,10).split('-');
            var rowDate = new Date(rowDateParts[0], rowDateParts[1] - 1, rowDateParts[2]);

            // logic to see if row matches user input search filters
            let dateGood = (startDate <= rowDate && rowDate <= endDate) ? true : false;
            let rowCustGood = (rowCustomer.indexOf(custName) == 0 || custName == "") ? true : false;
            let rowAssocGood = (rowAssociate.indexOf(rowAssoc) == 0 || rowAssoc == "") ? true : false;
            let rowStatusGood = (rowStatus.indexOf(statusSelect) == 0 || statusSelect == "") ? true : false;
            
            // if all four search filters went through, show the row. if not, hide the row.
            (dateGood && rowCustGood && rowAssocGood && rowStatusGood) ? $row.show() : $row.hide();
        }
    });
});

/**************************************/
/*                                    */
/*      View Quote Functionality      */
/*                                    */
/**************************************/

$('#adminViewModal').on('show.bs.modal', e => {
    // the edit button that triggered the modal to display
    var button = $(e.relatedTarget);

    // map of data from the table row the edit button was pressed in
    var data = button.closest('tr').children().map(function () {
        return $(this).text();
    });

    // grab the quote_id to be edited
    var quoteid = data[0];
    
    // Ajax call to get line items & secret notes data from the database
    viewLineItems(quoteid);
    viewSecretNotes(quoteid);
});

/**************************************/
/*                                    */
/*        Line Items Handling:        */
/*              Display               */
/*                                    */
/**************************************/

// Makes AJAX call to PHP file to display line items
function viewLineItems(input) {
    $.ajax({
        type: "POST",
        url: "../backend/line_items.php",
        data: {'action': "view_line_item" , 'quote_id': input},
        success: function (output) {
            $('#line-items-table').html(output);
        }
    });
}

/**************************************/
/*                                    */
/*       Secret Notes Handling:       */
/*             Display                */
/*                                    */
/**************************************/

// Makes AJAX call to PHP file to display secret notes
function viewSecretNotes(input) {
    $.ajax({
        type: "POST",
        url: "../backend/secret_notes.php",
        data: {'action': "view_note" , 'quote_id': input},
        success: function (output) {
            $('#secret-notes-table').html(output);
        }
    });
}