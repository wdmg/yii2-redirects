<?php

namespace wdmg\redirects\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%redirects}}".
 *
 * @property int $id
 * @property string $section
 * @property string $request_url
 * @property string $redirect_url
 * @property integer $code
 * @property string $description
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 */

class Redirects extends ActiveRecord
{

    public $import;
    public $codeRange = [300, 301, 302, 303, 307, 308];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%redirects}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_url', 'redirect_url', 'code'], 'required'],
            [['description'], 'string'],
            [['code'], 'integer', 'length' => 3],
            [['code'], 'in', 'range' => $this->codeRange],
            [['section'], 'string', 'min' => 3, 'max' => 128],
            [['description'], 'string', 'max' => 255],
            [['is_active'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/redirects', 'ID'),
            'section' => Yii::t('app/modules/redirects', 'Section'),
            'request_url' => Yii::t('app/modules/redirects', 'Request URL'),
            'redirect_url' => Yii::t('app/modules/redirects', 'Redirect URL'),
            'code' => Yii::t('app/modules/redirects', 'Code'),
            'description' => Yii::t('app/modules/redirects', 'Description'),
            'is_active' => Yii::t('app/modules/redirects', 'Is active'),
            'created_at' => Yii::t('app/modules/redirects', 'Created at'),
            'updated_at' => Yii::t('app/modules/redirects', 'Updated at'),
            'import' => Yii::t('app/modules/redirects', 'Import file'),
        ];
    }

    public function getRedirectsCodesList($addAllLabel = true) {

        $items = [];
        if ($addAllLabel)
            $items = ['*' => Yii::t('app/modules/redirects', 'All codes')];
        else
            $items = ['null' => Yii::t('app/modules/redirects', 'Not selected')];

        return ArrayHelper::merge($items, [
            '300' => Yii::t('app/modules/redirects', '300 Multiple Choices'),
            '301' => Yii::t('app/modules/redirects', '301 Moved Permanently'),
            '302' => Yii::t('app/modules/redirects', '302 Found'),
            '303' => Yii::t('app/modules/redirects', '303 See Other'),
            '307' => Yii::t('app/modules/redirects', '307 Temporary Redirect'),
            '308' => Yii::t('app/modules/redirects', '308 Permanent Redirect'),
        ]);
    }

    public function getActiveStatusList($addAllLabel = true) {
        $items = [];
        if ($addAllLabel)
            $items = ['*' => Yii::t('app/modules/redirects', 'All redirects')];

        return ArrayHelper::merge($items, [
            '1' => Yii::t('app/modules/redirects', 'Active'),
            '0' => Yii::t('app/modules/redirects', 'Not active'),
        ]);
    }

}
