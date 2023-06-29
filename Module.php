<?php

namespace wdmg\redirects;

/**
 * Yii2 Redirects
 *
 * @category        Module
 * @version         1.1.0
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-redirects
 * @copyright       Copyright (c) 2019 - 2023 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use wdmg\helpers\ArrayHelper;
use Yii;
use wdmg\base\BaseModule;

/**
 * Redirects module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\redirects\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = "redirects/index";

    /**
     * @var string, the name of module
     */
    public $name = "Redirects";

    /**
     * @var string, the description of module
     */
    public $description = "Manage of redirects for application";

    /**
     * @var boolean, the flag for automatic check
     * requested URL for redirection
     */
    public $autocheck = true;

    /**
     * @var boolean, the flag for allow external URL for add
     * requested URL for redirection
     */
    public $allowExternal = false;

    /**
     * @var string the module version
     */
    private $version = "1.1.0";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 1;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);

    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($options = null)
    {
        $items = [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/'. $this->id],
            'icon' => 'fa fa-fw fa-exchange-alt',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id])
        ];

	    if (!is_null($options)) {

		    if (isset($options['count'])) {
			    $items['label'] .= '<span class="badge badge-default float-right">' . $options['count'] . '</span>';
			    unset($options['count']);
		    }

		    if (is_array($options))
			    $items = ArrayHelper::merge($items, $options);

	    }

	    return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        // Configure activity component
        $app->setComponents([
            'redirects' => [
                'class' => 'wdmg\redirects\components\Redirects'
            ]
        ]);

        // Check for redirection (frontend)
        if (!$this->isBackend() && !$this->isConsole()) {
            if ($this->autocheck && $this->module && ($app->redirects instanceof \yii\base\Component)) {
                \yii\base\Event::on(\yii\base\Controller::class, \yii\base\Controller::EVENT_BEFORE_ACTION, function ($event) {
                    $url = Yii::$app->request->getUrl();
                    Yii::$app->redirects->check($url);
                });
            }
        }
    }
}