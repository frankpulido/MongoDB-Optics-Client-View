<?php
/**
 * A base model for handling the database connections
 */

class Model {
    protected $collection;
    protected static $db;

    public function __construct() {
        $this->connect();
        $this->collection = self::$db->selectCollection(strtolower(get_class($this)));
    }

    protected function connect() {
        if (!self::$db) {
            require_once(ROOT_PATH . '/config/db.inc.php');
            self::$db = $db;
        }
    }

    public function find($criteria = []) {
        return $this->collection->find($criteria)->toArray();
    }

    public function findOne($criteria = []) {
        return $this->collection->findOne($criteria);
    }

    public function insert($document) {
        return $this->collection->insertOne($document);
    }

    public function update($criteria, $update) {
        return $this->collection->updateMany($criteria, ['$set' => $update]);
    }

    public function delete($criteria) {
        return $this->collection->deleteMany($criteria);
    }
}
?>