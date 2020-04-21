<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public $table_name;

    public function __construct(array $config = [])
    {
        parent::__construct();
        $this->table_name = 'user';
    }

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->table_name, [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'verification_token' => $this->string()->defaultValue(null),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $new_user = new \common\models\User();
        $new_user->setPassword('user_test');
        $new_user->generateAuthKey();
        $new_user->username = 'user_test';
        $new_user->email = 'user_test@mail.local';
        $new_user->status = 10;
        $new_user->save();
    }

    public function down()
    {
        \common\models\User::deleteAll();
        $this->dropTable($this->table_name);
    }
}
