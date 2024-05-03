<?php

require __DIR__ . '/../config/db.php';

class UserService {

    public static function getUserId($username, $db_connection) {
        $conn = getDB();
        $sql = "SELECT id FROM user WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        $id = mysqli_fetch_assoc($result)['id'];
        mysqli_close($conn);
        return $id;
    }

    // Fetch a user's role by his id
    public static function get_role($id) {
        $conn = getDB();
        $sql = "SELECT role_id FROM user WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        $role_id = mysqli_fetch_assoc($result)['role_id'];
        $sql = "SELECT role_name FROM role WHERE id = $role_id";
        $result = mysqli_query($conn, $sql);
        $role_name = mysqli_fetch_assoc($result)['role_name'];
        mysqli_close($conn);
        return $role_name;
    }

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

}

?>