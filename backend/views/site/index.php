<?php

use \yii\helpers\Url;
use \backend\models\Generator;
use \backend\models\SettingsType;
use \yii\widgets\LinkPager;

$this->title = 'Generate apple';

$this->registerCssFile('@web/css/page_apples.css', [
    'depends' => ['yii\web\YiiAsset']
]);

$this->registerCssFile('@web/css/apple_front.css', [
    'depends' => ['yii\web\YiiAsset']
]);

$this->registerJsFile('@web/js/generate_apple.js', [
    'depends' => ['yii\web\YiiAsset']
]);

$this->registerJsFile('@web/js/jquery.mask.min.js', [
    'depends' => ['yii\web\YiiAsset']
]);

/** @var $entity_data */
/** @var $colors */
/** @var $states */
/** @var $last_batch_id */
/** @var $entities */
/** @var $entity */
/** @var $current_entity */
/** @var $pages */
?>

<div class="site-index">
    <!-- Panel settings -->
    <?php $count_data_entity = $current_entity->find()->count(); ?>
    <div class="row">
        <div class="container">
            <ul class="nav nav-pills nav_entities">
                <?php foreach ($entities as $key_entity => $entity): ?>
                    <li class="<?=$key_entity == 0 ? 'active' : ''?>" entity="<?=$entity['entity']?>">
                        <a data-toggle="tab" href="<?= Url::to(['site/index', 'entity' => $entity['entity']])?>"><?= ucfirst($entity['entity_ru'])?></a>
                    </li>
                <?php endforeach;?>
                <li class="disabled"><a href="#" >груши</a></li>
                <li class="disabled"><a href="#" >любая сущность</a></li>
            </ul>
            <br>
        </div>
        <div class="container">
            <label>Какое количество <?=$current_entity->getEntityInfo()['plural_ru']?> сгенерировать?</label>
        </div>
        <div class="col-lg-5">
            <div class="input-group count_apple">
                <input type="text" class="form-control" placeholder="Любое количество">
                <span class="label label-danger" style="display: none"></span>
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" aria-label="Help" id="dropdownActionInfo"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="glyphicon glyphicon-info-sign"></span>
                    </button>
                    <div class="alert alert-info dropdown-menu" aria-labelledby="dropdownActionInfo">
                        <strong>Внимание!</strong>
                        <p>Введи любое целочисленное число от <b>1</b></p>
                        <p>Учтите, что если число более <b>10 000 000</b></p>
                        <p>генерация может длиться длительное время</p>
                    </div>
                    <button id="generate" type="button" class="btn btn-success" disabled>
                        Сгенерировать
                        <span class="glyphicon glyphicon-play"></span>
                        <span class="span_generate_loader" hidden>
                            <i class="fa fa-cog fa-spin fa-fw" ></i>
                            <span class="sr-only">Загрузка...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <!-- advanced action entity -->
        <div class="col-lg-7 actions_entity">
            <div class="input-group">
                <?php if($count_data_entity > 0):?>
                    <button id="delete_all_apples" type="button" class="btn btn-danger">
                        Удалить все
                        <span class="glyphicon glyphicon-trash"></span>
                        <span class="span_delete_loader" hidden>
                            <i class="fa fa-cog fa-spin fa-fw" ></i>
                            <span class="sr-only">Загрузка...</span>
                        </span>
                    </button>
                <?php endif;?>
            </div>
        </div>
    </div>
    <!-- generate_progress_bar -->
    <div class="container col-lg-9 row progress_footer">
        <div class="block_progress_bar progress" style="display: none">
            <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0"
                 aria-valuemin="0" aria-valuemax="100" style="width:0%">
                0%
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="tab-content">
        <div id="<?= $current_entity->getEntityInfo()['entity'] ?>" class="tab-pane fade in active">
            <span id="count_<?=$entity['entity']?>" count="<?=$count_data_entity?>" hidden></span>
            <?php if($count_data_entity > 0):?>
                <div class="container-fuild row">
                    <div class="col-md-12">
                        <span id="last_batch_id_<?=$entity['entity']?>" last_batch_id="<?= $entity['last_batch']?>"></span>
                        <p class="count_<?= mb_strtolower($entity['entity'])?>" count="<?=$count_data_entity?>">Всего <?=$entity['plural_ru']?> : <?= $count_data_entity ?></p>
                    </div>
                    <!-- Entity counts -->
                    <div class="col-sm-4 col-md-3 col-lg-2">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Подсчёт <?=$entity['plural_ru']?> по цвету <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <?php foreach ($colors as $key_color => $color):?>
                                    <li><a><?= $color->title ?>: <?= $current_entity->find()->where(['color'=>$color->id])->count() ?></a></li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4 col-lg-3">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Подсчёт <?=$entity['plural_ru']?> по состоянию <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <?php foreach ($states as $key_state => $state):?>
                                    <li><a><?= $state->title ?>: <?= $current_entity->find()->where(['state'=>$state->id])->count() ?></a></li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif;?>
            <!-- Entity list -->
            <div class="container row apple_list">
                <?php  if(count($entity_data) > 0): ?>
                    <?php foreach ($entity_data as $key_entity_data => $entity_val):?>
                        <!-- Entity item -->
                        <?php $entity_state = $states[$entity_val->state];
                            $entity_color = $colors[$entity_val->color]
                        ?>
                        <div class="col-xs-2 col-md-1 col-lg-1 item_apple">
                            <div class="buttons dropup">
                                <button class="btn btn-default dropdown-toggle entity_actions" type="button" id="dropdownAction"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Действие
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownAction" state="<?=$entity_state->code?>" entity_id="<?= $entity_val->id?>">
                                    <li>
                                        <button class="btn btn-primary btn-sm entity_item_action" type="button" action_state="<?= SettingsType::ACTION_DROP?>"
                                            <?= $entity_state->code == SettingsType::STATE_ON_TREE ? '' : 'disabled'?>>Сбросить на землю</button>
                                    </li>
                                    <li>
                                        <button class="btn btn-danger btn-sm entity_item_action" type="button" action_state="<?= SettingsType::ACTION_EAT?>"
                                            <?= $entity_state->code == SettingsType::STATE_DOWN ? '' : 'disabled'?>>Откусить <?=$entity['singular_ru']?></button>
                                    </li>
                                </ul>
                            </div>
                            <div class="item_info apple" style="background:<?=$entity_color->value?>;
                                    background:linear-gradient(<?=implode(',',SettingsType::getGradientFromColor($entity_color->value))?>);">
                                <b><span><?= $entity_val->size?>%</span></b>
                                <b><span><?= $entity_state->title?></span></b>
                            </div>
                        </div>
                    <?php endforeach;?>
                <?php else:?>
                    <div class="alert alert-warning" role="alert">В базе нет <?=$entity['plural_ru']?>!, попробуйте их сгенерировать</div>
                <?php endif;?>
            </div>
        </div>
        <?= LinkPager::widget([
            'pagination' => $pages,
        ]); ?>
    </div>
</div>

<script>
    var ajax_request_url = {
        "eat_entity": "<?= Url::toRoute(['/site/eat-entity']); ?>",
        "drop_entity": "<?= Url::toRoute(['/site/drop-entity']); ?>",
        "start_generate": "<?= Url::toRoute(['/site/generate']); ?>",
        "delete_entity_data": "<?= Url::toRoute(['/site/delete-entity-data']); ?>"
    },
    generate_params = {
        "cnt_in_batch": "<?= Generator::MAX_CNT_IN_BATCH ?>",
        "status_generating": "<?= Generator::STATUS_GENERATING ?>",
        "status_generated": "<?= Generator::STATUS_GENERATED ?>"
    };
    action_params = {
        "action_eat": "<?= SettingsType::ACTION_EAT ?>",
        "action_drop": "<?= SettingsType::ACTION_DROP ?>"
    };
    state_params = {
        "state_on_tree": "<?= SettingsType::STATE_ON_TREE ?>",
        "state_down": "<?= SettingsType::STATE_DOWN ?>",
        "state_rotten": "<?= SettingsType::STATE_ROTTEN ?>"
    };
</script>
