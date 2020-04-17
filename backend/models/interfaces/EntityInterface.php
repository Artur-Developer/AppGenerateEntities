<?php

namespace backend\models\interfaces;

interface EntityInterface
{
    public function getEntity(): object;

    public function deleteEntityToBatches();

    public function getSettingValue(string $setting): array;
}