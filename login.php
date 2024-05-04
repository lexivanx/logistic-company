<?php

require 'includes/http.php';
require 'classes/User.php';
require 'includes/db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    ## Fetch connection to DB
    $db_connection = getDB();

    if(User::userAuth($_POST['username'], $_POST['password'], $db_connection)) {

        ## Prevent session fixation
        session_regenerate_id(true);
        
        ## Set session variables
        $_SESSION['is_logged_in'] = true;
        $_SESSION['username'] = $_POST['username'];
        // Get the user id
        $_SESSION['user_id'] = User::getUserIdByUsername($_SESSION['username'], $db_connection);
        $_SESSION['user_role'] = User::getRole($_SESSION['user_id'], $db_connection);

        redirectToPath('/logistic-company/index.php');

    } else {

        $error = "Username or password are invalid";

    }
    
}

?>

<?php require 'includes/header.php'; ?>

<h4> User login </h4>

<?php if (!empty($error)) : ?>

    <p class="error-message"><?= $error ?></p>

<?php endif; ?>

<form method="post">
    
    <div>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : ''; ?>">
    </div>

    <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
    </div>

    <button type="submit">Login</button>

</form>

<?php require 'includes/footer.php'; ?>
