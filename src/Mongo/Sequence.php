<?php

namespace Zobr\Toolbox\Mongo;

use MongoDB\Operation\FindOneAndUpdate;

/**
 * Sequence generator using a MongoDB collection.
 * An alternative for AUTO_INCREMENT in MySQL.
 */
class Sequence {

    private $collection;

    public function __construct(Database $mongo) {
        $this->collection = $mongo->getCollection('sequences');
    }

    /**
     * Get the next sequence number for the given name.
     * @param  string $name
     * @return int
     */
    public function get(string $name) {
        $result = $this->collection->findOneAndUpdate([
            '_id' => $name,
        ], [
            '$inc' => [ 'seq' => 1 ],
        ], [
            'upsert' => true,
            'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
        ]);
        $seq = $result->seq;
        return $seq;
    }

    public function set(string $name, int $value) {
        $result = $this->collection->findOneAndUpdate([
            '_id' => $name,
        ], [
            '$set' => [ 'seq' => $value ],
        ], [
            'upsert' => true,
            'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
        ]);
        $seq = $result->seq;
        return $seq;
    }

}
