<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- This includes the CSS & JS files for Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

  <!-- This includes the JS file for jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

  <title>HQ Access</title>
  <link rel="icon" href="../images/favicon.ico">
</head>

<body data-bs-theme="dark">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a href="#" class="navbar-brand">HQ</a>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="hq_sanction.php">Sanction Quotes</a></li>
        <li class="nav-item"><a class="nav-link" href="hq_process.php">Process Quotes</a></li>
      </ul>
      <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Log Out</a></li>
      </ul>
    </div>
  </nav>

  <!-- Container for main body content -->
  <div class="container my-5">
    <!-- Table to display all quotes made by current associate, with Edit Quote button for popup edit menu -->
    <h1>List of all Finalized Quotes</h1>
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Quote ID</th>
          <th scope="col">Customer Name</th>
          <th scope="col">Customer Address</th>
          <th scope="col">Customer Contact</th>
          <th scope="col">Total Price</th>
          <th scope="col">Discount</th>
          <th scope="col">Final Price</th>
          <th scope="col">Quote Status</th>
          <th scope="col">Date Filed</th>
          <th scope="col"></th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody id="finalizedQuotes">
        <script id="listOfQuotes">
          $(document).ready(function () {
            $("#finalizedQuotes").load("../backend/quotes.php", { 'action': "get_finalized_quote" });
          });
        </script>
      </tbody>
    </table>
  </div>

  <!-- Modal popup to edit the information of a quote -->
  <div class="modal fade" id="editQuoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalTitleText">Edit Quote</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container my-5">
            <p>Line Items</p>
            <button class="btn btn-primary mb-2" id="addLineItemButton" type="button">Add New Line Item</button>
            <div class="my-2" id="line-items-display" data-quote-id=""></div>
            <div class="mt-2" id="display-total">loading total price...</div>
          </div>

          <div class="container my-5">
            <p> Secret Notes:</p>
            <button class="btn btn-primary mb-2" id="addSecretNoteButton" type="button">Add New Secret Note</button>
            <div class="my-2" id="secret-notes-display" data-quote-id=""></div>
          </div>

          <!-- Discount -->
          <div class="conatiner mx-auto p-2" style="width: 50%">
            <p>Discount:</p>

            <div class="input-group">
              <div class="input-group-text mb2">
                <input class="form-check-input mt-0" type="radio" name="radio" id="amount-radio" value="">
              </div>
              <span class="input-group-text">$</span>
              <input type="number" step=".01" class="form-control" id="discount-amount" name="discount-amount"
                placeholder="">
            </div>
            <div class="input-group">
              <div class="input-group-text mb2">
                <input class="form-check-input mt-0" type="radio" name="radio" id="percent-radio" value="">
              </div>
              <input type="number" step=".01" class="form-control" id="discount-percent" name="discount-percent"
                placeholder="">
              <span class="input-group-text">%</span>

            </div>
            <style>
              input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
                -webkit-appearance: none; 
                margin: 0;
              }
              input[type=number] {
                -moz-appearance: textfield;
              }
            </style>

          </div>

          <div class="mt-2" id="display-final">loading final price...</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="saveButton">Save</input>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal popup to confirm sanctioning a quote -->
  <div class="modal fade" id="sanctionQuoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="sanctionModalTitleText">Sanction Quote</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h5>Are you sure you wish to sanction this quote?</h5>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" id="finalSanctionButton" data-quoteid="">Sanction</button>
        </div>
      </div>
    </div>
  </div>

</body>

<!-- Link associate.js -->
<script src="hq_sanction.js"></script>

</html>