<?php

require '/logistic-company/app/config/db.php';

class AddressService {

    public static function getAddress($id) {
        $conn = getDB();
        $sql = "SELECT * FROM address WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $address = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $address;
    }

    public static function getAllAddresses() {
        $conn = getDB();
        $sql = "SELECT * FROM address";
        $result = mysqli_query($conn, $sql);
        $addresses = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($conn);
        return $addresses;
    }

    public static function createAddress($location_type, $country, $city, $street, $number) {
        $conn = getDB();
        $sql = "INSERT INTO address (location_type, country, city, street, number) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $location_type, $country, $city, $street, $number);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    public static function updateAddress($id, $location_type, $country, $city, $street, $number) {
        $conn = getDB();
        $sql = "UPDATE address SET location_type=?, country=?, city=?, street=?, number=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $location_type, $country, $city, $street, $number, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    public static function deleteAddress($id) {
        $conn = getDB();
        $sql = "DELETE FROM address WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

}
?>
