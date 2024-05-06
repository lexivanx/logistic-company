<?php

## Define a function to sanitize input and prevent security issues
function getQueryType($queryParam) {
    $allowedQueries = ['all', 'by_employee', 'sent', 'by_sender', 'received'];
    if (in_array($queryParam, $allowedQueries)) {
        return $queryParam;
    }
    ## default to 'all' if an invalid query type is provided
    return 'all'; 
}


?>