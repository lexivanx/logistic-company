<?php

require 'includes/db.php';
require 'includes/authentication.php';

session_start();

## Fetch connection to DB
$db_connection = getDB();

#
#
#
## QUERIES
## INITIALIZE $results variable according to the query defined from the links"query-links"
# if the role is X and link is pressed, $results should be the query for that role and link
# difference between admin and employee is that employee uses only his own company id
#
#

if (checkAuthentication()) {
    ### NB NB NB!!!! Admin needs to see all companies, employee only his own
    if ($_SESSION['user_role'] == "admin") {
        $results = mysqli_query($db_connection, "SELECT * FROM shipment ORDER BY date_sent DESC");
    } else if ($_SESSION['user_role'] == "employee") {
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

<!-- Add query buttons for admin and employee roles -->
<!-- Buttons: All shipments(default defined in $results variable) -->
<!-- All shipments registed by registered_by_user_id, specified using employee user full_name -->
<!-- All shipments with status 'Sent' -->
<!-- All shipments by deliver_from_user_id, specified using user full_name -->
<!-- All shipments with status 'Received' and deliver_to_user_id, specifiued using user full_name -->
<!-- For admin, no company id needs to be specified, for employee, the session variable  $_SESSION['company_id'] always needs to be used -->
<div class="query-links">
<?php if ($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "employee"): ?>
    <a href="index.php">All shipments</a><br>
    <a href="index.php">All shipments by employee</a><br>
    <a href="index.php">All shipments 'Sent'</a><br>
    <a href="index.php">All shipments by Sender</a><br>
    <a href="index.php">All shipments 'Received' by Recipient</a><br>
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
                <h3>
                    <a href ="views/shipment.php?id=<?= $shipment['id']; ?>"> Shipment ID
                        <?= htmlspecialchars($shipment['id'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </h3>
                <p>Status: <?= htmlspecialchars($shipment['statusShipment'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Date sent: <?= htmlspecialchars(date("Y-m-d H:i:s", strtotime($shipment['date_sent'])), ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Exact Price: <?= htmlspecialchars($shipment['exact_price'], ENT_QUOTES, 'UTF-8'); ?> BGN</p>
                <p>Is Paid: <?= $shipment['is_paid'] ? 'Yes' : 'No'; ?></p>
            </shipment>
        </li>
    <?php } ?>
</ul>

<?php endif; ?>
<?php require 'includes/footer.php'; ?>