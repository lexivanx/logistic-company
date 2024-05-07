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
    public static function handleOffice($db, $office_name, $company_id = null, $address_id = null, $office_id = null) {
        if (empty($office_id)) {
            // Add new office
            if (empty($office_name) || empty($company_id) || empty($address_id)) {
                echo "Error: All fields are required to create a new office.";
                return;
            }
            $sql = "INSERT INTO office (office_name, company_id, address_id) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "sii", $office_name, $company_id, $address_id);
        } else {
            // Update existing office, only if fields are provided
            $sql = "UPDATE office SET ";
            $params = [];
            $types = "";

            if (!empty($office_name)) {
                $sql .= "office_name = ?, ";
                $params[] = $office_name;
                $types .= "s";
            }
            if (!empty($company_id)) {
                $sql .= "company_id = ?, ";
                $params[] = $company_id;
                $types .= "i";
            }
            if (!empty($address_id)) {
                $sql .= "address_id = ?, ";
                $params[] = $address_id;
                $types .= "i";
            }

            // Remove the last comma and space
            $sql = rtrim($sql, ", ");
            $sql .= " WHERE id = ?";
            $params[] = $office_id;
            $types .= "i";

            $stmt = mysqli_prepare($db, $sql);
            if (!$stmt) {
                echo "SQL error: " . mysqli_error($db);
                return;
            }

            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
        }

        if ($stmt && mysqli_stmt_affected_rows($stmt) == 0) {
            echo "No changes made or office not found.";
        }
    }

    ## For admins - print all offices
    public static function fetchAllOffices($db) {
        $sql = "SELECT o.id, o.office_name, c.company_name, o.address_id FROM office o JOIN company c ON o.company_id = c.id";
        $result = mysqli_query($db, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<p><strong>ID:</strong> {$row['id']}, <strong>Office:</strong> {$row['office_name']}, 
            <strong>Company:</strong> {$row['company_name']}, <strong>Address ID:</strong> {$row['address_id']}</p>";
        }
    }

}
?>