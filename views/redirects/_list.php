<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\redirects\models\Redirects */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="redirects-form">
    <?php $form = ActiveForm::begin([
        'id' => "addListRedirectsForm",
        'enableAjaxValidation' => false
    ]); ?>
    <?= $form->field($model, 'list')->textarea(['rows' => 12])->hint(Yii::t('app/modules/redirects', 'Please, insert list of redirection one by new line. Format: <em class="text-danger">request URL;redirect URL;Response code</em>')) ?>
    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/redirects', '&larr; Back to list'), ['redirects/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('app/modules/redirects', 'Add'), ['class' => 'btn btn-success pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>