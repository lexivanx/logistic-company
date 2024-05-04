<?php

require '/logistic-company/app/service/http.php';

session_start();

### Unset session vars and destroy session
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 86400,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

redirectToPath('/logistic-company/public/index.php');

?>