<?php

namespace wdmg\redirects\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\httpclient\Client;

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
    public $list;
    public static $codeRange = [300, 301, 302, 303, 307, 308];

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
            ['request_url', 'checkRequestUrl', 'on' => 'create'],
            ['redirect_url', 'checkRedirectUrl'],
            [['request_url', 'redirect_url'], 'match', 'pattern' => '/^\/admin.*/', 'not' => true, 'message' => Yii::t('app/modules/redirects','URL starting from «/admin» not allowed.')],
            [['request_url', 'redirect_url'], 'match', 'pattern' => '/^(?!www\.|(?:http|ftp)s?:\/\/|[A-Za-z]:\\\\|\/\/).*/', 'message' => Yii::t('app/modules/redirects','URL must be a relative.')],
            [['description'], 'string'],
            [['code'], 'integer'],
            [['code'], 'in', 'range' => self::$codeRange],
            [['section'], 'string', 'min' => 3, 'max' => 128],
            [['description'], 'string', 'max' => 255],
            [['is_active'], 'boolean'],
            [['created_at', 'updated_at', 'list'], 'safe'],
        ];
    }

    public function checkRequestUrl($attribute, $params, $validator) {
        if ($this->request_url) {
            if ($this->request_url === $this->redirect_url)
                $this->addError('request_url', Yii::t('app/modules/redirects', 'The request URL must not coincide with redirect URL.'));

            if (self::findOne(['request_url' => $this->request_url]))
                $this->addError('request_url', Yii::t('app/modules/redirects', 'The request URL already exist.'));

        }
    }

    public function checkRedirectUrl($attribute, $params, $validator) {
        if ($this->redirect_url) {

            if ($this->redirect_url === $this->request_url)
                $this->addError('redirect_url', Yii::t('app/modules/redirects', 'The redirect URL must not coincide with request URL.'));

            $client = new Client(['baseUrl' => \yii\helpers\Url::base(true)]);
            $response = $client->get($this->redirect_url)->send();
            if (!$response->isOk || !intval($response->headers["http-code"]) == 200) {
                $this->addError('redirect_url', Yii::t('app/modules/redirects', 'The redirect URL must be exist and returning 200 HTTP code.'));
            }
        }
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
            'list' => Yii::t('app/modules/redirects', 'Redirects list'),
        ];
    }

    /**
     * Set (save) or update redirect
     *
     * @param string $section
     * @param string $request_url
     * @param string $redirect_url
     * @param integer $code
     * @param string $description
     * @param boolean $is_active
     *
     * @return boolean
     */
    public static function setRedirect($section = null, $request_url, $redirect_url, $code, $description = null, $is_active = false)
    {

        if (!empty($section) && !empty($request_url))
            $model = static::findOne(['section' => $section, 'request_url' => $request_url]);
        elseif (!is_null($request_url))
            $model = static::findOne(['request_url' => $request_url]);

        if ($model === null)
            $model = new static();

        if ($section)
            $model->section = strval($section);

        $model->request_url = trim($request_url);
        $model->redirect_url = trim($redirect_url);
        $model->code = intval($code);

        if ($description)
            $model->description = strval($description);

        $model->is_active = boolval($is_active);
        return $model->save();
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
            $items = ['*' => Yii::t('app/modules/redirects', 'All status')];

        return ArrayHelper::merge($items, [
            '1' => Yii::t('app/modules/redirects', 'Active'),
            '0' => Yii::t('app/modules/redirects', 'Not active'),
        ]);
    }

}
