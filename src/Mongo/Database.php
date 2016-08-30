<?php

namespace Zobr\Toolbox\Mongo;

use MongoDB\Client;

class Database {

    /**
     * Selected MongoDB database
     * @var \MongoDB\Database
     */
    public $db;

    /**
     * MongoDB client
     * @var Client
     */
    public $client;

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

}
