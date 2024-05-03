<?php

### Redirects to a given path on the local server
function redirectToPath($path) {

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {

        $protocol = 'https';

    } else {

        $protocol = 'http';
        
    }

    header("Location: $protocol://" . $_SERVER['HTTP_HOST'] . $path);

    exit;
}

?>