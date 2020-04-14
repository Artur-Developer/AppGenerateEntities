<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "apple".
 *
 * @property int $id
 * @property int $color
 * @property int|null $state
 * @property int $date_show
 * @property int|null $date_down
 * @property int $size
 * @property string $create_at
 * @property string $update_at
 *
 * @property SettingsType $color0
 * @property SettingsType $state0
 */
class Apple extends \yii\db\ActiveRecord
{
    const DEFAULT_SIZE = 100;
    const DEFAULT_EAT = 25;
    const STATE_ON_TREE = 'on_tree';
    const STATE_DOWN = 'down';
    const STATE_ROTTEN = 'rotten';

    public static function tableName()
    {
        return 'apple';
    }

    public function rules()
    {
        return [
            [['color', 'date_show'], 'required'],
            [['color', 'state', 'date_show', 'date_down', 'size'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['color'], 'exist', 'skipOnError' => true, 'targetClass' => SettingsType::className(), 'targetAttribute' => ['color' => 'id']],
            [['state'], 'exist', 'skipOnError' => true, 'targetClass' => SettingsType::className(), 'targetAttribute' => ['state' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'color' => 'Color',
            'state' => 'State',
            'date_show' => 'Date Show',
            'date_down' => 'Date Down',
            'size' => 'Size',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getColor()
    {
        return $this->hasOne(SettingsType::className(), ['id' => 'color']);
    }

    public function getState()
    {
        return $this->hasOne(SettingsType::className(), ['id' => 'state']);
    }

    public function buildInsert(array $colors, int $state): array
    {
        return [
            'color' => SettingsType::randColor($colors),
            'date_show' => SettingsType::randTimeStamp(),
            'size' => self::DEFAULT_SIZE,
            'state' => $state
        ];
    }

    public function getSize(): float
    {
        return round($this->size / 100, 2);
    }

    public function eat(int $id)
    {
        $apple = self::findOne($id);
        if($apple->state == SettingsType::getStateApple(self::STATE_ON_TREE)){
//            throw new Exception('Съесть нельзя, яблоко на дереве');
            return 'Съесть нельзя, яблоко на дереве';
        }
        if ($apple->size !== 0){
            $apple->size -= self::DEFAULT_EAT;
            if ($apple->size === 0){
                $apple->delete();
            }
            return $apple->save();
        }
        return false;
    }

    public function setRotten()
    {
        $this->state = SettingsType::getStateApple(self::STATE_ROTTEN);
        return $this->save();
    }

    public function delete()
    {
        return $this->delete();
    }

    public function fallToGround()
    {
        $this->state = SettingsType::getStateApple(self::STATE_DOWN);
        return $this->save();
    }

    public function createApple()
    {
        $this->color = SettingsType::randColor(SettingsType::getColors());
        $this->date_show = SettingsType::randTimeStamp();
        $this->size = self::DEFAULT_SIZE;
        $this->state = SettingsType::getStateApple(self::STATE_ON_TREE);
        $this->save();
    }

}
