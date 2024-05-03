<?php

### get DB connection via host, name, user, pass
function getDB() {
    $db_host = "mysql-server";
    $db_name = "logistic_companyDB";
    $db_user = "root";
    $db_pass = "secretpass123";

    $db_connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if (mysqli_connect_error()) {
        echo mysqli_connect_error();
        exit;
    }

    return $db_connection;
}

?>