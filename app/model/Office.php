<?php

class Office {

    private $id;
    private $name;
    private $address; // Address object
    private $company; // Company object

    public function __construct($id, $name, $address, $company) {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->company = $company;
    }
}

?>