<?php

### Check if session variable is set and true
function checkAuthentication() {

    return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'];
}

?>