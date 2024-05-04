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
        $deliver_from_user_id = $shipment['deliver_from_user_id'];
        $deliver_to_user_id = $shipment['deliver_to_user_id'];

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

<?php require  __DIR__ . '/logistic-company/app/view/header.php'; ?>

<h4> Shipment information </h4>

<?php require  __DIR__ . '/logistic-company/app/view/shipment-form.php'; ?>

<?php require  __DIR__ . '/logistic-company/app/view/footer.php'; ?>