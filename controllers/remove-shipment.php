<?php

session_start();

require '../includes/db.php';
require '../includes/shipment-funs.php';
require '../includes/http.php';
require '../includes/authentication.php';

if (!checkAuthentication()) {
    die("You don't have permission to edit or remove");
}

### Fetch connection to DB
$db_connection = getDB();

### If ID is not set, print error and exit script
if (!isset($_GET['id'])) {
    die("ID not specified, no shipment found");
}

$shipment = getShipment($db_connection, $_GET['id']);

### If ID is invalid or the shipment does not exist, print error and exit script
if ($shipment) {
    $id = $shipment['id'];

    if  ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "employee") {
        die("You don't have permission to edit or remove");
    }
} else {
    die("No shipment found");
}

### Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    ## Delete query
    $prepared_query = mysqli_prepare($db_connection, "DELETE FROM shipment WHERE id = ?");

    ## Check for error in query
    if ($prepared_query === false) {
        echo mysqli_error($db_connection);
    } else {
        mysqli_stmt_bind_param($prepared_query, "i", $id);
        if (mysqli_stmt_execute($prepared_query)) {
            # Redirect to main shipments page
            redirectToPath("/logistic-company/index.php");
        } else {
            echo mysqli_stmt_error($prepared_query);
        }
    }
}

?>

<?php require '../includes/header.php'; ?>

<h4> Remove Shipment </h4>

<form method="post">
    <p>Are you sure you want to remove this shipment?</p>
    <button class="light-green-hover" type="submit">Remove</button>
</form>
<br>
<div class="button-container">
<a class="cancel-link" href="/logistic-company/views/shipment.php?id=<?= $id; ?>">Cancel</a>
</div>
<?php require '../includes/footer.php'; ?>
