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

## Function to calculate revenue
function calculateRevenue($db, $companyId = null, $startDate, $endDate) {
    // Base SQL query to calculate the sum of exact_price from shipments that are paid within the given date range
    $sql = "SELECT SUM(exact_price) AS totalRevenue FROM shipment 
            WHERE is_paid = 1 AND date_sent BETWEEN ? AND ?";

    // Conditionally add a filter for companyId if provided
    if ($companyId !== null) {
        $sql .= " AND company_id = ?";
        $stmt = mysqli_prepare($db, $sql);
        // Bind parameters including companyId
        mysqli_stmt_bind_param($stmt, "ssi", $startDate, $endDate, $companyId);
    } else {
        $stmt = mysqli_prepare($db, $sql);
        // Bind parameters without companyId
        mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
    }

    // Execute the prepared statement
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the result and return the total revenue
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['totalRevenue'] : 0; // Return 0 if there's no revenue
}

?>