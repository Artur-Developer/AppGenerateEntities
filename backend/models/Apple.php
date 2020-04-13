<?php

namespace backend\models;

use Yii;
use yii\base\Exception;
use \DateTime;

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
            [['color'], 'unique'],
            [['state'], 'unique'],
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

    public function randDateInRange(DateTime $start, DateTime $end) {
        $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
        $randomDate = new DateTime();
        $randomDate->setTimestamp($randomTimestamp);
        return $randomDate;
    }

    public function randDate() {
        $randomDate = new DateTime();
        $randomDate->setTimestamp(time() - rand(10,1000));
        return $randomDate->getTimestamp();
    }

    public function randColor(){
        $colors = SettingsType::find()->where(['object_name'=>'color_apple'])->asArray()->all();
        return $colors[array_rand($colors)];
    }

    public function eat(){
        if($this->state == self::STATE_ON_TREE){
//            throw new Exception('Съесть нельзя, яблоко на дереве');
            return 'Съесть нельзя, яблоко на дереве';
        }
        if ($this->size !== 0){
            $this->size - self::DEFAULT_EAT;
            if ($this->size === 0){
                $this->delete();
            }
            return $this->save();
        }
        return false;
    }

    public function setRotten(){
        $this->state = self::STATE_ROTTEN;
        return $this->save();
    }

    public function getSize(){
        return $this->size / 100;
    }

    public function delete(){
        return $this->delete();
    }

    public function fallToGround(){
        $this->state = self::STATE_DOWN;
        return $this->save();
    }

    public function createApple(){
        $this->color = $this->randColor();
        $this->date_show = $this->randDate(); // сделать случайным !!!
        $this->size = self::DEFAULT_SIZE;
        $this->state = self::STATE_ON_TREE;
        return $this->save();
    }

}
