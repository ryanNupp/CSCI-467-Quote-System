<!DOCTYPE html>
<html lang="en">

<?php
include ('../backend/!query.php');
session_start();
$currLogin = $_SESSION['associateSession'];
?>

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

  <title>Associate Access</title>
  <link rel="icon" href="../images/favicon.ico">

  <script>
    var sessionId = "<?php echo $currLogin['id']; ?>";
    var sessionIdInt = <?php echo $currLogin['id']; ?>;
  </script>
</head>

<body data-bs-theme="dark">
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a href="#" class="navbar-brand">Associate</a>
      <span id="session-name" data-id-to-js=""><?php echo $currLogin['username']; ?></span>
      <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Log Out</a></li>
      </ul>
    </div>
  </nav>

  <!-- Select a customer, create a new quote  -->
  <div class="container my-5">
    <h1>Quote Creation</h1>
    <form method="POST" class="row g-3" id="addQuoteForm">
      <div class="col-auto">
        <h4>Select Customer</h4>
      </div>
      <div class="col-auto">
        <select name="selectCustomer" id="selectCustomer" class="form-select row">
          <script>
            $(document).ready(function () {
              $("#selectCustomer").load("../backend/customer_select.php");
            });
          </script>
        </select>
      </div>
      <div class="col-auto">
        <input type="submit" name="newQuote" value="New Quote" class="btn btn-primary">
      </div>
    </form>
  </div>
  
  <!-- Table to display all quotes made by current associate, Edit Quote & Finalize buttons which lead to modals below -->
  <div class="container my-5">
    <h1>List of Open Quotes</h1>
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Quote ID</th>
          <th scope="col">Customer Name</th>
          <th scope="col">Total Price</th>
          <th scope="col">Quote Status</th>
          <th scope="col">Date Filed</th>
          <th scope="col"></th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody id="openQuotes">
        <script id="listOfQuotes">
          $(document).ready(function () {
            $("#openQuotes").load("../backend/quotes.php", { 'action': "get_open_quote" });
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
          <p>Line Items</p>
          <div id="line-items-display" data-quote-id=""></div>
          <button class="btn btn-primary" id="addLineItemButton" type="button">Add New Line Item</button>

          <p> Secret Notes:</p>
          <div id="secret-notes-display" data-quote-id=""></div>
          <button class="btn btn-primary" id="addSecretNoteButton" type="button">Add New Secret Note</button>

          <div class="col-auto mb-2">
            <label for="customeremail" class="form-label">Customer Email:</label>
            <input type="text" class="form-control" id="customer-email" name="customeremail" placeholder="">
          </div>

          <div id="display-total"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="saveButton">Save</input>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal popup to confirm finalization of a quote -->
  <div class="modal fade" id="finalizeQuoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="finalizeModalTitleText">Quote Finalization</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h5>Are you sure you wish to finalize this quote?</h5>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" id="finalFinalizeButton" data-quoteid="">Finalize</button>
        </div>
      </div>
    </div>
  </div>

</body>

<!-- Link associate.js -->
<script src="associate.js"></script>

</html>