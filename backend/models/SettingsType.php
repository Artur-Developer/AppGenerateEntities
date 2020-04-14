<?php

namespace backend\models;

use Yii;
use \DateTime;

/**
 * This is the model class for table "refs_settings_type".
 *
 * @property int $id
 * @property string $object_name
 * @property string $title
 * @property string $code
 * @property string $value
 * @property string $create_at
 * @property string $update_at
 */
class SettingsType extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'refs_settings_type';
    }

    public function rules()
    {
        return [
            [['object_name', 'title', 'code', 'value'], 'required'],
            [['value'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['object_name', 'title', 'code'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'object_name' => 'Object Name',
            'title' => 'Title',
            'code' => 'Code',
            'value' => 'Value',
            'created_at' => 'Create At',
            'updated_at' => 'Update At',
        ];
    }

    public static function getColors(): array
    {
        return static::find()->where(['object_name'=>'color_apple'])->asArray()->all();
    }

    public static function getStateApple($state): int
    {
        return static::find()->where(['object_name' => 'state_apple', 'code' => $state])->one()->id;
    }

    public function randDateInRange(DateTime $start, DateTime $end) {
        $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
        $randomDate = new DateTime();
        $randomDate->setTimestamp($randomTimestamp);
        return $randomDate;
    }

    public static function randTimeStamp(): int
    {
        $randomDate = new DateTime();
        $randomDate->setTimestamp(time() - rand(10,1000));
        return $randomDate->getTimestamp();
    }

    public static function randColor(array $colors): int
    {
        return $colors[array_rand($colors)]['id'];
    }
}
