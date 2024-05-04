<?php 

session_start();

require '../includes/db.php';
require '../includes/shipment-funs.php';
require '../includes/http.php';
require '../includes/authentication.php';

if (!checkAuthentication()) {
    die ("Not logged in");
}

### Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    ## Prepare default or form values
    $statusShipment = $_POST['status_shipment'] ?? 'New';
    $shipWeight = $_POST['ship_weight'] ?? 0.00;
    $passengerAmount = $_POST['passenger_amount'] ?? 0;
    $dateSent = $_POST['date_sent'] ?? date('Y-m-d H:i:s'); 
    $dateSent = date('Y-m-d\TH:i', strtotime($dateSent));
    $deliverFromUserId = $_SESSION['user_id'];
    $deliverToUserId = $_POST['deliver_to_user_id'] ?? null; 
    $delivererUserId = $_POST['deliverer_user_id'] ?? null;
    $registeredByUserId = $_SESSION['user_id'];
    $fromAddressId = $_POST['from_address_id'];
    $toAddressId = $_POST['to_address_id'];
    $exactPrice = $_POST['exact_price'];
    $delivery_contact_info = $_POST['delivery_contact_info'] ?? null;
    $isPaid = isset($_POST['is_paid']) ? 1 : 0; // checkbox

    // Check for errors in form
    $errors = getShipmentErrs($statusShipment, $deliverFromUserId, $fromAddressId, $toAddressId, $exactPrice);
    if (empty($errors)) {
        
        ## Fetch connection to DB
        $db_connection = getDB();

        $prepared_query = mysqli_prepare($db_connection, "INSERT INTO shipment (statusShipment, ship_weight, 
        passenger_amount, date_sent, deliver_from_user_id, deliver_to_user_id, deliverer_user_id, 
        registered_by_user_id, from_address_id, to_address_id, delivery_contact_info, exact_price, is_paid) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        ## Check for error in query
        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
        } else {
            # Handle quotes, escape characters, SQL injection etc
            mysqli_stmt_bind_param($prepared_query, "sdisiiiiiisdi", 
                $statusShipment, $shipWeight, $passengerAmount, $dateSent, 
                $deliverFromUserId, $deliverToUserId, $delivererUserId, $registeredByUserId, 
                $fromAddressId, $toAddressId, $delivery_contact_info, $exactPrice, $isPaid);


            if (mysqli_stmt_execute($prepared_query)) {
                # Fetch id of new entry
                $id = mysqli_insert_id($db_connection);
                # Redirect to shipment page
                redirectToPath("/logistic-company/views/shipment.php?id=$id");
            } else {
                echo mysqli_stmt_error($prepared_query);
            }
        }
    } 
}

?>

<?php require '../includes/header.php'; ?>

<h4> Create a new shipment </h4>

<?php require '../includes/shipment.php'; ?>

<?php require '../includes/footer.php'; ?>