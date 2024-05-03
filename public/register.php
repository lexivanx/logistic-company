<?php

require __DIR__ . '/../app/model/User.php';
require __DIR__ . '/../app/service/http.php';
require __DIR__ . '/../app/config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $error = '';

    // Basic validation for user and pass
    if (!$username || !$password) {
        $error = 'Username and password are required';
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
            $prepared_query = mysqli_prepare($db_connection, "INSERT INTO user (username, password) VALUES (?, ?)");

            if ($prepared_query === false) {
                $error = mysqli_error($db_connection);
            } else {
                // Secure password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Bind and execute
                mysqli_stmt_bind_param($prepared_query, "ss", $username, $hashed_password);

                if (mysqli_stmt_execute($prepared_query)) {
                    // Redirect or show success message
                    header("Location: /logistic-company/app/view/success-page.php");
                    exit;
                } else {
                    $error = mysqli_stmt_error($prepared_query);
                }
            }
        }
    }

}

?>

<?php require  __DIR__ . '/../app/view/header.php'; ?>

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

<button type="submit">Register</button>

</form>

<?php require  __DIR__ . '/../app/view/footer.php'; ?>
