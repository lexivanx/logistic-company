<?php 

require 'includes/db.php';
require 'includes/article-funs.php';
require 'includes/http.php';
require 'includes/authentication.php';

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

    $errors = getArticleErrs($title, $body, $time_of);

    ## Check for errors in form
    if(empty($errors)) {
        ## Fetch connection to DB
        $db_connection = getDB();

        $prepared_query = mysqli_prepare($db_connection, "INSERT INTO article (title, body, time_of, created_by) VALUES (?, ?, ?, ?)");

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

                # Redirect to article page
                redirectToPath("/cms" . "/article.php?id=$id");

            } else {

                echo mysqli_stmt_error($prepared_query);

            }
        }
    }
    
}
?>

<?php require 'includes/header.php'; ?>

<h4> Create a new article </h4>

<?php require 'includes/article.php'; ?>

<?php require 'includes/footer.php'; ?>