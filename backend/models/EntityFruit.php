<?php

namespace backend\models;

class EntityFruit extends Entities
{
    static $apple = 'Apple';

    public function __construct(string $entity)
    {
        parent::__construct($entity);
        $this->entities = [
            static::$apple => new Apple()
        ];
        $this->entity = $this->getEntity($entity);
    }
}