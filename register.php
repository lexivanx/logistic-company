<?php

require 'includes/http.php';
require 'classes/User.php';
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $full_name = $_POST['full_name'] ?? '';

    $error = '';

    // Basic validation for user and pass
    if (!$username || !$password || !$full_name) {
        $error = 'Username, password and full name are required';
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $error = 'Username should only contain letters and numbers';
    } else {
        // Fetch connection to DB
        $db_connection = getDB();

        // Check if username already exists
        $existing_user_query = mysqli_prepare($db_connection, "SELECT COUNT(*) FROM user WHERE username = ?");
        mysqli_stmt_bind_param($existing_user_query, "s", $username);
        mysqli_stmt_execute($existing_user_query);
        mysqli_stmt_bind_result($existing_user_query, $existing_user_count);
        mysqli_stmt_fetch($existing_user_query);
        mysqli_stmt_close($existing_user_query);

        if ($existing_user_count > 0) {
            $error = 'Username already exists. Please choose a different username.';
        } else {
            // Prepare the query
            $prepared_query = mysqli_prepare($db_connection, "INSERT INTO user (username, password, full_name) VALUES (?, ?, ?)");

            if ($prepared_query === false) {
                $error = mysqli_error($db_connection);
            } else {
                // Secure password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Bind and execute
                mysqli_stmt_bind_param($prepared_query, "sss", $username, $hashed_password, $full_name);

                // Redirect or show success message
                if (mysqli_stmt_execute($prepared_query)) {
                    // add an entry into role table with role_name 'customer' and id the newly created user id
                    $user_id = mysqli_insert_id($db_connection);
                    $role_query = mysqli_prepare($db_connection, "INSERT INTO role (role_name, user_id) VALUES ('customer', ?)");
                    mysqli_stmt_bind_param($role_query, "i", $user_id);
                    mysqli_stmt_execute($role_query);
                    header("Location: views/success-page.php"); // Redirect to a success page
                    exit;
                } else {
                    $error = mysqli_stmt_error($prepared_query);
                }
            }
        }
    }

}

?>

<?php require 'includes/header.php'; ?>

<h4> User registration </h4>

<?php if (!empty($error)) : ?>

    <p class="error-message"><?= $error ?></p>

<?php endif; ?>

<form method="post">

<div>
    <label for="username">Username</label>
    <input type="text" name="username" id="username" placeholder="Only letters and numbers" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : ''; ?>">
</div>

<div>
    <label for="password">Password</label>
    <input type="password" name="password" id="password">
</div>

<div>
    <label for="full_name">Full name</label>
    <input type="text" name="full_name" id="full_name">
</div>

<button type="submit">Register</button>

</form>

<?php require 'includes/footer.php'; ?>
