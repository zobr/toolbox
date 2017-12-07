<?php

namespace Zobr\Toolbox\Mongo;

use MongoDB\Client;

class Database {

    /**
     * Selected MongoDB database
     * @var \MongoDB\Database
     */
    private $db;

    /**
     * MongoDB client
     * @var Client
     */
    private $client;

    public function __construct($uri, $database) {
        $this->client = new Client($uri);
        $this->db = $this->client->{$database};
    }

    /**
     * Returns the collection
     * @param  string $name
     * @return \MongoDB\Collection
     */
    public function getCollection($name) {
        return $this->db->{$name};
    }

    /**
     * Returns the database
     * @return \MongoDB\Database
     */
    public function getDatabase() {
        return $this->db;
    }

}
