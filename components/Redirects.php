<?php

namespace wdmg\redirects\components;


/**
 * Yii2 Redirects
 *
 * @category        Component
 * @version         1.0.1
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-redirects
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;

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
     * @param $url string
     * @return boolean (false) or the response object of redirection
     */
    public function check($url)
    {
        $this->request_url = $url;
        if ($this->model && $this->request_url) {
            $redirect = $this->model::findOne(['request_url' => $this->request_url]);
            if ($redirect !== null) {
                Yii::$app->response->redirect($redirect->redirect_url, $redirect->code);
            }
        } else {
            return false;
        }
    }

}

?>