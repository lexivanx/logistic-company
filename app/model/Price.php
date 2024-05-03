<?php

class Price {

    private $id;
    private $weight_class;
    private $price; // decimal(10,2)

    public function __construct($id, $weight_class, $price) {
        $this->id = $id;
        $this->weight_class = $weight_class;
        $this->price = $price;
    }

    public get_weight_class() {
        return $this->weight_class;
    }

    public get_price() {
        return $this->price;
    }
}


?>