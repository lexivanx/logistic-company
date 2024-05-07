<?php

session_start();
require '../includes/db.php';
require '../classes/Shipment.php';
require '../classes/User.php';
require '../classes/Address.php';
require '../includes/authentication.php';

## Fetch connection to DB
$db_connection = getDB();

if (isset($_GET['id'])) {
    $shipment = Shipment::getShipment($db_connection, $_GET['id']);
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['update_action'] == 'mark_completed') {
        $shipmentId = $_GET['id']; 
    
        // Prepare SQL Query to update the shipment
        $query = "UPDATE shipment SET statusShipment = ?, date_received = ? WHERE id = ?";
        $stmt = mysqli_prepare($db_connection, $query);
    
        // Check if prepare was successful
        if ($stmt === false) {
            echo "Failed to prepare the query: " . mysqli_error($db_connection);
            exit;
        }

        if ($shipment['is_paid'] == 0) {
            $statusShipment = 'Cancelled';
        } else {
            $statusShipment = 'Completed';
        }
    
        $dateReceived = date('Y-m-d H:i:s'); // Current date and time
    
        // Bind parameters and execute
        mysqli_stmt_bind_param($stmt, 'ssi', $statusShipment, $dateReceived, $shipmentId);
        if (mysqli_stmt_execute($stmt)) {
            echo "Shipment marked as completed.";
        } else {
            echo "Error updating record: " . mysqli_stmt_error($stmt);
        }
    }
} else {
    $shipment = null;
}

## After fetching the shipment data
if ($shipment !== null && isset($_GET['id'])) {
    $deliver_from_full_name = $shipment['deliver_from_user_id'] ? User::getUserFullNameById($shipment['deliver_from_user_id'], $db_connection) : "Unknown Sender";
    $deliver_to_full_name = $shipment['deliver_to_user_id'] ? User::getUserFullNameById($shipment['deliver_to_user_id'], $db_connection) : "Recipient Not Registered";
    $deliverer_employee_name = $shipment['deliverer_user_id'] ? User::getUserFullNameById($shipment['deliverer_user_id'], $db_connection) : "No Driver Assigned";
    $registered_by_full_name = User::getUserFullNameById($shipment['registered_by_user_id'], $db_connection);

    $from_address = Address::getAddress($db_connection, $shipment['from_address_id']);
    $from_country = $from_address['country'];
    $from_city = $from_address['city'];
    $from_street = $from_address['street'];
    $from_street_number = $from_address['street_number'];
    
    $to_address = Address::getAddress($db_connection, $shipment['to_address_id']);
    $to_country = $to_address['country'];
    $to_city = $to_address['city'];
    $to_street = $to_address['street'];
    $to_street_number = $to_address['street_number'];
}

?>
<?php require '../includes/header.php'; ?>
<div class="shipment-details">
    <?php if ($shipment === null): ?>
        <p class="error-message">No shipment found.</p>
    <?php else: ?>

        <shipment>
            <h3>Shipment ID: <?= htmlspecialchars($shipment['id'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <div>
            <p>Status: <?= htmlspecialchars($shipment['statusShipment'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php if ($shipment['ship_weight'] >= 0.35): ?>
                <p>Ship Weight: <?= htmlspecialchars($shipment['ship_weight'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php else: ?>
                <p>Passenger Amount: <?= htmlspecialchars($shipment['passenger_amount'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <p>Date sent: <?= htmlspecialchars($shipment['date_sent'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Date arrived: <?php if ($shipment['date_received']) { echo htmlspecialchars($shipment['date_received'], ENT_QUOTES, 'UTF-8'); } else { echo "Not received yet"; } ?> (can be completed on shipment page)</p>
            </div>
            <div>
            <p>Sender name: <?= htmlspecialchars($deliver_from_full_name, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Recipient name: <?= htmlspecialchars($deliver_to_full_name, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Delivery employee name: <?= htmlspecialchars($deliverer_employee_name, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Registered By: <?= htmlspecialchars($registered_by_full_name, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div>
            <p>Source address</p>
            <p>Country: <?= htmlspecialchars($from_country, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>City: <?= htmlspecialchars($from_city, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Street: <?= htmlspecialchars($from_street, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Street Number: <?= htmlspecialchars($from_street_number, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div>
            <p>Destination address</p>
            <p>Country: <?= htmlspecialchars($to_country, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>City: <?= htmlspecialchars($to_city, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Street: <?= htmlspecialchars($to_street, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Street Number: <?= htmlspecialchars($to_street_number, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div>
            <p>Delivery Contact Info: <?php if ($shipment['delivery_contact_info']) { echo htmlspecialchars($shipment['delivery_contact_info'], ENT_QUOTES, 'UTF-8'); } else { echo "Not provided"; } ?></p>
            <p>Exact Price: <?= htmlspecialchars($shipment['exact_price'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Is Paid: <?= $shipment['is_paid'] ? 'Yes' : 'No (complete to cancel)'; ?></p>
            </div>
        </shipment>

        <?php if (checkAuthentication()): ?>
            <?php if ($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "employee"): ?>
                <a href="/logistic-company/controllers/edit-shipment.php?id=<?= $shipment['id']; ?>" class="edit-link">Edit</a>
                <a href="/logistic-company/controllers/remove-shipment.php?id=<?= $shipment['id']; ?>" class="delete-link">Delete</a>
            <?php else: ?>
                <br>
                <p><em>Can't edit or remove!</em></p>
            <?php endif; ?>
            <?php if (($shipment['statusShipment'] != "Completed") && ($shipment['statusShipment'] != "Cancelled")): ?>
            <form method="POST" action="/logistic-company/views/shipment.php?id=<?= $shipment['id']; ?>">
                <input type="hidden" name="shipment_id" value="<?= $shipment['id']; ?>">
                <input type="hidden" name="update_action" value="mark_completed">
                <button type="submit">Complete shipment</button>
            </form>
            <?php endif; ?>
        <?php else: ?>
            <br>
            <p><em>Can't edit or remove!</em></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php require '../includes/footer.php'; ?>
