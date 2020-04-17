<?php
namespace backend\models;

use backend\models\interfaces\EntityInterface;
use yii\helpers\ArrayHelper;

abstract class Entities implements EntityInterface
{
    public $entities;
    public $entity;

    /**
     * Entities constructor.
     * @param string $entity
     * examples
     * $this->entities = [
     *      "Apple" => new Apple(),
     * ];
     * $this->entity = "Apple";
     */
    public function __construct(string $entity)
    {
        $this->entities = [];
        $this->entity = new class{};
    }

    /**
     * @param string $entity for find object in $this->entities
     * @return object
     * example "Apple" = new Apple()
     */
    public function getEntity(string $entity): object
    {
        return $this->entities[$entity];
    }

    /**
     * delete all post current entity to batch
     * with interval 1 second
     * @return string -> count post entity in db
     */
    public function deleteEntityToBatches(): string
    {
        while ($this->entity->find()->count() > 0){
            sleep(1);
            $models = $this->entity->find()->select('id')->limit(500)->column();
            $this->entity->deleteAll(['in','id',$models]);
        }
        return $this->entity->find()->count();
    }

    /**
     * @return string
     * return count current entity in db
     */
    public function getCount(): string
    {
        return $this->entity->find()->count();
    }

    /**
     * @param string $setting - setting_name
     * example "state" or "color" where is relationships with entity
     * @return array
     * example return array
     * return [
     *      setting.id => setting.value
     * ]
     * result Array (
     *  [4] => #1E90FF
     *  [5] => #008000
     * )
     */
    public function getSettingValue(string $setting): array
    {
        return ArrayHelper::map($this->entity->find()->alias('f')->select(['s.id','s.value'])
            ->innerJoin(['s' => SettingsType::tableName()],'s.id=f.' . $setting)->groupBy('s.id')->asArray()->all(),'id','value');
    }

    public function getColors(): array
    {
        return $this->getSettingValue(SettingsType::PARAMS_COLOR);
    }

    public function getStates(): array
    {
        return $this->getSettingValue(SettingsType::PARAMS_STATE);
    }
}
