<?php

require 'service/db.php';
require 'service/authentication.php';

session_start();

## Fetch connection to DB
$db_connection = getDB();

$results = mysqli_query($db_connection, "SELECT * FROM shipment ORDER BY time_of");

## Check for error in query
if ( $results === false) {
    echo mysqli_error($db_connection);
} else {
    $shipments = mysqli_fetch_all($results, MYSQLI_ASSOC);
}

?>
<?php require 'service/header.php'; ?>

<div class="logged-in-info">
<?php if (checkAuthentication()): ?>

    <p> Currently logged in as: <strong> <?php echo $_SESSION['username']; ?> </strong> </p>
        <a href="logout.php">Logout</a>
    <p>
        <a href="create-shipment.php">Create shipment</a>
    </p>

<?php else: ?>

    <p> No user logged in </p>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>

<?php endif; ?>
</div>


<?php if (empty($shipments)): ?>
    <p>No shipments found.</p>
<?php else: ?>

<ul>
    <?php foreach ($shipments as $shipment) { ?>
        <li>
            <shipment>
                <h3>
                    <a href ="shipment.php?id=<?= $shipment['id']; ?>">
                        <?= htmlspecialchars($shipment['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </h3>
                <p>
                    <?= htmlspecialchars($shipment['body'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <p>
                    Created by: <strong> <?= htmlspecialchars($shipment['created_by'], ENT_QUOTES, 'UTF-8'); ?> </strong> 
                </p>
                <p>
                    Created at: <em> <?= htmlspecialchars($shipment['time_of'], ENT_QUOTES, 'UTF-8'); ?> </em> 
                </p>

            </shipment>
        </li>
    <?php } ?>
</ul>

<?php endif; ?>
<?php require 'service/footer.php'; ?>