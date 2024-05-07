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