<?php 

session_start();

require '../includes/db.php';
require '../includes/shipment-funs.php';
require '../includes/http.php';
require '../includes/authentication.php';

if (!checkAuthentication()) {
    die ("Not logged in");
}

## Prepare default or form values
$statusShipment = '';
$shipWeight = '';
$passengerAmount = '';
$dateSent = '';
$deliverFromUserId = '';
$deliverToUserId = '';
$delivererUserId = '';
$registeredByUserId = '';
$fromAddressId = '';
$toAddressId = '';
$exactPrice = 0.00;
$delivery_contact_info = '';

### Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    ## Prepare default or form values
    $statusShipment = $_POST['status_shipment'];
    $shipWeight = $_POST['ship_weight'];
    $passengerAmount = $_POST['passenger_amount'];
    $dateSent = $_POST['date_sent'];
    $dateReceived = null;
    $deliverFromUserId = $_POST['deliver_from_user_id'];
    $deliverToUserId = $_POST['deliver_to_user_id']; 
    $delivererUserId = $_POST['deliverer_user_id'];
    $registeredByUserId = $_SESSION['user_id'];
    $fromAddressId = $_POST['from_address_id'];
    $toAddressId = $_POST['to_address_id'];
    $exactPrice = $_POST['exact_price'];
    $delivery_contact_info = $_POST['delivery_contact_info'];
    $isPaid = isset($_POST['is_paid']) ? 1 : 0; // checkbox

    // Check for errors in form
    $errors = getShipmentErrs($fromAddressId, $toAddressId, $shipWeight, $passengerAmount);
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

            if ($shipWeight > 0.00) {
                $exactPrice = $shipWeight * $row['price'];
            } elseif ($passengerAmount > 0) {
                $exactPrice = $passengerAmount * $row['price'];
            } else {
                $exactPrice = 0.00;
            }
            
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