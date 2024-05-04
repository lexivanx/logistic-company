<?php

require 'includes/db.php';
require 'includes/authentication.php';

session_start();

## Fetch connection to DB
$db_connection = getDB();

if (checkAuthentication()) {
    if ($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "employee") {
        $results = mysqli_query($db_connection, "SELECT * FROM shipment ORDER BY date_sent DESC");
    } else {
        $sql = "SELECT * FROM shipment WHERE deliver_from_user_id = ? OR deliver_to_user_id = ? ORDER BY date_sent DESC";

        $prepared_query = mysqli_prepare($db_connection, $sql);

        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
        } else {
            mysqli_stmt_bind_param($prepared_query, "ii", $_SESSION['user_id'], $_SESSION['user_id']);
            mysqli_stmt_execute($prepared_query);
            $results = mysqli_stmt_get_result($prepared_query);
        }
    }
}

## Check if query found anything
if ($results != null) {
    ## Check for error in query
    if ( $results === false) {
        echo mysqli_error($db_connection);
    } else {
        $shipments = mysqli_fetch_all($results, MYSQLI_ASSOC);
    }
}


?>
<?php require 'includes/header.php'; ?>

<div class="logged-in-info">
<?php if (checkAuthentication()): ?>

    <p> Currently logged in as: <strong> <?php echo $_SESSION['username']; ?> </strong> </p>
        <a href="logout.php">Logout</a>
    <p>
        <a href="controllers/create-shipment.php">Create shipment</a>
    </p>

<?php else: ?>

    <p> No user logged in </p>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>

<?php endif; ?>
</div>


<?php if (!checkAuthentication()): ?>
    <p>Please, log in to view shipments!</p>
<?php elseif (empty($shipments)): ?>
    <p>No shipments found.</p>
<?php else: ?>

<ul>
    <?php foreach ($shipments as $shipment) { ?>
        <li>
            <shipment>  
                <!-- Only admins and employees should be able to click shipment links to edit or remove -->
                <h3>
                    <a href ="views/shipment.php?id=<?= $shipment['id']; ?>"> Shipment ID
                        <?= htmlspecialchars($shipment['id'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </h3>
                <p>Status: <?= htmlspecialchars($shipment['statusShipment'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Ship Weight: <?= htmlspecialchars($shipment['ship_weight'], ENT_QUOTES, 'UTF-8'); ?> kg</p>
                <p>Passenger Amount: <?= htmlspecialchars($shipment['passenger_amount'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Date sent: <?= htmlspecialchars(date("Y-m-d H:i:s", strtotime($shipment['date_sent'])), ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Date arrived: <?= $shipment['date_received'] ? htmlspecialchars(date("Y-m-d H:i:s", strtotime($shipment['date_received'])), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></p>
                <p>From User ID: <?= htmlspecialchars($shipment['deliver_from_user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>To User ID: <?= $shipment['deliver_to_user_id'] ? htmlspecialchars($shipment['deliver_to_user_id'], ENT_QUOTES, 'UTF-8') : 'N/A'; ?></p>
                <p>Deliverer User ID: <?= htmlspecialchars($shipment['deliverer_user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Registered By User ID: <?= htmlspecialchars($shipment['registered_by_user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>From Address ID: <?= htmlspecialchars($shipment['from_address_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>To Address ID: <?= htmlspecialchars($shipment['to_address_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Delivery Contact Info: <?= $shipment['delivery_contact_info'] ? htmlspecialchars($shipment['delivery_contact_info'], ENT_QUOTES, 'UTF-8') : 'N/A'; ?></p>
                <p>Exact Price: <?= htmlspecialchars($shipment['exact_price'], ENT_QUOTES, 'UTF-8'); ?> BGN</p>
                <p>Is Paid: <?= $shipment['is_paid'] ? 'Yes' : 'No'; ?></p>

            </shipment>
        </li>
    <?php } ?>
</ul>

<?php endif; ?>
<?php require 'includes/footer.php'; ?>