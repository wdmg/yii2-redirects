<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model wdmg\users\models\Redirects */

$this->title = Yii::t('app/modules/redirects', 'Create option');
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['redirects/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="redirects-create">
    <?= $this->render('_form', [
        'model' => $model,
        'redirectsCodes' => $redirectsCodes,
        'activeStatus' => $activeStatus
    ]) ?>
</div>