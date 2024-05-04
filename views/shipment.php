<?php

session_start();

require '../includes/db.php';
require '../includes/shipment-funs.php';
require '../includes/authentication.php';

## Fetch connection to DB
$db_connection = getDB();

if (isset($_GET['id'])) {
    $shipment = getShipment($db_connection, $_GET['id']);
} else {
    $shipment = null;
}

?>
<?php require '../includes/header.php'; ?>
    <?php if ($shipment === null): ?>
        <p class="error-message">No shipment found.</p>
    <?php else: ?>

        <shipment>
            <h3>Status: <?= htmlspecialchars($shipment['statusShipment'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <p>Ship Weight: <?= htmlspecialchars($shipment['ship_weight'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Passenger Amount: <?= htmlspecialchars($shipment['passenger_amount'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Date Sent: <?= htmlspecialchars($shipment['date_sent'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>From Address ID: <?= htmlspecialchars($shipment['from_address_id'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>To Address ID: <?= htmlspecialchars($shipment['to_address_id'], ENT_QUOTES, 'UTF-8'); ?></p>
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
        <?php else: ?>
            <br>
            <p><em>Can't edit or remove!</em></p>
        <?php endif; ?>
        
    <?php endif; ?>
<?php require '../includes/footer.php'; ?>
