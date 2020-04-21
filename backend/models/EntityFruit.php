<?php

namespace backend\models;

class EntityFruit extends Entities
{
    static $apple = 'Apple';

    public function __construct(string $entity)
    {
        $entities = [
            static::$apple => new Apple()
        ];
        parent::__construct($entities,$entity);
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