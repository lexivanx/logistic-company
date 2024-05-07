<?php

## companies CRUD
## offices CRUD
## roles CRUD - update role table to assign a user id to a role
## users CRUD - update pass or office id 
require 'includes/db.php';
require 'includes/authentication.php';
require 'includes/http.php';
require 'includes/queries.php';
require 'classes/Company.php';
require 'classes/Office.php';
require 'classes/User.php';
require 'classes/Role.php';

session_start();

## Fetch connection to DB
$db_connection = getDB();

## If not logged in or not an employee/admin, quit
if (!checkAuthentication() || $_SESSION['user_role'] != "admin") {
    die("You are not authorized to view this page");
}

?>
<?php require 'includes/header.php'; ?>

<h2>Admin Dashboard</h2>

<!-- Companies CRUD -->
<h3>Companies</h3>
<form method="POST">
    <input type="text" name="company_name" placeholder="Enter company name">
    <button type="submit" name="add_company">Add Company</button>
</form>

<?php
if (isset($_POST['submit_company'])) {
    handleCompany($db_connection, $_POST['company_name'], $_POST['company_id'] ?? null);
}
?>

<!-- Offices CRUD -->
<h3>Offices</h3>
<form method="POST">
    <input type="text" name="office_name" placeholder="Office name">
    <input type="number" name="company_id" placeholder="Company ID">
    <input type="number" name="address_id" placeholder="Address ID">
    <button type="submit" name="add_office">Add Office</button>
</form>

<?php
if (isset($_POST['submit_office'])) {
    handleOffice($db_connection, $_POST['office_name'], $_POST['company_id'], $_POST['address_id'], $_POST['office_id'] ?? null);
}
?>

<!-- Roles CRUD -->
<h3>Roles</h3>
<form method="POST">
    <input type="text" name="role_name" placeholder="Role name">
    <input type="number" name="user_id" placeholder="User ID">
    <button type="submit" name="assign_role">Assign Role</button>
</form>

<?php
if (isset($_POST['submit_role'])) {
    handleRole($db_connection, $_POST['role_name'], $_POST['user_id'], $_POST['role_id'] ?? null);
}
?>

<!-- Users CRUD -->
<h3>Users</h3>
<form method="POST">
    <input type="number" name="user_id" placeholder="User ID">
    <input type="text" name="password" placeholder="New Password">
    <input type="number" name="office_id" placeholder="Office ID">
    <button type="submit" name="update_user">Update User</button>
</form>

<?php
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'] ?? null;
    $office_id = $_POST['office_id'];

    User::updateUser($db_connection, $user_id, $password, $office_id);
}

?>

<h3>All companies</h3>
<?php Company::fetchAllCompanies($db_connection); ?>
<h3>All offices</h3>
<?php Office::fetchAllOffices($db_connection); ?>
<h3>All roles</h3>
<?php Role::fetchAllRoles($db_connection); ?>

<?php require 'includes/footer.php'; ?>
