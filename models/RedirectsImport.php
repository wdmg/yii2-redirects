<?php

namespace wdmg\redirects\models;

use yii\base\Model;
use wdmg\redirects\models\Redirects;

/**
 * RedirectsImport represents the model behind the search form of `wdmg\redirects\models\Redirects`.
 */
class RedirectsImport extends Redirects
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['import'], 'file', 'skipOnEmpty' => true, 'minSize' => 1, 'maxSize' => 512000, 'extensions' => 'json'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Import or update redirects
     *
     * @param array $redirects
     *
     * @return boolean
     */
    public function import($redirects)
    {
        $count_success = 0;
        $count_fails = 0;
        foreach ($redirects as $redirect) {
            $section = '';
            if(!is_null($redirect['section']))
                $section = $redirect['section'];

            $description = '';
            if(!is_null($redirect['description']))
                $description = $redirect['description'];

            $is_active = 0;
            if(!is_null($redirect['is_active']))
                $is_active = $redirect['is_active'];

            if (Redirects::setRedirect($section, $redirect['request_url'], $redirect['redirect_url'], $redirect['code'], $description, $is_active))
                $count_success++;
            else
                $count_fails++;
        }

        if($count_success > 0 && $count_fails == 0)
            return true;
        else
            return false;
    }

}
