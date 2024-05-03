<?php

require  __DIR__ . '/../config/db.php';
require  __DIR__ . '/../service/shipment-funs.php';
require  __DIR__ . '/../service/http.php';
require  __DIR__ . '/../service/authentication.php';

session_start();

if (!checkAuthentication()) {
    die("You don't have permission to edit or remove");
}

### Fetch connection to DB
$db_connection = getDB();

### If ID is not set, print error and exit script
if (isset($_GET['id'])) {

    $shipment = getShipment($db_connection, $_GET['id']);

    ## If ID is invalid, print error and exit script
    if ($shipment) {

        $id = $shipment['id'];
        $title = $shipment['title'];
        $body = $shipment['body'];
        $time_of = $shipment['time_of'];
        $created_by = $shipment['created_by'];

        if ($_SESSION['username'] != "admin" && $_SESSION['username'] != $shipment['created_by']) {

            die("You don't have permission to edit or remove");

        }

    } else {

        die("No shipment found");

    }

} else {

    die("ID not specified, no shipment found");

}

### Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $body = $_POST['body'];
    $time_of = $_POST['time_of'];

    $errors = getShipmentErrs($title, $body, $time_of);

    ## Check for errors in form
    if(empty($errors)) {

        ## Update query
        $prepared_query = mysqli_prepare($db_connection, "UPDATE shipment SET title = ?, body = ?, time_of = ? WHERE id = ?");

        ## Check for error in query
        if ( $prepared_query === false) {

            echo mysqli_error($db_connection);

        } else {
            
            if ($time_of == '') {
                $time_of = null;
            }

            # Handle quotes, escape characters, SQL injection etc.
            mysqli_stmt_bind_param($prepared_query, "sssi", $title, $body, $time_of, $id);

            if (mysqli_stmt_execute($prepared_query)) {

                # Redirect to shipment page
                redirectToPath("/logistic-company/app/view" . "/shipment.php?id=$id");
                
            } else {

                echo mysqli_stmt_error($prepared_query);

            }
        }

    }

}

?>

<?php require  __DIR__ . '/../view/header.php'; ?>

<h4> Shipment information </h4>

<?php require  __DIR__ . '/../view/shipment-form.php'; ?>

<?php require  __DIR__ . '/../view/footer.php'; ?>