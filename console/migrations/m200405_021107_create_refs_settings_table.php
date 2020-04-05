<?php

use yii\db\Migration;

class m200405_021107_create_refs_settings_table extends Migration
{
    public $table_name;

    public function __construct(array $config = [])
    {
        parent::__construct();
        $this->table_name = 'refs_settings_type';
    }

    public function safeUp()
    {
        $this->createTable($this->table_name, [
            'id' => $this->primaryKey(),
            'object_name' => $this->string(255)->notNull(),
            'title' => $this->string(255)->notNull(),
            'code' => $this->string(255)->notNull(),
            'value' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultValue(new \yii\db\Expression('CURRENT_TIMESTAMP')),
            'updated_at' => $this->dateTime()->notNull()->defaultValue(new \yii\db\Expression('CURRENT_TIMESTAMP'))." ON UPDATE CURRENT_TIMESTAMP",
        ]);

        $data = [
            // states
            [
                'object_name' => 'state_apple',
                'title' => 'яблоко на дереве',
                'code' => 'on_tree',
                'value' => 'on_tree',
            ],
            [
                'object_name' => 'state_apple',
                'title' => 'яблоко упало с дерева',
                'code' => 'down',
                'value' => 'down',
            ],
            [
                'object_name' => 'state_apple',
                'title' => 'яблоко испорчено',
                'code' => 'rotten',
                'value' => 'rotten',
            ],
            // colors
            [
                'object_name' => 'color_apple',
                'title' => 'синие',
                'code' => 'blua',
                'value' => '#1E90FF',
            ],
            [
                'object_name' => 'color_apple',
                'title' => 'зелёное',
                'code' => 'green',
                'value' => '#008000',
            ],
            [
                'object_name' => 'color_apple',
                'title' => 'красное',
                'code' => 'red',
                'value' => '#A52A2A',
            ],
            [
                'object_name' => 'color_apple',
                'title' => 'розовое',
                'code' => 'pink',
                'value' => '#EE82EE',
            ],
            [
                'object_name' => 'color_apple',
                'title' => 'бежевое',
                'code' => 'lavender',
                'value' => '#E6E6FA',
            ],
            [
                'object_name' => 'color_apple',
                'title' => 'жёлтое',
                'code' => 'yellow',
                'value' => '#FFFF00',
            ],
            [
                'object_name' => 'color_apple',
                'title' => 'фиолетовое',
                'code' => 'purple',
                'value' => '#FFFF00',
            ],
            [
                'object_name' => 'color_apple',
                'title' => 'чёрное',
                'code' => 'black',
                'value' => '#000000',
            ],
            [
                'object_name' => 'color_apple',
                'title' => 'коричневое',
                'code' => 'brown',
                'value' => '#8B4513',
            ]
        ];

        foreach($data as $item) {
            $st = new \backend\models\SettingsType();
            $st->setAttributes($item);
            $st->save();
        }
    }

    public function safeDown()
    {
        $objects = ['state_apple'];

        foreach ($objects as $object){
            \backend\models\SettingsType::deleteAll('object_name = :object_name', [':object_name' => $object]);
        }

        $this->dropTable($this->table_name);
    }
}
