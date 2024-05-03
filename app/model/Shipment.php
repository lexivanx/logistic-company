<?php

class Shipment {

    private $id;
    private $status;
    private $weight; // decimal(6,3)
    private $dateSent;
    private $dateReceived;
    private $deliverToUser; // User object
    private $sentByUser; // User object
    private $deliverer; // User object - role Delivery
    private $registeredBy; // User object - role OfficeEmployee
    private $price; // Price object

    public function __construct($id, $status, $weight, $dateSent, $dateReceived, $deliverToUser, $sentByUser, $deliverer, $registeredBy, $price) {
        $this->id = $id;
        $this->status = $status;
        $this->weight = $weight;
        $this->dateSent = $dateSent;
        $this->dateReceived = $dateReceived;
        $this->deliverToUser = $deliverToUser;
        $this->sentByUser = $sentByUser;
        $this->deliverer = $deliverer;
        $this->registeredBy = $registeredBy;
        $this->price = $price;
    }

}


?>