<?php 

class Role {

    private $id;
    private $roleName;

    public function __construct($id, $roleName) {
        $this->id = $id;
        $this->roleName = $roleName;
    }
}

?>