<?php

namespace backend\models;

use Yii;

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
}
