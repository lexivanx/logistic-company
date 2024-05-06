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


}
?>