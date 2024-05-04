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

### Check if the shipment exists and handle permissions
if ($shipment) {
    $shipmentId = $shipment['id'];
    $statusShipment = $shipment['statusShipment'];
    $shipWeight = $shipment['ship_weight'];
    $passengerAmount = $shipment['passenger_amount'];
    $dateSent = date('Y-m-d\TH:i', strtotime($shipment['date_sent']));
    $deliverFromUserId = $shipment['deliver_from_user_id'];
    $delivererUserId = $shipment['deliverer_user_id'];
    $registeredByUserId = $shipment['registered_by_user_id'];
    $fromAddressId = $shipment['from_address_id'];
    $toAddressId = $shipment['to_address_id'];
    $exactPrice = $shipment['exact_price'];
    $isPaid = $shipment['is_paid'];

    if ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "employee") {
        die("You don't have permission to edit this shipment");
    }
} else {
    die("No shipment found");
}

### Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $statusShipment = $_POST['status_shipment'] ?? $statusShipment;
    $shipWeight = $_POST['ship_weight'] ?? $shipWeight;
    $dateSent = $_POST['date_sent'] ?? $dateSent;
    $dateSent = date('Y-m-d\TH:i', strtotime($dateSent));
    $deliverFromUserId = $_POST['deliver_from_user_id'] ?? $deliverFromUserId;
    $deliverToUserId = $_POST['deliver_to_user_id'] ?? null;
    $delivererUserId = $_POST['deliverer_user_id'] ?? null;
    $registeredByUserId = $_SESSION['user_id'];  // Assume this should not change
    $fromAddressId = $_POST['from_address_id'] ?? $fromAddressId;
    $toAddressId = $_POST['to_address_id'] ?? $toAddressId;
    $deliveryContactInfo = $_POST['delivery_contact_info'] ?? null;
    $exactPrice = $_POST['exact_price'] ?? $exactPrice;
    $isPaid = isset($_POST['is_paid']) ? 1 : 0;

    $errors = getShipmentErrs($statusShipment, $dateSent, $deliverFromUserId, $delivererUserId, $registeredByUserId, $fromAddressId, $toAddressId, $exactPrice);

    ## Check for errors in form
    if(empty($errors)) {

        ## Update query
        $prepared_query = mysqli_prepare($db_connection, "UPDATE shipment SET statusShipment = ?, date_sent = ?, 
        deliver_from_user_id = ?, deliver_to_user_id = ?, deliverer_user_id = ?, 
        registered_by_user_id = ?, from_address_id = ?, 
        to_address_id = ?, delivery_contact_info = ?, exact_price = ?, is_paid = ? WHERE id = ?");

        ## Check for error in query
        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
        } else {
            mysqli_stmt_bind_param($prepared_query, 'ssiiiiiisdii',
                $statusShipment,
                $dateSent,
                $deliverFromUserId,
                $deliverToUserId,
                $delivererUserId,
                $registeredByUserId,
                $fromAddressId,
                $toAddressId,
                $deliveryContactInfo,
                $exactPrice,
                $isPaid,
                $shipmentId
            );

            if (mysqli_stmt_execute($prepared_query)) {
                # Redirect to shipment page
                redirectToPath("/logistic-company/views/shipment.php?id=$shipmentId");
            } else {
                echo mysqli_stmt_error($prepared_query);
            }
        }
    }
}

?>

<?php require '../includes/header.php'; ?>

<h4> Edit Shipment Information </h4>

<?php require '../includes/shipment.php'; ?>

<?php require '../includes/footer.php'; ?>
