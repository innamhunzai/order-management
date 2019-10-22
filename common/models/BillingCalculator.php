<?php


namespace common\models;


use yii\base\Model;

class BillingCalculator extends Model
{
    /**
     * @var DiscountRule
     */
    private $discountRule;

    /**
     * BillingCalculator constructor.
     * @param DiscountRule $discountRule
     * @param array $config
     */
    function __construct($discountRule, $config = [])
    {
        $this->discountRule = $discountRule;
        parent::__construct($config);
    }

    /**
     * @param Order
     * @return  float Total Bill Amount
     */
    public function calculateBill($order)
    {
        $totalBill = $order->product->price * $order->quantity;
        if ($this->discountRule != null) {
            //Apply Discount Formula
            $totalBill = $totalBill - ($totalBill * ($this->discountRule->percentage / 100));
        }
        return $totalBill;
    }
}