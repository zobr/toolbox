<?php

namespace Zobr\Toolbox;

use DateTime;

abstract class Entity {

    /**
     * Date of entity creation
     * @var \DateTime
     */
    public $createdAt;

    /**
     * Date of entity update
     * @var \DateTime
     */
    public $updatedAt;

    /**
     * Whitelist of fields, initializable from the Request object
     */
    const REQUEST_FIELDS = [];

    /**
     * Initializes the entity with an array-like or object-like data
     * @param  mixed $data An object/array with data to assign to this entity
     * @return static
     */
    public function from($data = null) {
        if (is_object($data)) {
            // Convert to array
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            return $this->fromArray($data);
        }
        return $this;
    }

    /**
     * Initializes the entity with an array.
     * @param  array $data An array with data to assign to this entity
     * @return static
     */
    public function fromArray(array $data) {
        foreach ($data as $i => $value) {
            $this->{$i} = $value;
        }
        return $this;
    }

    /**
     * Initializes the entity with data from the request body (aka POST data).
     * Whitelist of fields must be defined in static::REQUEST_FIELDS.
     * @param array $body
     * @return static
     */
    public function fromRequestBody(array $body) {
        $filtered = [];
        foreach ($body as $i => $value) {
            if (in_array($i, static::REQUEST_FIELDS)) {
                $filtered[$i] = $value;
            }
        }
        return $this->from($filtered);
    }

    /**
     * Converts entity to an array
     * @return array
     */
    public function toArray() {
        $to_array = function ($self) {
            return get_object_vars($self);
        };
        return $to_array($this);
    }

    /**
     * Before save repository hook
     * @return static
     */
    public function beforeSave() {
        // Assign some common Date fields
        if (!isset($this->createdAt)) {
            $this->createdAt = new DateTime();
        }
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * After save repository hook
     * @return static
     */
    public function afterSave() {
        return $this;
    }

    /**
     * Before delete repository hook
     * @return static
     */
    public function beforeDelete() {
        return $this;
    }

    /**
     * Before delete repository hook
     * @return static
     */
    public function afterDelete() {
        return $this;
    }

}
