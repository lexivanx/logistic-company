<?php

class Comapny {

    private $id;
    private $name;

    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public get_name() {
        return $this->name;
    }

    public get_id() {
        return $this->id;
    }

}

?>