<?php

namespace Zobr\Toolbox\Mongo;

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Regex;
use Zobr\Toolbox\Repository as BaseRepository;
use Zobr\Toolbox\Mongo\Entity;

/**
 * Service for retrieving entities from MongoDB. Has some basic methods for
 * doing some basic operations. Operates on entities that inherit from
 * Entity.
 * @method Entity make()
 * @method Entity ensureIdentity($id)
 */
abstract class Repository extends BaseRepository {

    /**
     * @var \MongoDB\Collection
     */
    protected $collection;

    /**
     * Dummy method for rebuilding indexes when collection state changes.
     * @return void
     */
    public function rebuildIndex() {

    }

    /**
     * Find entities matching criteria
     * @param  array $criteria
     * @return Entity[]
     */
    public function find(array $criteria = [], array $options = []) {
        $cursor = $this->collection->find($criteria, $options);
        $entities = [];
        foreach ($cursor as $doc) {
            $entities[(string) $doc->_id] = $this
                ->ensureIdentity($doc->_id, function () use ($doc) {
                    return $this->make()->fromMongoDocument($doc);
                });
        }
        return $entities;
    }

    /**
     * Find one entity matching criteria
     * @param  array $criteria
     * @return Entity|null
     */
    public function findOne(array $criteria = [], array $options = []) {
        $doc = $this->collection->findOne($criteria, $options);
        if (!$doc) {
            return null;
        }
        $entity = $this
            ->ensureIdentity($doc->_id, function () use ($doc) {
                return $this->make()->fromMongoDocument($doc);
            });
        return $entity;
    }

    /**
     * Get one entity by _id
     * @param  ObjectID|string $id
     * @return Entity|null
     */
    public function getById($id) {
        return $this->findOne([
            '_id' => new ObjectID((string) $id),
        ]);
    }

    /**
     * Get one entity by sequential id
     * @param  ObjectID|string $id
     * @return Entity|null
     */
    public function getBySeqId(int $id) {
        return $this->findOne([
            'seqId' => $id,
        ]);
    }

    /**
     * Get all entities from the collection
     * @return Entity[]
     */
    public function getAll() {
        return $this->find();
    }

    /**
     * Save the entity to the collection
     * @param  Entity $entity
     * @return self
     */
    public function save(Entity $entity) {
        $entity->beforeSave();
        $doc = $entity->toMongoDocument();
        if (!isset($entity->_id)) {
            $result = $this->collection->insertOne($doc);
            $entity->_id = $result->getInsertedId();
        } else {
            $selector = [ '_id' => $entity->_id ];
            $result = $this->collection->replaceOne($selector, $doc);
        }
        $entity->afterSave();
        $this->rebuildIndex();
        return $this;
    }

    /**
     * Save the entity to the collection
     * @param  Entity $entity
     * @return self
     */
    public function delete(Entity $entity) {
        $entity->beforeDelete();
        $doc = $entity->toMongoDocument();
        $this->collection->deleteOne($doc);
        $entity->afterDelete();
        return $this;
    }

}
