<?php
class Shipment {

    ### Function to fetch article columns based on DB connection and ID
    ### Fetches all columns by default
    public static function getShipment($db_connection, $id, $columns = '*') {
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
    public static function getShipmentErrs($shipWeight, $passengerAmount) {
        $errors = [];
        
        // Shipment required fields
        if ($shipWeight != 0.00 && $passengerAmount != 0) {
            $errors[] = "Can't set both shipment weight and passenger amount!";
        }
        if (($shipWeight == '' && $passengerAmount == '') || ($shipWeight == 0.00 && $passengerAmount == 0)){
            $errors[] = "At least shipment weight OR passenger amount required!";
        }

        return $errors;
    }

}
?>