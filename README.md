App generate any entity 
=======================

Проект написан в качестве тестового задания для компании.
Изначально было задумано делать генерацию одной сущности - яблоки,
но было оптимальнее написать архитектуру для универсальной генерациии любой сущности 
в очень больших количествах и работу с ними

Instruction settings
------

#### .htaccess files 
    main: /.htaccess
    backend: /backend/web/.htaccess
    frontend: /frontend/web/.htaccess

#### Step 1 generate local config
```bash
    php init
```
#### Step 2 db config
edit your /common/config/main_local.php
```php
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=your_db_name',
        'username' => 'your_username',
        'password' => 'your_password',
        'charset' => 'utf8',
    ],       
```
#### Step 3
```bash
    php composer install -o
```
#### Step 4 migrations
```bash
    php yii migrate
```
#### Step 5 user auth
    link auth: /backend/site/login
    username: user_test
    password: user_test
    

#### Instruction create new entity:
```php
 1. create model \backend\models\Apple.php extends \yii\db\ActiveRecord
 2. add require methods
 ```
```php
 public static function getEntityInfo(): array
 public function buildInsert(int $batch_id, array $colors, int $state): array
 
 * Add relationships
 
 public function get_color()
 {
     return $this->hasOne(SettingsType::className(), ['id' => 'color']);
 }
 public function get_state()
 {
    return $this->hasOne(SettingsType::className(), ['id' => 'state']);
 }
 ```
##### Then declare statically statically the entity key and include the created entity in the constructor of the EntityFruit class
```php
static $apple = 'Apple';

public function __construct(string $entity)
{
    $entities = [
        static::$apple => new Apple()
    ];
    parent::__construct($entities,$entity);
}
    
```
###### if the existing EntityFruit does not suit you then create a new class and inherit from Entities!


###### Use entity
```php
    $entity = 'Apple';
    $obj = new EntityFruit($entity); // if the entity is not found, an exception is thrown 
    $obj = $obj->getEntity(); also such as $obj = new Apple();
        or 
    $id = 23;
    $obj = $obj->findEntity($id) also such as $obj = Apple:findOne($id);
    $obj->getFoundObject();
    print_r( $obj->[entity_field_name] )
```

##### models SettingsType
###### If you need to add your parameter to work just create it at backend/web/settings

#### If you need to check the rotten of apples, add the yii entity/set-rottens script every 30 minutes on the cron
```bash
    php yii entity/set-rottens
```