<?php

require __DIR__ . '/../app/config/db.php';
require __DIR__ . '/../app/service/authentication.php';
require __DIR__ . '/../controller/AddressController.php';
require __DIR__ . '/../app/model/User.php';

session_start();

require __DIR__ . '/../app/view/header.php';

// Redirect logic
echo '<div class="employeelinks">';
if (!checkAuthentication() || !(User::get_role($_SESSION['user_id']) == 'admin' || User::get_role($_SESSION['user_id']) == 'employee')) {
    redirectToPath('/logistic-company/public/index.php');
    exit;
} else if (User::get_role($_SESSION['user_id']) == 'admin') {
    echo '<a href="admin.php">Admin</a>';
} 
echo '<a href="register.php">Queries</a>';
echo '</div>';

// Handling addresses display
$addresses = AddressController::getAllAddresses();
if (empty($addresses)) {
    echo "<p>No addresses found.</p>";
} else {
    echo "<ul>";
    foreach ($addresses as $address) {
        echo "<li>{$address['street']} {$address['number']}, {$address['city']}, {$address['country']}";
        echo " <a href='/logistic-company/app/controller/edit-address.php?id={$address['id']}' class='edit-link'>Edit</a>";
        echo " <a href='/logistic-company/app/controller/remove-address.php?id={$address['id']}' class='delete-link' onclick='return confirm(\"Are you sure?\");'>Delete</a></li>";
    }
    echo "</ul>";
}

require __DIR__ . '/../app/view/footer.php';

?>