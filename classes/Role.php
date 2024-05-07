<?php
class Role {

    ### Function to fetch columns based on DB connection and ID
    ### Fetches all columns by default
    public static function getRole($db_connection, $id, $columns = '*') {
        $sql_query = "SELECT $columns FROM role WHERE id = ?";

        ## Prevents SQL injection
        $prepared_query = mysqli_prepare($db_connection, $sql_query);

        if ($prepared_query === false ) {
            echo mysqli_error($db_connection);
        } else {
            mysqli_stmt_bind_param($prepared_query, 'i', $id);
            if (mysqli_stmt_execute($prepared_query)) {
                $result = mysqli_stmt_get_result($prepared_query);
                $role = mysqli_fetch_assoc($result);
                return $role;
            } else {
                echo mysqli_stmt_error($prepared_query);
            }
        }
    }

    ## Add or update(if id is specified)
    public static function handleRole($db, $role_name, $user_id, $role_id = null) {
        if (empty($role_id)) {
            // Add new role
            $sql = "INSERT INTO role (role_name, user_id) VALUES (?, ?)";
        } else {
            // Update existing role
            $sql = "UPDATE role SET role_name = ?, user_id = ? WHERE id = ?";
        }
        $stmt = mysqli_prepare($db, $sql);
        if ($stmt === false) {
            echo mysqli_error($db);
            return;
        }
    
        if (empty($role_id)) {
            mysqli_stmt_bind_param($stmt, "si", $role_name, $user_id);
        } else {
            mysqli_stmt_bind_param($stmt, "sii", $role_name, $user_id, $role_id);
        }
        mysqli_stmt_execute($stmt);
    }

    ## For admins - print all roles
    function fetchAllRoles($db) {
        $sql = "SELECT * FROM role";
        $result = mysqli_query($db, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<p>ID: {$row['id']}, Role: {$row['role_name']}, User ID: {$row['user_id']}</p>";
        }
    }

}
?>