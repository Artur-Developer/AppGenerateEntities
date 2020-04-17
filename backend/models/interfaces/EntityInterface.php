<?php

namespace backend\models\interfaces;

interface EntityInterface
{
    public function getEntity(string $entity): object;

    public function deleteEntityToBatches();

    public function getSettingValue(string $setting): array;
}