<?php

require 'includes/db.php';
require 'includes/article-funs.php';
require 'includes/http.php';
require 'includes/authentication.php';

session_start();

if (!checkAuthentication()) {
    die("You don't have permission to edit or remove");
}

### Fetch connection to DB
$db_connection = getDB();

### If ID is not set, print error and exit script
if (isset($_GET['id'])) {

    $article = getArticle($db_connection, $_GET['id']);

    ## If ID is invalid, print error and exit script
    if ($article) {

        $id = $article['id'];
        $title = $article['title'];
        $body = $article['body'];
        $time_of = $article['time_of'];
        $created_by = $article['created_by'];

        if ($_SESSION['username'] != "admin" && $_SESSION['username'] != $article['created_by']) {

            die("You don't have permission to edit or remove");

        }

    } else {

        die("No article found");

    }

} else {

    die("ID not specified, no article found");

}

### Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $body = $_POST['body'];
    $time_of = $_POST['time_of'];

    $errors = getArticleErrs($title, $body, $time_of);

    ## Check for errors in form
    if(empty($errors)) {

        ## Update query
        $prepared_query = mysqli_prepare($db_connection, "UPDATE article SET title = ?, body = ?, time_of = ? WHERE id = ?");

        ## Check for error in query
        if ( $prepared_query === false) {

            echo mysqli_error($db_connection);

        } else {
            
            if ($time_of == '') {
                $time_of = null;
            }

            # Handle quotes, escape characters, SQL injection etc.
            mysqli_stmt_bind_param($prepared_query, "sssi", $title, $body, $time_of, $id);

            if (mysqli_stmt_execute($prepared_query)) {

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

<h4> Article information </h4>

<?php require 'includes/article.php'; ?>

<?php require 'includes/footer.php'; ?>