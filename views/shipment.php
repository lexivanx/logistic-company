<?php

session_start();

require '../includes/db.php';
require '../includes/shipment-funs.php';
require '../includes/authentication.php';

## Fetch connection to DB
$db_connection = getDB();

if (isset($_GET['id'])) {
    $shipment = getShipment($db_connection, $_GET['id']);
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
    
        $statusShipment = 'Completed';
        $dateReceived = date('Y-m-d H:i:s'); // Current date and time
    
        // Bind parameters and execute
        mysqli_stmt_bind_param($stmt, 'ssi', $statusShipment, $dateReceived, $shipmentId);
        if (mysqli_stmt_execute($stmt)) {
            echo "Shipment marked as completed.";
        } else {
            echo "Error updating record: " . mysqli_stmt_error($stmt);
        }
    
        mysqli_stmt_close($stmt);
        mysqli_close($db_connection);
    }
} else {
    $shipment = null;
}

?>
<?php require '../includes/header.php'; ?>
<div class="shipment-details">
    <?php if ($shipment === null): ?>
        <p class="error-message">No shipment found.</p>
    <?php else: ?>

        <shipment>
            <h3>Shipment ID: <?= htmlspecialchars($shipment['id'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <p>Status: <?= htmlspecialchars($shipment['statusShipment'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Ship Weight: <?= htmlspecialchars($shipment['ship_weight'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Passenger Amount: <?= htmlspecialchars($shipment['passenger_amount'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Date sent: <?= htmlspecialchars($shipment['date_sent'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Date arrived: <?php if ($shipment['date_received']) { echo htmlspecialchars($shipment['date_received'], ENT_QUOTES, 'UTF-8'); } else { echo "Not received yet"; } ?></p>
            <p>From user Id: <?= htmlspecialchars($shipment['deliver_from_user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>To user Id: <?= htmlspecialchars($shipment['deliver_to_user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Deliverer User ID: <?= htmlspecialchars($shipment['deliverer_user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Registered By User ID: <?= htmlspecialchars($shipment['registered_by_user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>From Address ID: <?= htmlspecialchars($shipment['from_address_id'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>To Address ID: <?= htmlspecialchars($shipment['to_address_id'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Delivery Contact Info: <?= htmlspecialchars($shipment['delivery_contact_info'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Exact Price: <?= htmlspecialchars($shipment['exact_price'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Is Paid: <?= $shipment['is_paid'] ? 1 : 0; ?></p>
        </shipment>

        <?php if (checkAuthentication()): ?>
            <?php if ($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "employee"): ?>
                <a href="/logistic-company/controllers/edit-shipment.php?id=<?= $shipment['id']; ?>" class="edit-link">Edit</a>
                <a href="/logistic-company/controllers/remove-shipment.php?id=<?= $shipment['id']; ?>" class="delete-link">Delete</a>
            <?php else: ?>
                <br>
                <p><em>Can't edit or remove!</em></p>
            <?php endif; ?>
            <form method="POST" action="/logistic-company/views/shipment.php?id=<?= $shipment['id']; ?>">
                <input type="hidden" name="shipment_id" value="<?= $shipment['id']; ?>">
                <input type="hidden" name="update_action" value="mark_completed">
                <button type="submit">Complete shipment</button>
            </form>
        <?php else: ?>
            <br>
            <p><em>Can't edit or remove!</em></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php require '../includes/footer.php'; ?>
