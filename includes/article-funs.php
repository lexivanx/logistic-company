<?php

### Function to fetch article columns based on DB connection and ID
### Fetches all columns by default
function getArticle($db_connection, $id, $columns = '*') {
    $sql_query = "SELECT $columns FROM article WHERE id = ?";

    ## Prevents SQL injection
    $prepared_query = mysqli_prepare($db_connection, $sql_query);

    if ($prepared_query === false ) {

        echo mysqli_error($db_connection);

    } else {

        mysqli_stmt_bind_param($prepared_query, 'i', $id);

        if (mysqli_stmt_execute($prepared_query)) {

            $result = mysqli_stmt_get_result($prepared_query);
            $article = mysqli_fetch_assoc($result);

            return $article;
            
        } else {

            echo mysqli_stmt_error($prepared_query);

        }
    }
}


### Function to validate if article fields are empty
function getArticleErrs($title, $body, $time_of) {

    $errors = [];

    ## Check for empty fields
    if (empty($title)) {

        $errors[] = "Title is required";

    }
    if (empty($body)) {

        $errors[] = "Body is required";

    }

    return $errors;
}
?>