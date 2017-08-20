<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;

/**
* ContactForm is the model for feedback table.
*
 * @property string $before
 * @property string $after
 * @property integer $status
*
*/
class OrderFilterForm extends Model
{
    public $before, $after, $status;

    public function rules()
    {
        return [
            [['before', 'after'], 'string'],
            ['status', 'in', 'range' => array_keys(Order::$STATUS_TO_STRING)],
            // all status
            ['status', 'default', 'value' => -1],
            ['before', 'default', 'value' => date('Y-m-d')],
            ['after', 'default', 'value' => date('Y-m-d', time() - 7 * 60 * 60 * 24)],
            [['before', 'after', 'status'], 'safe'],
        ];
    }


    public function getOrders() {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()
                ->select('order.id, order.status, order.created_at, order.public_id, order.user_id, user.email,
                    sum(order_product.products_count * old_price) as total_price,
                    sum(order_product.confirmed_count * old_price) as confirmed_price'
                )->groupBy('order.id')
                ->join('RIGHT JOIN', 'order_product', 'order.id=order_product.order_id')
                ->joinWith('user')
            ,
            'sort' => [
                'attributes' => [
                    'created_at',
                    'total_price',
                    'confirmed_price',
                    'user.email',
                    'status_str' => [
                        'asc' => ['status' => SORT_ASC],
                        'desc' => ['status' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'ref' => [
                        'asc' => ['public_id' => SORT_ASC],
                        'desc' => ['public_id' => SORT_DESC],
                        'default' => SORT_DESC,
                    ]
                ]
            ]
        ]);
        $dataProvider->query->andFilterWhere(['>=', 'order.created_at', strtotime($this->after)]);
        $dataProvider->query->andFilterWhere(['<=', 'order.created_at', strtotime($this->before)]);
        if ($this->status != -1) {
            $dataProvider->query->andFilterWhere(['=', 'order.status', $this->status]);
        }

        Yii::$app->db->cache(function ($db) use ($dataProvider) {
            $dataProvider->prepare();
        }, null, new TagDependency(['tags' => [
            'cache_table_' . Order::tableName(),
            'cache_table_' . OrderProduct::tableName(),
        ]]));

        return $dataProvider;
    }
}
