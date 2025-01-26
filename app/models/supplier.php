<?php
declare(strict_types=1);

class Supplier {
    protected $collection = 'suppliers';
    protected $db;

    public function __construct() {
        global $client;
        $this->db = $client->selectDatabase('optics');
    }

    public function getDatabase() {
        return $this->db;
    }
}
?>