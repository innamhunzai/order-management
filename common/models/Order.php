<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $quantity
 * @property string $amount
 * @property int $created_at
 * @property int $updated_at
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @var BillingCalculator
     */
    private $billingCalculator;

    /**
     * initialize Order
     * @param BillingCalculator $billingCalculator
     * @return Order
     */
    public function initialize($billingCalculator)
    {
        $this->billingCalculator = $billingCalculator;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'product_id', 'quantity'], 'required'],
            [['user_id', 'product_id', 'quantity', 'created_at', 'updated_at'], 'integer'],
            ['quantity', function ($attribute, $params) {
                $min = 1;
                if ($this->$attribute < $min) {
                    $this->addError($attribute, "Quantity must be at least {$min}.");
                }
            }],
            [['amount'], 'number'],
            ['product_id', 'exist', 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            ['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            TimestampBehavior::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'product_id' => 'Product',
            'quantity' => 'Quantity',
            'amount' => 'Amount',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Find user detail of the order
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getUserName()
    {
        return $this->user->username;
    }

    /**
     * Find product detail of the order
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @return String of the associated product
     */
    public function getProductName()
    {
        return $this->product->name;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->amount = $this->billingCalculator->calculateBill($this->product->price, $this->quantity);
        return parent::beforeSave($insert);
    }
}
