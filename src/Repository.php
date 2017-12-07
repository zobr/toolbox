<?php

namespace Zobr\Toolbox;

/**
 * Service for retrieving entities from memory or another type of storage.
 * It also serves as an entity factory and an identity manager, because they
 * are usually used together with repositories.
 * This is an abstract class, where you need to implement your own entity
 * retrieval mechanisms.
 */
abstract class Repository {

    protected $class;

    /**
     * Holds instances of all entities.
     * @var Entity[]
     */
    protected $identityMap = [];

    /**
     * Creates an entity (basic factory)
     * @return Entity
     */
    public function make() {
        return new $this->class();
    }

    /**
     * Creates an entity with the specified identifier.
     * Entities with the same identifier will reference to the same entity
     * object. If new identity is created, it calls the callback, where you
     * can manually create an entity.
     * @param  mixed $id
     * @param  callable|null $onNewIdentity
     * @return Entity
     */
    public function ensureIdentity($id, callable $onNewIdentity = null) {
        // Find the existing identity
        if (isset($this->identityMap[(string) $id])) {
            return $this->identityMap[(string) $id];
        }
        // Create a new identity
        if ($onNewIdentity) {
            // Manual via callback bound to repository
            $entity = $onNewIdentity->bindTo($this)();
        } else {
            // Automatic via a simple factory
            $entity = $this->make();
        }
        if ($entity) {
            $this->identityMap[(string) $id] = $entity;
        }
        return $entity;
    }

}
