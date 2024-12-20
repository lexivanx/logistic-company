<?php

require 'includes/http.php';
require 'classes/User.php';
require 'includes/db.php';
require 'classes/Company.php';

session_start();

 ## Fetch connection to DB
 $db_connection = getDB();
 $companies = Company::getAllCompanies($db_connection);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(User::userAuth($_POST['username'], $_POST['password'], $db_connection)) {

        ## Prevent session fixation
        session_regenerate_id(true);
        
        ## Set session variables
        $_SESSION['is_logged_in'] = true;
        $_SESSION['username'] = $_POST['username'];
        ## Get the user id
        $_SESSION['user_id'] = User::getUserIdByUsername($_SESSION['username'], $db_connection);
        $_SESSION['user_role'] = User::getRole($_SESSION['user_id'], $db_connection);
        $_SESSION['full_name'] = User::getUserFullNameById($_SESSION['user_id'], $db_connection);

        ## Set only for employees for filtering purposes
        ##if ($_SESSION['user_role'] == "employee") {
        ##    $_SESSION['company_id'] = User::getCompanyIdByUserId($_SESSION['user_id'], $db_connection);
        ##} 

        $_SESSION['company_id'] = $_POST['login_company_id'];

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

    <div>
        <label for="login_company_id">Company</label>
        <select name="login_company_id" id="login_company_id">
            <?php foreach ($companies as $company) : ?>
                <option value="<?= $company['id'] ?>"><?= htmlspecialchars($company['company_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit">Login</button>

</form>

<?php require 'includes/footer.php'; ?>
