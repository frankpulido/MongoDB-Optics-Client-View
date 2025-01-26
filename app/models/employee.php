<?php
declare(strict_types=1);

class Employee {
    protected $collection = 'employees';
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