<?php

use yii\db\Migration;
use backend\models\SettingsType;

class m200405_022801_create_apple_table extends Migration
{
    public $table_name;

    public function __construct(array $config = [])
    {
        parent::__construct();
        $this->table_name = 'apple';
    }

    public function safeUp()
    {
        $this->createTable($this->table_name, [
            'id' => $this->primaryKey(),
            'color' => $this->integer()->notNull(),
            'state' => $this->integer(),
            'date_show' => $this->integer()->notNull(),
            'date_down' => $this->integer(),
            'size' => $this->integer()->notNull()->defaultValue(100),
            'create_at' => $this->dateTime()->notNull()->defaultValue(new \yii\db\Expression('CURRENT_TIMESTAMP')),
            'update_at' => $this->dateTime()->notNull()->defaultValue(new \yii\db\Expression('CURRENT_TIMESTAMP'))." ON UPDATE CURRENT_TIMESTAMP",
        ]);

        $this->createIndex(
            'idx-'.$this->table_name.'-state-'.SettingsType::tableName().'-id',
            $this->table_name,
            'state'
        );

        $this->addForeignKey(
            'fk-'.$this->table_name.'-state',
            $this->table_name,
            'state',
            SettingsType::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->createIndex(
            'idx-'.$this->table_name.'-color-'.SettingsType::tableName().'-id',
            $this->table_name,
            'color'
        );

        $this->addForeignKey(
            'fk-'.$this->table_name.'-color',
            $this->table_name,
            'color',
            SettingsType::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-'.$this->table_name.'-state',
            $this->table_name
        );

        $this->dropIndex(
            'idx-'.$this->table_name.'-state-'.SettingsType::tableName().'-id',
            $this->table_name
        );

        $this->dropForeignKey(
            'fk-'.$this->table_name.'-color',
            $this->table_name
        );

        $this->dropIndex(
            'idx-'.$this->table_name.'-color-'.SettingsType::tableName().'-id',
            $this->table_name
        );

        $this->dropTable($this->table_name);
    }
}
