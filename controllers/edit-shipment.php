<?php

session_start();

require '../includes/db.php';
require '../classes/Shipment.php';
require '../classes/User.php';
require '../classes/Address.php';
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

$shipment = Shipment::getShipment($db_connection, $_GET['id']);

if ($shipment) {
    $shipmentId = $shipment['id'];
    $statusShipment = $shipment['statusShipment'];
    
    $shipWeight = $shipment['ship_weight'];
    $passengerAmount = $shipment['passenger_amount'];
    
    $dateSent = $shipment['date_sent'];
    $dateReceived = $shipment['date_received'];
    
    $deliverFromUserId = $shipment['deliver_from_user_id'];
    $deliver_from_full_name = User::getUserFullNameById($shipment['deliver_from_user_id'], $db_connection);
    
    $registered_by_full_name = User::getUserFullNameById($shipment['registered_by_user_id'], $db_connection);

    $deliverToUserId = $shipment['deliver_to_user_id'];
    $deliver_to_full_name = User::getUserFullNameById($shipment['deliver_to_user_id'], $db_connection);

    $delivererUserId = $shipment['deliverer_user_id'];
    $deliverer_employee_name = User::getUserFullNameById($shipment['deliverer_user_id'], $db_connection);

    $registeredByUserId = $shipment['registered_by_user_id'];
    
    $fromAddressId = $shipment['from_address_id'];
    $toAddressId = $shipment['to_address_id'];
    
    $fromAddress = Address::getAddress($db_connection, $fromAddressId);
    $from_country = $fromAddress['country'];
    $from_city = $fromAddress['city'];
    $from_street = $fromAddress['street'];
    $from_street_number = $fromAddress['street_number'];
    
    $toAddress = Address::getAddress($db_connection, $toAddressId);
    $to_country = $toAddress['country'];
    $to_city = $toAddress['city'];
    $to_street = $toAddress['street'];
    $to_street_number = $toAddress['street_number'];
    
    $exactPrice = $shipment['exact_price'];
    $delivery_contact_info = $shipment['delivery_contact_info'];
    $isPaid = $shipment['is_paid'];

    if ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "employee") {
        die("You don't have permission to edit this shipment");
    }
} else {
    die("No shipment found");
}

### Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    ## Prepare default or form values
    $statusShipment = $_POST['status_shipment'];
    $shipWeight = $_POST['ship_weight'];
    $passengerAmount = $_POST['passenger_amount'];
    $dateSent = $_POST['date_sent'];
    $registeredByUserId = $_SESSION['user_id'];
    $delivery_contact_info = $_POST['delivery_contact_info'];
    ## Checkbox for payment status
    $isPaid = isset($_POST['is_paid']) ? 1 : 0; 

    ## Check for errors in form
    $errors = Shipment::getShipmentErrs($shipWeight, $passengerAmount);
    $errors_names = User::getUserShipmentErrs($_POST['deliver_from_full_name'], $_POST['deliver_to_full_name'], $_POST['deliverer_employee_name'], $db_connection);
    $errors_from_address = Address::getAddressErrs('Source', $_POST['from_country'], $_POST['from_city'], $_POST['from_street'], $_POST['from_street_number']);
    $errors_to_address = Address::getAddressErrs('Destination', $_POST['to_country'], $_POST['to_city'], $_POST['to_street'], $_POST['to_street_number']);
    if (empty($errors) && empty($errors_names) && empty($errors_from_address) && empty($errors_to_address)){
        
        ## Update query
        $prepared_query = mysqli_prepare($db_connection, "UPDATE shipment SET statusShipment = ?, date_sent = ?, 
        deliver_from_user_id = ?, deliver_to_user_id = ?, deliverer_user_id = ?, 
        registered_by_user_id = ?, from_address_id = ?, 
        to_address_id = ?, delivery_contact_info = ?, exact_price = ?, is_paid = ?,
        ship_weight = ?, passenger_amount = ? WHERE id = ?");

        ## Check for error in query
        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
        } else {
            ## If dateSent is not set, set to null OR default
            if ($statusShipment == '') {
                $statusShipment = 'New';
            }
            if ($dateSent == '') {
                $dateSent = date('Y-m-d H:i:s');
            }
            if ($deliverFromUserId == '') {
                $deliverFromUserId = $_SESSION['user_id'];
            }
            if ($deliverToUserId == '') {
                $deliverToUserId = null;
            }
            if ($delivererUserId == '') {
                $delivererUserId = null;
            }
            if ($delivery_contact_info == '') {
                $delivery_contact_info = null;
            }
            if ($shipWeight == ''){
                $shipWeight = 0.00;
            
            } 
            if ($passengerAmount == ''){
                $passengerAmount = 0;
            }

            # Calculate price
            # Fetch price field in table 'price' where weight_class='package_c'
            # Store field value in $exactPrice
            if ($shipWeight > 0.00 && $shipWeight <= 5.00) {
                $sql_query = "SELECT price FROM price WHERE weight_class = 'package_c'";
            } elseif ($shipWeight > 5.00 && $shipWeight < 20.00) {
                $sql_query = "SELECT price FROM price WHERE weight_class = 'package_b'";
            } elseif ($shipWeight >= 20.00) {
                $sql_query = "SELECT price FROM price WHERE weight_class = 'package_a'";
            } 

            if ($passengerAmount > 0 && $passengerAmount <= 10) {
                $sql_query = "SELECT price FROM price WHERE weight_class = 'people_b'";
            } elseif ($passengerAmount > 10) {
                $sql_query = "SELECT price FROM price WHERE weight_class = 'people_a'";
            } 
            $result = mysqli_query($db_connection, $sql_query);
            $row = mysqli_fetch_assoc($result);

            if ($shipWeight > 0.35) {
                $exactPrice = $shipWeight * $row['price'];
            } elseif ($passengerAmount > 0) {
                $exactPrice = $passengerAmount * $row['price'];
            } else {
                $exactPrice = 0.00;
            }

            ## User changes
            $deliverFromUserId = User::getUserIdByFullName($_POST['deliver_from_full_name'], $db_connection);
            $deliverToUserId = User::getUserIdByFullName($_POST['deliver_to_full_name'], $db_connection);
            $delivererUserId = User::getUserIdByFullName($_POST['deliverer_employee_name'], $db_connection);

            ## Address changes
            $fromAddressId = Address::createOrUpdateAddress($db_connection, $_POST['from_country'], $_POST['from_city'], $_POST['from_street'], $_POST['from_street_number']);
            $toAddressId = Address::createOrUpdateAddress($db_connection, $_POST['to_country'], $_POST['to_city'], $_POST['to_street'], $_POST['to_street_number']); 

            mysqli_stmt_bind_param($prepared_query, 'ssiiiiiisdidii',
                $statusShipment,
                $dateSent,
                $deliverFromUserId,
                $deliverToUserId,
                $delivererUserId,
                $registeredByUserId,
                $fromAddressId,
                $toAddressId,
                $delivery_contact_info,
                $exactPrice,
                $isPaid,
                $shipWeight,
                $passengerAmount,
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
