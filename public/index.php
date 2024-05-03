<?php

require __DIR__ . '/../app/config/db.php';
require __DIR__ . '/../app/service/authentication.php';
require __DIR__ . '/../app/model/User.php';

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
<?php require __DIR__ . '/../app/view/header.php'; ?>

<div class="logged-in-info">
<?php if (checkAuthentication()): ?>

    <p> Currently logged in as: <strong> <?php echo $_SESSION['username']; ?> </strong> </p>
        <a href="/logistic-company/app/controller/logout.php">Logout</a>
    <p>
        <a href="/logistic-company/app/controller/create-shipment.php">Create shipment</a>
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
                <?php if (checkAuthentication() && (User::get_role($_SESSION['user_id']) == 'admin' || User::get_role($_SESSION['user_id']) == 'employee')): ?>
                    <a href ="/logistic-company/app/view/shipment.php?id=<?= $shipment['id']; ?>">
                        <?= htmlspecialchars($shipment['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php else: ?>
                    <?= htmlspecialchars($shipment['title'], ENT_QUOTES, 'UTF-8'); ?>
                <?php endif; ?>
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
<?php require  __DIR__ . '/../app/view/footer.php'; ?>