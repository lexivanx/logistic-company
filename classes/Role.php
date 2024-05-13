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
            ## Add new role
            if (empty($role_name) || empty($user_id)) {
                echo "Error: Role name and user ID are required.";
                return;
            }
            $sql = "INSERT INTO role (role_name, user_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "si", $role_name, $user_id);
        } else {
            ## Update existing role
            $sql = "UPDATE role SET role_name = ?, user_id = ? WHERE id = ?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "sii", $role_name, $user_id, $role_id);
        }

        if (!$stmt) {
            echo "SQL error: " . mysqli_error($db);
            return;
        }

        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) == 0) {
            echo "No changes made or role not found.";
        }
    }

    ## For admins - print all roles
    public static function fetchAllRoles($db) {
        $sql = "SELECT * FROM role";
        $result = mysqli_query($db, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<p><strong>ID:</strong> {$row['id']}, <strong>Role:</strong> {$row['role_name']}, <strong>User ID:</strong> {$row['user_id']}</p>";
        }
    }

}
?>