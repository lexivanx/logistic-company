<?php

session_start();

require 'includes/db.php';
require 'includes/shipment-funs.php';
require 'includes/authentication.php';

## Fetch connection to DB
$db_connection = getDB();

if (isset($_GET['id'])) {

    $shipment = getShipment($db_connection, $_GET['id']);

} else {

    $shipment = null;
    
}

?>
<?php require 'includes/header.php'; ?>
    <?php if ($shipment === null): ?>
        <p class="error-message">No shipments found.</p>
    <?php else: ?>

        <shipment>
            <h3><?= htmlspecialchars($shipment['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <p><?=  htmlspecialchars($shipment['body'], ENT_QUOTES, 'UTF-8'); ?></p>
            <br><br>
            <p>Created by: <strong> <?= htmlspecialchars($shipment['created_by'], ENT_QUOTES, 'UTF-8'); ?> </strong> </p>
            <p>Created at: <em> <?= htmlspecialchars($shipment['time_of'], ENT_QUOTES, 'UTF-8'); ?> </em> </p>
        </shipment>

        <?php if (checkAuthentication()): ?>

            <?php if ($_SESSION['username'] == "admin" || $_SESSION['username'] == $shipment['created_by']): ?>
                <a href="edit-shipment.php?id=<?= $shipment['id']; ?>" class="edit-link">Edit</a>
                <a href="remove-shipment.php?id=<?= $shipment['id']; ?>" class="delete-link">Delete</a>
            <?php else: ?>
                <br>
                <p><em>Can't edit or remove!</em></p>
            <?php endif; ?>

        <?php else: ?>
            <br>
            <p><em>Can't edit or remove!</em></p>
        <?php endif; ?>
        
    <?php endif; ?>
<?php require 'includes/footer.php'; ?>