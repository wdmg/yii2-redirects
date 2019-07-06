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
        'id' => "addRedirectForm",
        'enableAjaxValidation' => true
    ]); ?>
    <?= $form->field($model, 'request_url')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'redirect_url')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'code')->widget(SelectInput::className(), [
        'items' => $redirectsCodes,
        'options' => [
            'class' => 'form-control'
        ]
    ]) ?>
    <?= $form->field($model, 'section')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    <?= $form->field($model, 'is_active')->widget(SelectInput::className(), [
        'items' => $activeStatus,
        'options' => [
            'class' => 'form-control'
        ]
    ]) ?>
    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/redirects', '&larr; Back to list'), ['redirects/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('app/modules/redirects', 'Save'), ['class' => 'btn btn-success pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php $this->registerJs(<<< JS
$(document).ready(function() {
    function afterValidateAttribute(event, attribute, messages)
    {
        if (attribute.name == "value" && attribute.status == 1 && messages.length == 0) {
            var form = $(event.target);
            $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serializeArray(),
                }
            ).done(function(data) {
                if(data.success) {
                    if(data.type) {
                        form.find('#redirects-type').val(data.type);
                        form.find('#redirects-type').trigger('change');
                    }
                } else {
                    form.find('#redirects-type').val("string");
                    form.find('#redirects-type').trigger('change');
                }
                form.yiiActiveForm('validateAttribute', 'redirects-type');
            }).fail(function () {
                form.find('#redirects-type').val("");
                form.find('#redirects-type').trigger('change');
            });
            return false; // prevent default form submission
        }
    }
    $("#addOptionForm").on("afterValidateAttribute", afterValidateAttribute);
});
JS
); ?>