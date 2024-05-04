<?php

require '/logistic-company/app/config/db.php';
require '/logistic-company/app/service/ShipmentService.php';
require '/logistic-company/app/service/http.php';
require '/logistic-company/app/service/authentication.php';
require '/logistic-company/app/service/UserService.php';

session_start();

if (!checkAuthentication() || !($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'employee')) {
    redirectToPath('/logistic-company/public/index.php');
    die("You don't have permission to edit or remove");
}

### Fetch connection to DB
$db_connection = getDB();

### If ID is not set, print error and exit script
if (isset($_GET['id'])) {

    $shipment = UserService::getShipment($db_connection, $_GET['id']);

    ## If ID is invalid, print error and exit script
    if ($shipment) {

        $id = $shipment['id'];

    } else {

        die("No shipment found");

    }

} else {

    die("ID not specified, no shipment found");

}

### Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    ## Delete query
    $prepared_query = mysqli_prepare($db_connection, "DELETE FROM shipment WHERE id = ?");

    ## Check for error in query
    if ( $prepared_query === false) {

        echo mysqli_error($db_connection);

    } else {
        
        # Handle quotes, escape characters, SQL injection etc.
        mysqli_stmt_bind_param($prepared_query, "i", $id);

        if (mysqli_stmt_execute($prepared_query)) {

            # Redirect to shipment page
            redirectToPath("/logistic-company/public" . "/index.php");
            
        } else {

            echo mysqli_stmt_error($prepared_query);

        }
    }

}

?>

<?php require '/logistic-company/app/view/header.php'; ?>

<h4> Remove shipment </h4>

<form method="post">
    <p>Are you sure you want to remove this shipment?</p>

    <button>Remove</button>

    <a href="/logistic-company/app/view/shipment.php?id=<?= $shipment['id']; ?>">Cancel</a>
</form>

<?php require '/logistic-company/app/view/footer.php'; ?>