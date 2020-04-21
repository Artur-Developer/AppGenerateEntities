<?php

namespace backend\models;

use Yii;
use \DateTime;
use backend\models\Apple;

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
    const PARAMS_STATE = 'state';
    const PARAMS_COLOR = 'color';

    const ACTION_DROP = 'drop';
    const ACTION_ROTTEN = 'rotten';
    const ACTION_EAT = 'eat';

    const INVALID_PARAMS = 403;
    const ENTITIES_PATH = 'backend\models\\';

    const DEFAULT_ENTITY_SIZE = 100;
    const DEFAULT_ENTITY_EAT = 25;

    const STATE_ON_TREE = 'on_tree';
    const STATE_DOWN = 'down';
    const STATE_ROTTEN = 'rotten';

    const PATH_GENERATE_FILE_LOCK =  "/runtime/generate-file.lock";

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

    public static function getState($state): int
    {
        return static::find()->where(['object_name' => 'state_apple', 'code' => $state])->one()->id;
    }

    public static function getLastBatch($entity): int
    {
        return intval($entity::find()->select('batch')->max('batch'));
    }

    public static function getEntitiesInfo(): array
    {
        return [
            Apple::getEntityInfo(),
        ];
    }

    public static function getPathGenerateLockFile(): string
    {
        return Yii::getAlias('@backend') . static::PATH_GENERATE_FILE_LOCK;
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
        $randomDate->setTimestamp(time() - rand(10,5000));
        return $randomDate->getTimestamp();
    }

    public static function randColor(array $colors): int
    {
        return $colors[array_rand($colors)]['id'];
    }

    /** return range colors from transfer $color
     * @param string $color
     * @return array
     */
    public static function getGradientFromColor(string $color): array
    {
        $gradient = [];
        $c1 = intval($color[1]);
        $c2 = intval($color[2]);
        $next_color = intval($c1 . $c2);
        $gradient[] = $color;
        $format = '#%s'.substr($color,3);
        if ($next_color < 50){
            $color1 =  sprintf($format,$next_color - 5);
            $color2 = sprintf($format,$next_color - 10);
        } else {
            $color1 = sprintf($format,abs($next_color + 15));
            $color2 = sprintf($format,abs($next_color + 30));
        }
        $gradient[] =  $color1;
        $gradient[] = $color2;

        return $gradient;
    }
}
