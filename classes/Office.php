<?php
class Office {

    ### Function to fetch columns based on DB connection and ID
    ### Fetches all columns by default
    public static function getOffice($db_connection, $id, $columns = '*') {
        $sql_query = "SELECT $columns FROM office WHERE id = ?";

        ## Prevents SQL injection
        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        if ($prepared_query === false ) {
            echo mysqli_error($db_connection);
        } else {
            mysqli_stmt_bind_param($prepared_query, 'i', $id);
            if (mysqli_stmt_execute($prepared_query)) {
                $result = mysqli_stmt_get_result($prepared_query);
                $office = mysqli_fetch_assoc($result);
                return $office;
            } else {
                echo mysqli_stmt_error($prepared_query);
            }
        }
    }

    ## Add or update(if id is specified)
    public static function handleOffice($db, $office_name, $company_id, $address_id, $office_id = null) {
        if (empty($office_id)) {
            // Add new office
            $sql = "INSERT INTO office (office_name, company_id, address_id) VALUES (?, ?, ?)";
        } else {
            // Update existing office
            $sql = "UPDATE office SET office_name = ?, company_id = ?, address_id = ? WHERE id = ?";
        }
        $stmt = mysqli_prepare($db, $sql);
        if ($stmt === false) {
            echo mysqli_error($db);
            return;
        }
    
        if (empty($office_id)) {
            mysqli_stmt_bind_param($stmt, "sii", $office_name, $company_id, $address_id);
        } else {
            mysqli_stmt_bind_param($stmt, "siii", $office_name, $company_id, $address_id, $office_id);
        }
        mysqli_stmt_execute($stmt);
    }

    ## For admins - print all offices
    function fetchAllOffices($db) {
        $sql = "SELECT o.id, o.office_name, c.company_name, o.address_id FROM office o JOIN company c ON o.company_id = c.id";
        $result = mysqli_query($db, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<p>ID: {$row['id']}, Office: {$row['office_name']}, Company: {$row['company_name']}, Address ID: {$row['address_id']}</p>";
        }
    }

}
?>