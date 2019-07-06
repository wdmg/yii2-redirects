<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model wdmg\redirects\models\Redirects */

\yii\web\YiiAsset::register($this);

?>
<div class="redirects-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'request_url:ntext',
            'redirect_url:ntext',
            [
                'attribute' => 'code',
                'format' => 'html',
                'value' => function($data) use ($redirectsCodes) {
                    if ($redirectsCodes && $data->code !== null)
                        return $redirectsCodes[$data->code];
                    else
                        return $data->code;
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => 'html',
                'value' => function($data) use ($activeStatus) {
                    if ($activeStatus) {
                        if ($data->is_active)
                            return '<span class="label label-success">' . $activeStatus[$data->is_active] . '</span>';
                        else
                            return '<span class="label label-danger">' . $activeStatus[$data->is_active] . '</span>';
                    } else {
                        return $data->is_active;
                    }
                }
            ],
            'section:ntext',
            'description:ntext',
            'created_at:datetime',
            'updated_at:datetime'
        ],
    ]) ?>
    <div class="modal-footer">
        <?= Html::a(Yii::t('app/modules/redirects', 'Close'), "#", [
                'class' => 'btn btn-default pull-left',
                'data-dismiss' => 'modal'
        ]); ?>
        <?= Html::a(Yii::t('app/modules/redirects', 'Edit'), ['update', 'id' => $model->id], [
            'class' => 'btn btn-primary pull-right'
        ]); ?>
        <?= Html::a(Yii::t('app/modules/redirects', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('app/modules/redirects', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]); ?>
    </div>
</div>
