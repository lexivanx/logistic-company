<?php

require __DIR__ . '/../service/AddressService.php';

class AddressController {

    public static function getAddress($id) {
        $address = AddressService::getAddress($id);
        return $address;
    }

    public static function getAllAddresses() {
        $addresses = AddressService::getAllAddresses();
        return $addresses;
    }

    public static function createAddress($location_type = 'private', $country, $city, $street, $number) {
        return AddressService::createAddress($location_type, $country, $city, $street, $number);
    }

    public static function updateAddress($id, $location_type, $country, $city, $street, $number) {
        return AddressService::updateAddress($id, $location_type, $country, $city, $street, $number);
    }

    public static function deleteAddress($id) {
        return AddressService::deleteAddress($id);
    }
}
?>
