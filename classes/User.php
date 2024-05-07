<?php

class User {

    ### Return true if user and pass are correct
    public static function userAuth($username, $password, $db_connection) {
        $sql_query = "SELECT * FROM user WHERE username = ?";

        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        ## Check for error in query
        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
        } else {
            ## Bind username and execute query
            mysqli_stmt_bind_param($prepared_query, "s", $username);
            mysqli_stmt_execute($prepared_query);

            $result = mysqli_stmt_get_result($prepared_query);
            $user = mysqli_fetch_assoc($result);

            ## Verify if hashed pass is correct
            if ($user) {
                return password_verify($password, $user['password']);
            }
        }
        return false; // Return false if no user is found or on error
    }

    ### Return user id by username
    public static function getUserIdByUsername($username, $db_connection) {
        $sql_query = "SELECT id FROM user WHERE username = ?";

        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        ## Check for error in query
        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
        } else {
            ## Bind username and execute query
            mysqli_stmt_bind_param($prepared_query, "s", $username);
            mysqli_stmt_execute($prepared_query);

            $result = mysqli_stmt_get_result($prepared_query);
            $user = mysqli_fetch_assoc($result);

            return $user ? $user['id'] : null;
        }
    }

    ### Return full_name field of user by user id
    public static function getUserFullNameById($user_id, $db_connection) {
        $sql_query = "SELECT full_name FROM user WHERE id = ?";

        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        ## Check for error in query
        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
            return null;
        } else {
            ## Bind user_id and execute query
            mysqli_stmt_bind_param($prepared_query, "i", $user_id);
            mysqli_stmt_execute($prepared_query);

            $result = mysqli_stmt_get_result($prepared_query);
            $user = mysqli_fetch_assoc($result);

            return $user ? $user['full_name'] : null;
        }
    }

    ### Return id field of user by full_name
    public static function getUserIdByFullName($full_name, $db_connection) {
        $sql_query = "SELECT id FROM user WHERE full_name = ?";

        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        ## Check for error in query
        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
            return null;
        } else {
            ## Bind full_name and execute query
            mysqli_stmt_bind_param($prepared_query, "s", $full_name);
            mysqli_stmt_execute($prepared_query);

            $result = mysqli_stmt_get_result($prepared_query);
            $user = mysqli_fetch_assoc($result);

            return $user ? $user['id'] : null;
        }
    }

    ## Return company_id by retrieving office_id of user and then retrieving the company_id from the office table by the retrieved office_id
    public static function getCompanyIdByUserId($user_id, $db_connection) {
        $sql_query = "SELECT company_id FROM office WHERE id = (SELECT office_id FROM user WHERE id = ?)";

        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        ## Check for error in query
        if ($prepared_query === false) {
            echo mysqli_error($db_connection);
            return null;
        } else {
            ## Bind user_id and execute query
            mysqli_stmt_bind_param($prepared_query, "i", $user_id);
            mysqli_stmt_execute($prepared_query);

            $result = mysqli_stmt_get_result($prepared_query);
            $office = mysqli_fetch_assoc($result);

            return $office ? $office['company_id'] : null;
        }
    }

    ### Return role_name by querying 'role' table where user_id == id
    public static function getRole($user_id, $db_connection) {
        $sql_query = "SELECT role_name FROM role WHERE user_id = ?";

        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        if ($prepared_query === false) {
            echo "Error preparing statement: " . mysqli_error($db_connection);
            return null;
        } else {
            // Bind user_id and execute query
            mysqli_stmt_bind_param($prepared_query, "i", $user_id);
            mysqli_stmt_execute($prepared_query);

            $result = mysqli_stmt_get_result($prepared_query);

            if ($result === false) {
                echo "Error executing query: " . mysqli_error($db_connection);
                return null;
            }

            $role = mysqli_fetch_assoc($result);

            if (!$role) {
                echo "No role found for user ID: " . $user_id;
                return null;
            }

            return $role['role_name'];
        }
    }

    ### Function to validate if address fields are empty
    public static function getUserShipmentErrs($sender_name, $recipient_name, $delivery_name, $db_connection) {
        $errors = [];

        if (empty($sender_name)) {
            $errors[] = "Sender can't be empty!";
        }
        if (!User::getUserIdByFullName($sender_name, $db_connection)) {
            $errors[] = "Sender name does not exist!";
        }
        if (!User::getUserIdByFullName($recipient_name, $db_connection) && !empty($recipient_name)) {
            $errors[] = "Recipient not registered! Please leave blank";
        }
        if (!User::getUserIdByFullName($delivery_name, $db_connection) && !empty($delivery_name)) {
            $errors[] = "No such driver exists!";
        }

        return $errors;
    }

    ## Function to fetch customers
    public static function fetchCustomers($db) {
        $sql = "SELECT u.id, u.username, u.full_name 
            FROM user u 
            JOIN role r ON u.id = r.user_id 
            WHERE r.role_name = 'customer'";
        $result = mysqli_query($db, $sql);
        if (!$result) {
            die('MySQL error: ' . mysqli_error($db));
        }
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    ## Function to fetch employees
    public static function fetchEmployees($db) {
        $sql = "SELECT u.id, u.username, u.office_id, u.full_name 
            FROM user u 
            JOIN role r ON u.id = r.user_id 
            WHERE r.role_name = 'employee'";
        $result = mysqli_query($db, $sql);
        if (!$result) {
            die('MySQL error: ' . mysqli_error($db));
        }
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    ## Update user
    public static function updateUser($db, $user_id, $password = null, $office_id = null) {
        if (empty($user_id)) {
            echo "Error: User ID is required.";
            return;
        }

        $sql = "UPDATE user SET ";
        $params = [];
        $types = "";

        if (!empty($password)) {
            $sql .= "password = ?, ";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
            $types .= "s";
        }
        if (!empty($office_id)) {
            $sql .= "office_id = ?, ";
            $params[] = $office_id;
            $types .= "i";
        }

        $sql = rtrim($sql, ", ");
        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = mysqli_prepare($db, $sql);
        if (!$stmt) {
            echo "SQL error: " . mysqli_error($db);
            return;
        }

        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) == 0) {
            echo "No changes made or user not found.";
        }
    }  

}
?>