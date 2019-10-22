<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;
use yii\db\ActiveQuery;

/**
 * OrderSearch represents the model behind the search form of `common\models\Order`.
 */
class OrderSearch extends Order
{
    public $timeSpan;
    public $searchTerm;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['searchTerm', 'string', 'max' => 255],
            [['timeSpan'], 'in', 'range' => ['alltime', 'week', 'today']],
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
        //Create active query for search and listing orders with lazy loading of related product and user
        $query = Order::find()
            ->innerJoinWith('user', false)
            ->innerJoinWith('product', false);

        //Create Data provider with pagination
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10 ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // return query result empty if any validation rules fail.
            $query->where('0=1');
            return $dataProvider;
        }



        // add username search condition
        $query->andFilterWhere([
            'like', 'username', $this->searchTerm
        ]);

        //add product name filter contition
        $query->orFilterWhere([
            'like', 'name', $this->searchTerm
        ]);

        // add time filter conditions
        $query = $this->timeSpanMapper($query);

        //order by most recently modified order
        $query->orderBy('order.updated_at DESC');

        return $dataProvider;
    }

    /**
     * @param ActiveQuery $query
     * @return ActiveQuery
     */
    private function timeSpanMapper($query)
    {
        switch($this->timeSpan) {
            case "alltime":
                break;
            case "today":
                $query->andFilterCompare(
                    'order.created_at',
                    strtotime('today midnight'),
                    '>='
                );
                break;
            case "week":
                $query->andFilterCompare(
                    'order.created_at',
                    strtotime('-1 week midnight'),
                    '>='
                );
                break;
        }
        return $query;
    }
}
