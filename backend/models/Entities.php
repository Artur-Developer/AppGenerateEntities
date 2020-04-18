<?php
namespace backend\models;

use backend\models\interfaces\EntityInterface;
use yii\base\InvalidArgumentException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

abstract class Entities implements EntityInterface
{
    protected $entities;
    protected $entity;
    protected $found_object;

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
        $this->entity = $this->entities[$entity];
    }

    /**
     * @param string
     * $entity for find object in $this->entities
     * @return object
     * example "Apple" = new Apple()
     */
    public function getEntity(): object
    {
        return $this->entity;
    }

    /**
     * delete all post current entity to batch
     * with interval 1 second
     * @return string -> count post entity in db
     */
    public function deleteEntityToBatches(): string
    {
        while ($this->entity->find()->count() > 0){
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

    public function getSize(): float
    {
        return round($this->found_object->size / 100, 2);
    }

    /**
     * find object entity to {id}
     * @param int $id
     * @return object
     * @throws Exception
     */
    public function findEntity(int $id): object
    {
        $this->found_object = $this->entity->findOne($id);
        if (!isset($this->found_object->id)){
            throw new Exception('Object not found');
        }
        return $this->found_object;
    }

    /**
     * @return object
     */
    public function getFoundObject(): object
    {
        return $this->found_object;
    }

    /**
     * the function subtracts the transmitted
     * percentage of health from the found object
     * @param int $percent
     * @return bool
     * @throws Exception
     */
    public function eat(int $percent = SettingsType::DEFAULT_ENTITY_EAT)
    {
        if ($percent < 1 || $percent > 100){
            throw new InvalidArgumentException('percent should be from 1 to 100');
        }
        if($this->found_object->state == SettingsType::getState(SettingsType::STATE_ON_TREE)){
            throw new Exception('You can’t eat, an apple on a tree');
        }
        if ($this->found_object->size !== 0){
            $this->found_object->size -= $percent;
            if ($this->found_object->size === 0){
                $this->found_object->delete();
            }
            return $this->found_object->save();
        }
        return false;
    }

    /**
     * the function change exists state for found object
     * @param string $state
     * @return bool
     */
    public function changeState(string $state)
    {
        $this->found_object->state = SettingsType::getState($state);
        return $this->found_object->save();
    }
}
