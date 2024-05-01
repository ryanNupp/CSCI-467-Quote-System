<?php
include("!query.php");

$customerList = customerList($pdoLegacy);

echo "<option value=\"default\">Select a Customer</option>";
for ($i = 0; $i < sizeof($customerList); $i++) {
    echo "
    <option 
        value=\"" . $customerList[$i]['id'] . "\"
        name=\"" . $customerList[$i]['name'] . "\"
    >" .  $customerList[$i]['name'] . "</option>";
}