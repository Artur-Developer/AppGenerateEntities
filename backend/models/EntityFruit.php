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
        $this->entity = $this->entities[$entity];
    }

    public function setRotten()
    {
        return $this->changeState(SettingsType::STATE_ROTTEN);
    }

    public function fallToGround()
    {
        return $this->changeState(SettingsType::STATE_DOWN);
    }

}