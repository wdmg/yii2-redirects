<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model wdmg\users\models\Redirects */

$this->title = Yii::t('app/modules/redirects', 'Create redirect');
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['redirects/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="redirects-create">
    <?= Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app/modules/redirects', 'One redirect'),
                'content' => $this->render('_form', [
                    'model' => $model,
                    'redirectsCodes' => $redirectsCodes,
                    'activeStatus' => $activeStatus
                ]),
                'active' => true
            ], [
                'label' => Yii::t('app/modules/redirects', 'List of redirects'),
                'content' => $this->render('_list', [
                    'model' => $model,
                    'redirectsCodes' => $redirectsCodes,
                    'activeStatus' => $activeStatus
                ]),
            ]
        ]
    ]);
/*
 *     'items' => [
 *         [
 *             'label' => 'One',
 *             'content' => 'Anim pariatur cliche...',
 *             'active' => true
 *         ],
 *         [
 *             'label' => 'Two',
 *             'content' => 'Anim pariatur cliche...',
 *             'headerOptions' => [...],
 *             'options' => ['id' => 'myveryownID'],
 *         ],
 *         [
 *             'label' => 'Example',
 *             'url' => 'http://www.example.com',
 *         ],
 *         [
 *             'label' => 'Dropdown',
 *             'items' => [
 *                  [
 *                      'label' => 'DropdownA',
 *                      'content' => 'DropdownA, Anim pariatur cliche...',
 *                  ],
 *                  [
 *                      'label' => 'DropdownB',
 *                      'content' => 'DropdownB, Anim pariatur cliche...',
 *                  ],
 *                  [
 *                      'label' => 'External Link',
 *                      'url' => 'http://www.example.com',
 *                  ],
 *             ],
 *         ],
 *     ],
 * ]);*/ ?>


</div>