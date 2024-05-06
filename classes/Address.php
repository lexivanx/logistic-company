<?php

class Address{

    ### Function to fetch address columns based on DB connection and ID
    ### Fetches all columns by default
    public static function getAddress($db_connection, $id, $columns = '*') {
        $sql_query = "SELECT $columns FROM address WHERE id = ?";

        ## Prevents SQL injection
        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        if ($prepared_query === false ) {
            echo mysqli_error($db_connection);
        } else {
            mysqli_stmt_bind_param($prepared_query, 'i', $id);
            if (mysqli_stmt_execute($prepared_query)) {
                $result = mysqli_stmt_get_result($prepared_query);
                $address = mysqli_fetch_assoc($result);
                return $address;
            } else {
                echo mysqli_stmt_error($prepared_query);
            }
        }
    }

    ### Function to validate if address fields are empty
    public static function getAddressErrs($direction, $country, $city, $street, $street_number) {
        $errors = [];

        if (empty($country)) {
            $errors[] = "$direction country is required";
        }
        if (empty($city)) {
            $errors[] = "$direction city is required";
        }
        if (empty($street)) {
            $errors[] = "$direction street is required";
        }
        if (empty($street_number)) {
            $errors[] = "$direction street number is required";
        }

        return $errors;
    }

    ### Check if an address exists with the same, country, city, stret and street number
    ### If it exists, return the address ID
    ### If it does not exist, create a new address and return the new address ID
    public static function createOrUpdateAddress($db_connection, $country, $city, $street, $street_number) {
        $sql_query = "SELECT id FROM address WHERE country = ? AND city = ? AND street = ? AND street_number = ?";

        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
        } else {
            mysqli_stmt_bind_param($prepared_query, 'sssi', $country, $city, $street, $street_number);
            mysqli_stmt_execute($prepared_query);

            $result = mysqli_stmt_get_result($prepared_query);
            $address = mysqli_fetch_assoc($result);

            if ($address) {
                return $address['id'];
            } else {
                $sql_query = "INSERT INTO address (country, city, street, street_number) VALUES (?, ?, ?, ?)";
                $prepared_query = mysqli_prepare($db_connection, $sql_query);

                if ($prepared_query === false) {
                    echo mysqli_error($db_connection);
                } else {
                    mysqli_stmt_bind_param($prepared_query, 'sssi', $country, $city, $street, $street_number);
                    if (mysqli_stmt_execute($prepared_query)) {
                        return mysqli_insert_id($db_connection);
                    } else {
                        echo mysqli_stmt_error($prepared_query);
                    }
                }
            }
        }
        

    }

}

?>