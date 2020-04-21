<?php

namespace backend\models\interfaces;

interface EntityInterface
{
    public function getEntity(): object;

    public function deleteEntityToBatches();

    public function getSettingValue(string $setting): array;

    public function findEntity(int $id): object;

    public function changeState(string $state);
}