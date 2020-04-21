<?php

namespace console\commands;

use backend\models\EntityFruit;
use backend\models\SettingsType;
use yii\console\Controller;

class EntityController extends Controller
{
    /**
     *
     * Search for entities that lie on the ground for more than 5 hours
     * It is recommended to install on crowns every 30 minutes
     *
     * Поиск сущностей который долго лежат на земле более 5 часов
     * Рекомендуется устанавливать на крон каждый 30 минут
     */
    public function actionSetRottens()
    {
        $datetime = new \DateTime();
        $datetime->modify("-5 hour");
        $state_rotten = SettingsType::getState(SettingsType::STATE_ROTTEN);
        $state_down = SettingsType::getState(SettingsType::STATE_DOWN);
        foreach (SettingsType::getEntitiesInfo() as $key_entity => $entity){
            $obj_entity = new EntityFruit($entity['entity']);
            $obj_entity->getEntity()->updateAll(
                ['state' => $state_rotten],
                ['and',
                    ['=','state', $state_down],
                    ['<=', 'date_show', $datetime->getTimestamp()]
                ]);
            sleep(2);
        }
    }
}