<?php

namespace Zobr\Toolbox\Mongo;

use DateTime;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDatetime;
use MongoDB\Model\BSONArray;
use Zobr\Toolbox\Entity as BaseEntity

abstract class Entity extends BaseEntity {

    /**
     * Basic document schema
     * Examples: [
     *     '<field>' => 'ref' // Document reference
     *     '<field>' => 'ref[]' // Array of references
     *     '<field>' => 'floatval' // Apply 'floatval' function to value
     * ]
     */
    const SCHEMA = [];

    /**
     * MongoDB document identifier
     * @var \MongoDB\BSON\ObjectID
     */
    public $_id;

    /**
     * Initializes the entity with an array returned by the MongoDB driver
     * @param $data
     * @return static
     */
    public function fromMongoDocument($data) {
        foreach ($data as $i => $value) {
            if ($value instanceof UTCDatetime) {
                $data[$i] = $value->toDateTime();
            }
            if ($value instanceof BSONArray) {
                $data[$i] = (array) $value;
            }
        }
        return $this->from($data);
    }

    /**
     * Converts entity into an ObjectId reference
     * @return \MongoDB\BSON\ObjectID
     */
    public function toMongoRef() {
        return new ObjectID($this->_id);
    }

    /**
     * Converts the entity to an array compatible with the MongoDB driver
     * @return array
     */
    public function toMongoDocument() {
        $doc = $this->toArray();
        foreach ($doc as $i => $value) {
            // Unset null fields
            if (is_null($value)) {
                unset($doc[$i]);
                continue;
            }
            // Convert _id into ObjectID
            if ($i === '_id' && is_string($value)) {
                $doc[$i] = $this->toMongoRef();
                continue;
            }
            // Enforce schema
            if (isset(static::SCHEMA[$i])) {
                $transform = static::SCHEMA[$i];
                if ($transform === 'ref') {
                    $doc[$i] = new ObjectID($value);
                    continue;
                }
                if ($transform === 'ref[]') {
                    $doc[$i] = [];
                    foreach ($value as $x) {
                        $doc[$i][] = new ObjectID($x);
                    }
                    continue;
                }
                if (is_callable($transform)) {
                    $doc[$i] = $transform($value);
                    continue;
                }
            }
            // Convert dates
            if ($value instanceof DateTime) {
                $doc[$i] = new UTCDateTime($value->getTimestamp() . '000');
                continue;
            }
        }
        return $doc;
    }

}
