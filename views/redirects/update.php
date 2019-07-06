<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model wdmg\users\models\Redirects */
$this->title = Yii::t('app/modules/redirects', 'Update redirect for: {url}', [
    'url' => $model->request_url,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/redirects', 'All redirects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/redirects', 'Edit');
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="redirects-update">
    <?= $this->render('_form', [
        'model' => $model,
        'redirectsCodes' => $redirectsCodes,
        'activeStatus' => $activeStatus
    ]) ?>

</div>