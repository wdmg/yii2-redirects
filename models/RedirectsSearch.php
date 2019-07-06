<?php

namespace wdmg\redirects\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use wdmg\redirects\models\Redirects;

/**
 * RedirectsSearch represents the model behind the search form of `wdmg\redirects\models\Redirects`.
 */
class RedirectsSearch extends Redirects
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'code'], 'integer'],
            [['section', 'request_url', 'redirect_url', 'description', 'is_active', 'created_at', 'updated_at'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Redirects::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'section', $this->section])
            ->andFilterWhere(['like', 'request_url', $this->request_url])
            ->andFilterWhere(['like', 'redirect_url', $this->redirect_url])
            ->andFilterWhere(['like', 'description', $this->description]);

        if($this->code !== "*")
            $query->andFilterWhere(['like', 'code', $this->code]);

        if($this->is_active !== "*")
            $query->andFilterWhere(['like', 'is_active', $this->is_active]);

        return $dataProvider;
    }

}
