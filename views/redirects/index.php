<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use wdmg\widgets\SelectInput;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\redirects\models\RedirectsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $this->context->module->name;
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs(<<< JS

    /* To initialize BS3 tooltips set this below */
    $(function () {
        $("[data-toggle='tooltip']").tooltip(); 
    });
    
    /* To initialize BS3 popovers set this below */
    $(function () {
        $("[data-toggle='popover']").popover(); 
    });

JS
);

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="redirects-index">
    <?php Pjax::begin([
        'id' => "redirectsAjax",
        'timeout' => 5000
    ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'request_url',
            'redirect_url',
            [
                'attribute' => 'code',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'code',
                    'items' => $redirectsCodes,
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) use ($redirectsCodes) {
                    if ($redirectsCodes && $data->code !== null)
                        return $redirectsCodes[$data->code];
                    else
                        return $data->code;
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'is_active',
                    'items' => $activeStatus,
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    if ($data->is_active == true) {
                        return '<div id="switcher-' . $data->id . '" data-value-current="' . $data->is_active . '" data-id="' . $data->id . '" data-toggle="button-switcher" class="btn-group btn-toggle"><button data-value="0" class="btn btn-xs btn-default">OFF</button><button data-value="1" class="btn btn-xs btn-primary">ON</button></div>';
                    } else {
                        return '<div id="switcher-' . $data->id . '" data-value-current="' . $data->is_active . '" data-id="' . $data->id . '" data-toggle="button-switcher" class="btn-group btn-toggle"><button data-value="0" class="btn btn-xs btn-danger">OFF</button><button data-value="1" class="btn btn-xs btn-default">ON</button></div>';
                    }
                }
            ],
            'section',
            'description',
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons'=> [
                    'view' => function($url, $data, $key) use ($module) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['redirects/view', 'id' => $data['id']]), [
                            'class' => 'redirect-details-link',
                            'title' => Yii::t('yii', 'View'),
                            'data-toggle' => 'modal',
                            'data-target' => '#redirectDetails',
                            'data-id' => $key,
                            'data-pjax' => '1'
                        ]);
                    }
                ],
            ]
        ],
        'rowOptions' => function ($model, $index, $widget, $grid) {
            if ($model->is_active) {
                return ['class' => 'changed success'];
            }
        },
    ]); ?>
    <hr/>
    <div>
        <div class="btn-group">
            <?= Html::a(Yii::t('app/modules/redirects', 'Import redirects'), ['import'], [
                'class' => 'btn btn-warning',
                'data-toggle' => 'modal',
                'data-target' => '#redirectsImport',
                'data-pjax' => '1'
            ]) ?>
            <?= Html::a(Yii::t('app/modules/redirects', 'Export redirects'), ['export'], [
                'class' => 'btn btn-info',
                'data-pjax' => '0'
            ]) ?>
        </div>
        <?= Html::a(Yii::t('app/modules/redirects', 'Add new redirect'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php $this->registerJs(
    'var $container = $("#redirectsAjax");
    var requestURL = window.location.href;
    if ($container.length > 0) {
        $container.delegate(\'[data-toggle="button-switcher"] button\', \'click\', function() {
            var id = $(this).parent(\'.btn-group\').data(\'id\');
            var value = $(this).data(\'value\');
            $.ajax({
                type: "POST",
                url: requestURL + \'?change=status\',
                dataType: \'json\',
                data: {\'id\': id, \'value\': value},
                complete: function(data) {
                    $.pjax.reload({type:\'POST\', container:\'#redirectsAjax\'});
                }
            });
        });
    }', \yii\web\View::POS_READY
); ?>

<?php $this->registerJs(<<< JS
$('body').delegate('.redirect-details-link', 'click', function(event) {
    event.preventDefault();
    $.get(
        $(this).attr('href'),
        function (data) {
            var body = $(data).remove('.modal-footer').html();
            var footer = $(data).find('.modal-footer').html();
            $('#redirectDetails .modal-body').html(body);
            $('#redirectDetails .modal-body').find('.modal-footer').remove();
            $('#redirectDetails .modal-footer').html(footer);
            $('#redirectDetails').modal();
        }  
    );
});
JS
); ?>

<?php Modal::begin([
    'id' => 'redirectsImport',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/redirects', 'Redirects import').'</h4>',
]); ?>
<?php echo $this->render('_import', ['model' => $importModel]); ?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'redirectDetails',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/redirects', 'Redirect details').'</h4>',
    'footer' => '<a href="#" class="btn btn-default pull-left" data-dismiss="modal">'.Yii::t('app/modules/redirects', 'Close').'</a>',
    'clientOptions' => [
        'show' => false
    ]
]); ?>
<?php Modal::end(); ?>

<?php echo $this->render('../_debug'); ?>
