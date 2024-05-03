<?php

class Address {

    private $id;
    private $location_type;
    private $country;
    private $city;
    private $street;
    private $number;

    public function __construct($id, $location_type, $country, $city, $street, $number) {
        $this->id = $id;
        $this->location_type = $location_type;
        $this->country = $country;
        $this->city = $city;
        $this->street = $street;
        $this->number = $number;
    }
}

?>