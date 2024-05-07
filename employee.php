<?php

require 'includes/db.php';
require 'includes/authentication.php';
require 'includes/http.php';
require 'includes/queries.php';
require 'classes/User.php';
require 'classes/Shipment.php';
require 'classes/Office.php';

session_start();

## Fetch connection to DB
$db_connection = getDB();

## If not logged in or not an employee/admin, quit
if (!checkAuthentication() || ($_SESSION['user_role'] != "admin" && $_SESSION['user_role'] != "employee")) {
    die("You are not authorized to view this page");
}

?>

<?php require 'includes/header.php'; ?>
<h3>Employee Portal</h3>

<?php
    $customers = User::fetchCustomers($db_connection);
    $employees = User::fetchEmployees($db_connection);

    ## Display customers
    echo "<h2>Customers</h2>";
    if (!empty($customers)) {
        foreach ($customers as $customer) {
            echo "<p>ID: {$customer['id']}, Username: {$customer['username']}, Full Name: {$customer['full_name']}</p>";
        }
    } else {
        echo "<p>No customers found.</p>";
    }

    ## Display Employees
    echo "<h2>Employees</h2>";
    if (!empty($employees)) {
        foreach ($employees as $employee) {
            $office = Office::getOffice($db_connection, $employee['office_id'], 'office_name');
            $office_name = $office['office_name'];
            echo "<p>ID: {$employee['id']}, Username: {$employee['username']}, Office ID: {$employee['office_id']},
            Office name: {$office_name}, Employee Name: {$employee['full_name']}</p>";
        }
    } else {
        echo "<p>No employees found.</p>";
    }

    ## Revenue Reports
    ## for employees - revenue for CURRENT company for a given time period
    ## for admins - revenue for ANY given company for a given time period
    if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
        $startDate = $_GET['start_date'];
        $endDate = $_GET['end_date'];
        if ($_SESSION['user_role'] == "admin") {
            $company_id = $_GET['query_company_id'];
        }
        
    
        ## Ensure end date is not before start date
        if ($endDate >= $startDate) {
            if ($_SESSION['user_role'] == "employee") {
                $revenue = calculateRevenue($db_connection, $_SESSION['company_id'], $startDate, $endDate);
                echo "<h3>Revenue for period from $startDate to $endDate: $revenue</h3>";
            } elseif ($_SESSION['user_role'] == "admin") {
                ## If query_company_id is not entered in the form
                if (empty($company_id)) {
                    $revenue = calculateRevenue($db_connection, null, $startDate, $endDate);
                } else {
                    $revenue = calculateRevenue($db_connection, $company_id, $startDate, $endDate);
                }
                echo "<h3>Revenue for period from $startDate to $endDate: $revenue</h3>";
            }
        } else {
            echo "<p>Error: End date must be on or after the start date.</p>";
        }
    
    }
?>
<form method="GET" action="">
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" required>
    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" required>
    <?php if ($_SESSION['user_role'] == "admin"): ?>
        <label for="query_company_id">Company ID:</label>
        <input id="query_company_id" name="query_company_id" placeholder="Optional for total revenue">
    <?php endif; ?>
    <button type="submit">Calculate Revenue</button>
</form>


<?php require 'includes/footer.php'; ?>