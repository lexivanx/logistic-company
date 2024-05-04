<?php

require '/logistic-company/app/config/db.php';
require '/logistic-company/app/service/authentication.php';
require '/logistic-company/app/service/AddressService.php'; 

session_start();

require '/logistic-company/app/view/header.php';

// Redirect logic
echo '<div class="employeelinks">';
if (!checkAuthentication() || !($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'employee')) {
    redirectToPath('/logistic-company/public/index.php');
    die("You don't have permission to edit or remove");
}

if ($_SESSION['role'] == 'admin') {
    echo '<a href="admin.php">Admin</a>';
} 

echo '<a href="register.php">Queries</a>';
echo '</div>';

// Handling addresses display
$addresses = AddressService::getAllAddresses();
if (empty($addresses)) {
    echo "<p>No addresses found.</p>";
} else {
    echo "<ul>";
    foreach ($addresses as $address) {
        echo "<li>{$address['street']} {$address['number']}, {$address['city']}, {$address['country']}";
        echo " <a href='/logistic-company/public/address/edit.php?id={$address['id']}' class='edit-link'>Edit</a>";
        echo " <a href='/logistic-company/public/address/remove.php?id={$address['id']}' class='delete-link' onclick='return confirm(\"Are you sure?\");'>Delete</a></li>";
    }
    echo "</ul>";
}

require '/logistic-company/app/view/footer.php';

?>