<?php

use \yii\helpers\Url;
use \backend\models\Apple;

$this->title = '';

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

/** @var $lock_delete */
/** @var $apples */
/** @var $colors_apple */
/** @var $count_apples */
/** @var $states_apple */
?>

<div class="site-index">
    <!-- Panel settings -->
    <div class="row">
        <div class="container">
            <label>Какое количество яблок сгенерировать?</label>
        </div>
        <div class="col-lg-7">
            <div class="input-group count_apple">
                <input type="text" class="form-control" placeholder="от 0 до 10.000">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default" aria-label="Help">
                        <span class="glyphicon glyphicon-question-sign"></span>
                    </button>
                    <button id="generate" type="button" class="btn btn-success">
                        Сгенерировать
                        <span class="glyphicon glyphicon-play"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="input-group">
                <button id="delete_all_apples" type="button" class="btn btn-danger" <?= $lock_delete == 0 ?: 'disabled'?>>
                    Удалить все
                    <span class="glyphicon glyphicon-trash"></span>
                </button>
            </div>
        </div>
    </div>
    <!-- generate_progress_bar -->
    <div class="container col-lg-9 row progress_footer">
        <div class="block_progress_bar progress" style="display: none">
            <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0"
                 aria-valuemin="0" aria-valuemax="100" style="width:00%">
                0%
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php if($count_apples > -1):?>
        <div class="container-fuild row">
            <div class="col-md-12">
                <p>Всего яблок : <?= $count_apples ?></p>
            </div>
            <!-- Apple counts -->
            <div class="col-sm-4 col-md-3 col-lg-2">
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Подсчёт яблок по цвету <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($colors_apple as $key_color => $a_color):?>
                            <li><a><?= $a_color->title ?>: <?= Apple::find()->where(['color'=>$a_color->id])->count() ?></a></li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-3">
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Подсчёт яблок по состоянию <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($states_apple as $key_state => $a_state):?>
                            <li><a><?= $a_state->title ?>: <?= Apple::find()->where(['state'=>$a_state->id])->count() ?></a></li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif;?>
    <!-- Apple list -->
    <div class="container row apple_list">
        <?php if(count($apples) > 0): ?>
            <?php foreach ($apples as $key_apple => $apple):?>
                <!-- Apple item -->
                <div class="col-xs-2 col-md-1 col-lg-1 item_apple">
                    <div class="buttons dropup">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownAction" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Действие
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownAction">
                            <li state="<?= $apple?>">
                                <a href="#">
                                    <button class="btn btn-primary btn-sm" type="button" <?= $apple != 'on_tree' ?: 'disabled'?>>Сбросить на землю</button>
                                </a>
                            </li>
                            <li state="<?= $apple?>">
                                <a href="#">
                                    <button class="btn btn-danger btn-sm" type="button" <?= $apple != 'on_tree' ?: 'disabled'?>>Откусить яблоко</button>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="apple">
                        <b><span><?= $apple?>%</span></b>
                        <b><span><?= $apple->state?></span></b>
                    </div>
                </div>
            <?php endforeach;?>
        <?php else:?>
            <div class="alert alert-warning" role="alert">В базе нет яблок!, попробуйте их сгенерировать</div>
        <?php endif;?>
    </div>
</div>

<script>
    var eat_apple_url = {
        "eat_apple": "<?= Url::toRoute(['/eat-apple-json']); ?>"
    };
</script>
