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
    function __construct($discountRule = null, $config = [])
    {
        $this->discountRule = $discountRule;
        parent::__construct($config);
    }

    /**
     * @param float
     * @param integer
     * @return  float Total Bill Amount
     */
    public function calculateBill($price, $quantity)
    {
        $totalBill = $price * $quantity;
        if ($this->discountRule != null && $this->discountRule->min_quantity <= $quantity) {
            //Apply Discount Formula
            $totalBill = $totalBill - ($totalBill * ($this->discountRule->percentage / 100));
        }
        return $totalBill;
    }
}