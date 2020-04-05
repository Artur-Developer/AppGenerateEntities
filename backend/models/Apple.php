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

    public function getColor0()
    {
        return $this->hasOne(SettingsType::className(), ['id' => 'color']);
    }

    public function getState0()
    {
        return $this->hasOne(SettingsType::className(), ['id' => 'state']);
    }
}
