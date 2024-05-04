<?php

### Function to fetch article columns based on DB connection and ID
### Fetches all columns by default
function getShipment($db_connection, $id, $columns = '*') {
    $sql_query = "SELECT $columns FROM shipment WHERE id = ?";

    ## Prevents SQL injection
    $prepared_query = mysqli_prepare($db_connection, $sql_query);

    if ($prepared_query === false ) {
        echo mysqli_error($db_connection);
    } else {
        mysqli_stmt_bind_param($prepared_query, 'i', $id);
        if (mysqli_stmt_execute($prepared_query)) {
            $result = mysqli_stmt_get_result($prepared_query);
            $shipment = mysqli_fetch_assoc($result);
            return $shipment;
        } else {
            echo mysqli_stmt_error($prepared_query);
        }
    }
}


### Function to validate if article fields are empty
function getShipmentErrs($statusShipment, $fromAddressId, $toAddressId, $exactPrice) {
    $errors = [];

    // Example checks for required fields
    if (empty($statusShipment)) {
        $errors[] = "Status of the shipment is required";
    }
    if (empty($fromAddressId)) {
        $errors[] = "From address is required";
    }
    if (empty($toAddressId)) {
        $errors[] = "To address is required";
    }
    if ($exactPrice === null) {
        $errors[] = "Exact price is required";
    }

    return $errors;
}

?>