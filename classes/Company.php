<?php
class Company {

    ### Function to fetch columns based on DB connection and ID
    ### Fetches all columns by default
    public static function getCompany($db_connection, $id, $columns = '*') {
        $sql_query = "SELECT $columns FROM company WHERE id = ?";

        ## Prevents SQL injection
        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        if ($prepared_query === false ) {
            echo mysqli_error($db_connection);
        } else {
            mysqli_stmt_bind_param($prepared_query, 'i', $id);
            if (mysqli_stmt_execute($prepared_query)) {
                $result = mysqli_stmt_get_result($prepared_query);
                $company = mysqli_fetch_assoc($result);
                return $company;
            } else {
                echo mysqli_stmt_error($prepared_query);
            }
        }
    }

    ### Function to fetch all companies
    public static function getAllCompanies($db_connection) {
        $sql_query = "SELECT * FROM company";

        $result = mysqli_query($db_connection, $sql_query);

        if ($result === false) {
            echo mysqli_error($db_connection);
        } else {
            $companies = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $companies;
        }
    }

}
?>