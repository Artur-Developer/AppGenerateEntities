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
            [['color', 'date_show', 'batch'], 'required'],
            [['color', 'state', 'date_show', 'date_down', 'size', 'batch'], 'integer'],
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
            'batch' => 'Batch',
            'date_show' => 'Date Show',
            'date_down' => 'Date Down',
            'size' => 'Size',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getEntityInfo(): array
    {
        return [
            'class' => self::className(),
            'entity' => ucfirst(self::tableName()),
            'last_batch' => SettingsType::getLastBatch(
                new self()
            ),
            'entity_ru' => 'яблоки',
            'singular_ru' => 'яблоко',
            'plural_ru' => 'яблок'
        ];
    }

    public function get_color()
    {
        return $this->hasOne(SettingsType::className(), ['id' => 'color']);
    }

    public function get_state()
    {
        return $this->hasOne(SettingsType::className(), ['id' => 'state']);
    }

    public function getLastBatch(): int
    {
        return intval($this->find()->select('batch')->max('batch'));
    }

    public function buildInsert(int $batch_id, array $colors, int $state): array
    {
        return [
            'color' => SettingsType::randColor($colors),
            'date_show' => SettingsType::randTimeStamp(),
            'size' => SettingsType::DEFAULT_ENTITY_SIZE,
            'state' => $state,
            'batch' => $batch_id
        ];
    }

    public function createApple()
    {
        $this->color = SettingsType::randColor(SettingsType::getColors());
        $this->date_show = SettingsType::randTimeStamp();
        $this->size = SettingsType::DEFAULT_ENTITY_SIZE;
        $this->state = SettingsType::getState(SettingsType::STATE_ON_TREE);
        $this->save();
    }

}
