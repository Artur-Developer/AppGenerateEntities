<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SettingsType */

$this->title = 'Create Settings Type';
$this->params['breadcrumbs'][] = ['label' => 'Settings Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
