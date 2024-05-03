<?php 

require  __DIR__ . '/../config/db.php';
require  __DIR__ . '/../service/shipment-funs.php';
require  __DIR__ . '/../service/http.php';
require  __DIR__ . '/../service/authentication.php';

session_start();

if (!checkAuthentication()) {
    die ("Not logged in");
}

$title = '';
$body = '';
$time_of = '';

### Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $body = $_POST['body'];
    $time_of = $_POST['time_of'];
    $created_by = $_SESSION['username'];

    $errors = getShipmentErrs($title, $body, $time_of);

    ## Check for errors in form
    if(empty($errors)) {
        ## Fetch connection to DB
        $db_connection = getDB();

        $prepared_query = mysqli_prepare($db_connection, "INSERT INTO shipment (title, body, time_of, created_by) VALUES (?, ?, ?, ?)");

        ## Check for error in query
        if ( $prepared_query === false) {

            echo mysqli_error($db_connection);

        } else {
            
            if ($time_of == '') {
                $time_of = date('Y-m-d H:i:s');
            }

            # Handle quotes, escape characters, SQL injection etc.
            mysqli_stmt_bind_param($prepared_query, "ssss", $title, $body, $time_of, $created_by);

            if (mysqli_stmt_execute($prepared_query)) {

                # Fetch id of new entry
                $id = mysqli_insert_id($db_connection);

                # Redirect to shipment page
                redirectToPath("/logistic-company/app/view" . "/shipment.php?id=$id");

            } else {

                echo mysqli_stmt_error($prepared_query);

            }
        }
    }
    
}
?>

<?php require  __DIR__ . '/../view/header.php'; ?>

<h4> Create a new shipment </h4>

<?php require  __DIR__ . '/../view/shipment.php'; ?>

<?php require  __DIR__ . '/../view/footer.php'; ?>