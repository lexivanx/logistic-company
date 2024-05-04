<?php

class User{

    public $id;
    public $username;
    public $password;

    ### Return true if user and pass are correct
    public static function userAuth($username, $password, $db_connection) {
        $sql_query = "SELECT * FROM user WHERE username = ?";

        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        ## Check for error in query
        if ( $prepared_query === false) {

            echo mysqli_error($db_connection);

        } else {

            ## Bind username and execute query
            mysqli_stmt_bind_param($prepared_query, "s", $username);
            mysqli_stmt_execute($prepared_query);

            $result = mysqli_stmt_get_result($prepared_query);
            $user = mysqli_fetch_object($result, 'User');

            ## Verify if hashed pass is correct
            if ($user) {
                return password_verify($password, $user->password);
            }

        }

    }

    ### Return user id by username
    public static function getUserIdByUsername($username, $db_connection) {
        $sql_query = "SELECT id FROM user WHERE username = ?";

        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        ## Check for error in query
        if ( $prepared_query === false) {

            echo mysqli_error($db_connection);

        } else {

            ## Bind username and execute query
            mysqli_stmt_bind_param($prepared_query, "s", $username);
            mysqli_stmt_execute($prepared_query);

            $result = mysqli_stmt_get_result($prepared_query);
            $user = mysqli_fetch_object($result, 'User');

            return $user->id;

        }
    }

    ### Return user_name by querying 'role' table where user_id == id
    public static function getRole($user_id, $db_connection) {
        $sql_query = "SELECT role_name FROM role WHERE user_id = ?";
    
        $prepared_query = mysqli_prepare($db_connection, $sql_query);
    
        // Check for error in preparing the query
        if ($prepared_query === false) {
            echo "Error preparing statement: " . mysqli_error($db_connection);
            return null; // Return null to indicate failure
        } else {
            // Bind user_id and execute query
            mysqli_stmt_bind_param($prepared_query, "i", $user_id);
            mysqli_stmt_execute($prepared_query);
    
            $result = mysqli_stmt_get_result($prepared_query);
    
            // Check for errors after executing the query
            if ($result === false) {
                echo "Error executing query: " . mysqli_error($db_connection);
                return null; // Return null to indicate failure
            }
    
            $role = mysqli_fetch_object($result);
    
            if ($role === null) {
                echo "No role found for user ID: " . $user_id;
                return null; // Return null if no role is found
            }
    
            return $role->role_name; // Return the role name
        }
    }
    
}

?>