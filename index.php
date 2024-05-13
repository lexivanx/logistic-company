<?php

require 'includes/db.php';
require 'includes/authentication.php';
require 'includes/queries.php';
require 'classes/Company.php';

session_start();

## Fetch connection to DB
$db_connection = getDB();

if (checkAuthentication()) {

    ## Check for the query type parameter in the URL
    $queryType = isset($_GET['query']) ? getQueryType($_GET['query']) : 'all';

    ## IDs from the GET parameters
    $query_registered_by_user_id = isset($_GET['registered_by_user_id']) ? (int)$_GET['registered_by_user_id'] : null;
    $query_deliver_from_user_id = isset($_GET['deliver_from_user_id']) ? (int)$_GET['deliver_from_user_id'] : null;
    $query_deliver_to_user_id = isset($_GET['deliver_to_user_id']) ? (int)$_GET['deliver_to_user_id'] : null;

    if ($_SESSION['user_role'] == "customer") {
        $sql = "SELECT * FROM shipment WHERE deliver_from_user_id = ? OR deliver_to_user_id = ? ORDER BY date_sent DESC";
        $prepared_query = mysqli_prepare($db_connection, $sql);

        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
        } else {
            mysqli_stmt_bind_param($prepared_query, "ii", $_SESSION['user_id'], $_SESSION['user_id']);
            mysqli_stmt_execute($prepared_query);
            $results = mysqli_stmt_get_result($prepared_query);
        }
    } else {
        switch ($queryType) {
            case 'by_employee':
                $sql = "SELECT * FROM shipment WHERE registered_by_user_id = ? ORDER BY date_sent DESC";
                $prepared_query = mysqli_prepare($db_connection, $sql);

                if ($prepared_query === false) {
                    echo mysqli_error($db_connection);
                } else {
                    mysqli_stmt_bind_param($prepared_query, "i", $query_registered_by_user_id);
                    mysqli_stmt_execute($prepared_query);
                    $results = mysqli_stmt_get_result($prepared_query);
                }
                break;
            case 'sent':
                $sql = "SELECT * FROM shipment WHERE statusShipment = 'Sent' ORDER BY date_sent DESC";
                $prepared_query = mysqli_prepare($db_connection, $sql);
                if ($prepared_query === false) {
                    echo mysqli_error($db_connection);
                } else {
                    mysqli_stmt_execute($prepared_query);
                    $results = mysqli_stmt_get_result($prepared_query);
                }
                break;
            case 'by_sender':
                $sql = "SELECT * FROM shipment WHERE deliver_from_user_id = ? ORDER BY date_sent DESC";
                $prepared_query = mysqli_prepare($db_connection, $sql);
                if ($prepared_query === false) {
                    echo mysqli_error($db_connection);
                } else {
                    mysqli_stmt_bind_param($prepared_query, "i", $query_deliver_from_user_id);
                    mysqli_stmt_execute($prepared_query);
                    $results = mysqli_stmt_get_result($prepared_query);
                }
                break;
            case 'received':
                $sql = "SELECT * FROM shipment WHERE statusShipment = 'Completed' AND deliver_to_user_id = ? AND is_paid = 1 ORDER BY date_sent DESC";
                $prepared_query = mysqli_prepare($db_connection, $sql);
                if ($prepared_query === false) {
                    echo mysqli_error($db_connection);
                } else {
                    mysqli_stmt_bind_param($prepared_query, "i", $query_deliver_to_user_id);
                    mysqli_stmt_execute($prepared_query);
                    $results = mysqli_stmt_get_result($prepared_query);
                }
                break;
            case 'all':
            default:
                ## Admin sees shipments for all companies, employee only for his own
                ## Customers can't see queries
                if ($_SESSION['user_role'] == "admin") {
                    $results = mysqli_query($db_connection, "SELECT * FROM shipment ORDER BY date_sent DESC");
                } else if ($_SESSION['user_role'] == "employee") {
                    $sql = "SELECT * FROM shipment WHERE company_id = ? ORDER BY date_sent DESC";
                    $prepared_query = mysqli_prepare($db_connection, $sql);

                    if ($prepared_query === false) {
                        echo mysqli_error($db_connection);
                    } else {
                        mysqli_stmt_bind_param($prepared_query, "i", $_SESSION['company_id']);
                        mysqli_stmt_execute($prepared_query);
                        $results = mysqli_stmt_get_result($prepared_query);
                    }
                }
                break;
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

<div class="query-links">
    <?php if ($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "employee"): ?>
    <h3>Queries</h3>
    <!-- All Shipments -->
    <a href="index.php?query=all">All registered shipments</a><br>
    <!-- Shipments 'Sent' -->
    <a href="index.php?query=sent">All shipments with status 'Sent' / Not received yet </a><br>

    <!-- Shipments by Employee -->
    <form action="index.php" method="get">
        <input type="hidden" name="query" value="by_employee">
        <input type="text" name="registered_by_user_id" placeholder="All Shipments registered by a user (ID)">
        <button type="submit">Submit</button>
    </form><br>

    <!-- Shipments by Sender -->
    <form action="index.php" method="get">
        <input type="hidden" name="query" value="by_sender">
        <input type="text" name="deliver_from_user_id" placeholder="All shipments sent by a user (ID)">
        <button type="submit">Submit</button>
    </form><br>

    <!-- Shipments 'Received' by Recipient -->
    <form action="index.php" method="get">
        <input type="hidden" name="query" value="received">
        <input type="text" name="deliver_to_user_id" placeholder="All shipments received by a user (ID)">
        <button type="submit">Submit</button>
    </form><br>
    <?php endif; ?>
</div>

<div class="management-links">
<?php if ($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "employee"): ?>
    <h3> Management </h3>
    <a href="employee.php">Employee portal</a><br>
<?php endif; ?>
<?php if ($_SESSION['user_role'] == "admin"): ?>
    <a href="admin.php">Company administration</a><br>
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
                <p>Company: <?= Company::getCompany($db_connection, $shipment['company_id'], 'company_name')['company_name'] ?></p>
            </shipment>
        </li>
    <?php } ?>
</ul>

<?php endif; ?>
<?php require 'includes/footer.php'; ?>