<?php

namespace wdmg\redirects\components;


/**
 * Yii2 Redirects
 *
 * @category        Component
 * @version         1.0.11
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-redirects
 * @copyright       Copyright (c) 2019 - 2020 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Redirects extends Component
{

    public $request_url;
    protected $model;

    /**
     * Initialize the component
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->model = new \wdmg\redirects\models\Redirects;
    }

    /**
     * Check if URL need to redirect
     *
     * @param $url
     * @param bool $setRedirect
     * @return bool|\yii\console\Response|\yii\web\Response
     */
    public function check($url, $setRedirect = true)
    {

        if (!Url::isRelative($url))
            $this->request_url = \parse_url($url, PHP_URL_PATH);
        else
            $this->request_url = $url;

        if ($this->model && $this->request_url) {
            $redirect = $this->model::findOne(['request_url' => $this->request_url, 'is_active' => true]);
            if ($redirect !== null) {
                if ($setRedirect)
                    return Yii::$app->response->redirect(\yii\helpers\Url::to($redirect->redirect_url), $redirect->code);
                else
                    return $redirect->redirect_url;
            }
        } else {
            return false;
        }
    }

    /**
     * Check if URL need to redirect
     */
    public function set($section = null, $request_url, $redirect_url, $code, $description = null, $is_active = true)
    {
        return $this->model->setRedirect($section, $request_url, $redirect_url, $code, $description, $is_active);
    }

}

?>