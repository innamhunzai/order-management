<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "discount_rule".
 *
 * @property int $id
 * @property int $product_id
 * @property string $min_quantity
 * @property string $percentage
 * @property int $created_at
 * @property int $updated_at
 */
class DiscountRule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discount_rule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'min_quantity', 'percentage', 'created_at', 'updated_at'], 'required'],
            [['product_id', 'created_at', 'updated_at'], 'integer'],
            [['min_quantity', 'percentage'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'min_quantity' => 'Min Quantity',
            'percentage' => 'Percentage',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param $productId
     * @param $quantity
     * @return DiscountRule
     */
    public static function findDiscountRule($productId, $quantity)
    {
        return self::find()
            ->andFilterCompare('product_id', $productId)
            ->andFilterCompare('min_quantity', $quantity, '<=')
            ->orderBy('min_quantity DESC')
            ->one();
    }
}
