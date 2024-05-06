<?php 

session_start();

require '../includes/db.php';
require '../classes/Shipment.php';
require '../classes/User.php';
require '../classes/Address.php';
require '../includes/http.php';
require '../includes/authentication.php';

if (!checkAuthentication()) {
    die ("Not logged in");
}

## Fetch connection to DB
$db_connection = getDB();

## Prepare default or form values
$statusShipment = '';
$shipWeight = '';
$passengerAmount = '';
$dateSent = '';
$deliver_from_full_name = '';
$deliver_to_full_name = '';
$deliverer_employee_name = '';
$registeredByUserId = '';
$fromAddressId = '';
$toAddressId = '';
$exactPrice = 0.00;
$delivery_contact_info = '';

### Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    ## Prepare form values from POST and default
    ## saves values if form is resubmitted with errors
    $statusShipment = 'New';
    $shipWeight = $_POST['ship_weight'];
    $passengerAmount = $_POST['passenger_amount'];
    $dateSent = $_POST['date_sent'];
    $dateReceived = null;
    $deliver_from_full_name = $_POST['deliver_from_full_name'];
    $deliver_to_full_name = $_POST['deliver_to_full_name'];
    $deliverer_employee_name = $_POST['deliverer_employee_name'];
    $registeredByUserId = $_SESSION['user_id'];
    $exactPrice = $_POST['exact_price'];
    $delivery_contact_info = $_POST['delivery_contact_info'];
    $isPaid = isset($_POST['is_paid']) ? 1 : 0; // checkbox

    $from_country = $_POST['from_country'];
    $from_city = $_POST['from_city'];
    $from_street = $_POST['from_street'];
    $from_street_number = $_POST['from_street_number'];

    $to_country = $_POST['to_country'];
    $to_city = $_POST['to_city'];
    $to_street = $_POST['to_street'];
    $to_street_number = $_POST['to_street_number'];

    if($deliver_from_full_name == '') {
        $deliver_from_full_name = $_SESSION['full_name'];
    }

    // Check for errors in form
    $errors = Shipment::getShipmentErrs($shipWeight, $passengerAmount);
    $errors_names = User::getUserShipmentErrs($deliver_from_full_name, $deliver_to_full_name, $deliverer_employee_name, $db_connection);
    $errors_from_address = Address::getAddressErrs('Source', $_POST['from_country'], $_POST['from_city'], $_POST['from_street'], $_POST['from_street_number']);
    $errors_to_address = Address::getAddressErrs('Destination', $_POST['to_country'], $_POST['to_city'], $_POST['to_street'], $_POST['to_street_number']);
    if (empty($errors) && empty($errors_names) && empty($errors_from_address) && empty($errors_to_address)){
        $deliverFromUserId = User::getUserIdByFullName($deliver_from_full_name, $db_connection);
        $deliverToUserId = User::getUserIdByFullName($deliver_to_full_name, $db_connection);
        $delivererUserId = User::getUserIdByFullName($deliverer_employee_name, $db_connection);

        $prepared_query = mysqli_prepare($db_connection, "INSERT INTO shipment (statusShipment, ship_weight, 
        passenger_amount, date_sent, deliver_from_user_id, deliver_to_user_id, deliverer_user_id, 
        registered_by_user_id, from_address_id, to_address_id, delivery_contact_info, exact_price, is_paid) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        ## Check for error in query
        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
        } else {

            ## Defaults for empty fields which can't be null due to DB constraint
            if ($statusShipment == '') {
                $statusShipment = 'New';
            }
            if ($dateSent == '') {
                $dateSent = date('Y-m-d H:i:s');
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
            if ($shipWeight > 0.05 && $shipWeight <= 5.00) {
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

            ## Create or retrieve address
            $fromAddressId = Address::createOrUpdateAddress($db_connection, $_POST['from_country'], $_POST['from_city'], $_POST['from_street'], $_POST['from_street_number']);
            $toAddressId = Address::createOrUpdateAddress($db_connection, $_POST['to_country'], $_POST['to_city'], $_POST['to_street'], $_POST['to_street_number']);
            
            # Handle quotes, escape characters, SQL injection etc
            mysqli_stmt_bind_param($prepared_query, "sdisiiiiiisdi", 
                $statusShipment, $shipWeight, $passengerAmount, $dateSent, 
                $deliverFromUserId, $deliverToUserId, $delivererUserId, $registeredByUserId, 
                $fromAddressId, $toAddressId, $delivery_contact_info, $exactPrice, $isPaid);


            if (mysqli_stmt_execute($prepared_query)) {
                # Redirect to index page
                redirectToPath("/logistic-company/index.php");
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