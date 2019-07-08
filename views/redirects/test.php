<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model wdmg\redirects\models\Redirects */

\yii\web\YiiAsset::register($this);

?>
<div class="redirects-test">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'request_url',
                'format' => 'html',
                'label' => Yii::t('app/modules/redirects', 'Request Url'),
                'value' => function($data) use ($redirectsCodes) {
                    return $data->request_url;
                },
            ],
            [
                'attribute' => 'redirect_url',
                'format' => 'html',
                'label' => Yii::t('app/modules/redirects', 'Redirect Url'),
                'value' => function($data) use ($redirectsCodes) {
                    return $data->redirect_url;
                },
            ],
            [
                'attribute' => 'code',
                'format' => 'html',
                'label' => Yii::t('app/modules/redirects', 'Code'),
                'value' => function($data) use ($redirectsCodes) {
                    if ($redirectsCodes && $data->code !== null)
                        return $redirectsCodes[$data->code];
                    else
                        return $data->code;
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'label' => Yii::t('app/modules/redirects', 'Status'),
                'value' => function($data) {
                    if ($data->status)
                        return '<span class="label label-success">'.Yii::t('app/modules/redirects', 'Success').'</span>';
                    else
                        return '<span class="label label-danger">'.Yii::t('app/modules/redirects', 'Failure').'</span>';
                },
            ]
        ],
    ]) ?>
    <div class="modal-footer">
        <?= Html::a(Yii::t('app/modules/redirects', 'Close'), "#", [
            'class' => 'btn btn-default pull-left',
            'data-dismiss' => 'modal'
        ]); ?>
    </div>
</div>
