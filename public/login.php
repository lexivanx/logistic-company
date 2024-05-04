<?php

require '/logistic-company/app/service/UserService.php';
require '/logistic-company/app/service/http.php';
require '/logistic-company/app/config/db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    ## Fetch connection to DB
    $db_connection = getDB();

    if(UserService::userAuth($_POST['username'], $_POST['password'], $db_connection)) {

        ## Prevent session fixation
        session_regenerate_id(true);
        
        ## Set session variables
        $_SESSION['is_logged_in'] = true;
        $_SESSION['username'] = $_POST['username'];
        ## save user id and role as session variables
        $_SESSION['user_id'] = UserService::getUserId($_POST['username'], $db_connection);
        $_SESSION['role'] = UserService::get_role($_SESSION['user_id']);

        redirectToPath('/logistic-company/public/index.php');

    } else {

        $error = "Username or password are invalid";

    }
    
}

?>

<?php require '/logistic-company/app/view/header.php'; ?>

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

<?php require '/logistic-company/app/view/footer.php'; ?>
