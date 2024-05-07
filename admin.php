<?php
require 'includes/db.php';
require 'includes/authentication.php';
require 'includes/http.php';
require 'includes/queries.php';
require 'classes/Company.php';
require 'classes/Office.php';
require 'classes/User.php';
require 'classes/Role.php';
require 'classes/Address.php';

session_start();

## Fetch connection to DB
$db_connection = getDB();

## If not logged in or not admin, quit
if (!checkAuthentication() || $_SESSION['user_role'] != "admin") {
    die("You are not authorized to view this page");
}

?>

<?php require 'includes/header.php'; ?>

<h2>Admin Dashboard</h2>

<!-- Companies CRUD -->
<h3>Companies</h3>
<form method="POST">
    <input type="number" name="company_id" placeholder="Company ID (leave blank for new)">
    <input type="text" name="company_name" placeholder="Enter company name">
    <button type="submit" name="submit_company">Save Company</button>
</form>

<?php
if (isset($_POST['submit_company'])) {
    Company::handleCompany($db_connection, $_POST['company_name'], $_POST['company_id'] ?? null);
}
?>

<!-- Offices CRUD -->
<h3>Offices</h3>
<form method="POST">
    <input type="number" name="office_id" placeholder="Office ID (leave blank for new)">
    <input type="text" name="office_name" placeholder="Office name">
    <input type="number" name="company_id" placeholder="Company ID">
    <input type="number" name="address_id" placeholder="Address ID">
    <button type="submit" name="submit_office">Save Office</button>
</form>

<?php
if (isset($_POST['submit_office'])) {
    Office::handleOffice($db_connection, $_POST['office_name'], $_POST['company_id'], $_POST['address_id'], $_POST['office_id'] ?? null);
}
?>

<!-- Roles CRUD -->
<h3>Roles</h3>
<form method="POST">
    <input type="number" name="role_id" placeholder="Role ID (leave blank for new)">
    <input type="text" name="role_name" placeholder="Role name">
    <input type="number" name="user_id" placeholder="User ID">
    <button type="submit" name="submit_role">Save Role</button>
</form>

<?php
if (isset($_POST['submit_role'])) {
    Role::handleRole($db_connection, $_POST['role_name'], $_POST['user_id'], $_POST['role_id'] ?? null);
}
?>

<!-- Users CRUD -->
<h3>Users</h3>
<form method="POST">
    <input type="number" name="user_id" placeholder="User ID (for update)" required>
    <input type="text" name="password" placeholder="New Password (optional)">
    <input type="number" name="office_id" placeholder="Office ID (optional)">
    <button type="submit" name="submit_user">Update User</button>
</form>

<?php
if (isset($_POST['submit_user'])) {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'] ?? null;  // Use null coalescing operator to handle non-set password
    $office_id = $_POST['office_id'] ?? null;  // Handle optional office ID
    User::updateUser($db_connection, $user_id, $password, $office_id);
}
?>

<!-- Addresses CRUD -->
<h3>Addresses</h3>
<form method="POST">
    <input type="number" name="address_id" placeholder="Address ID">
    <button type="submit" name="submit_address">Set Location Type to Office</button>
</form>

<?php
if (isset($_POST['submit_address'])) {
    Address::setLocationTypeToOffice($db_connection, $_POST['address_id']);
}
?>

<h3>All Companies</h3>
<?php Company::fetchAllCompanies($db_connection); ?>
<h3>All Offices</h3>
<?php Office::fetchAllOffices($db_connection); ?>
<h3>All Roles</h3>
<?php Role::fetchAllRoles($db_connection); ?>
<h3>All addresses</h3>
<?php Address::fetchAllAddresses($db_connection); ?>

<?php require 'includes/footer.php'; ?>
