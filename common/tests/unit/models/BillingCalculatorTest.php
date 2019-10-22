<?php

namespace common\tests\unit\models;

use common\models\BillingCalculator;
use common\models\DiscountRule;
use Webmozart\Assert\Assert;

class BillingCalculatorTest extends \Codeception\Test\Unit
{

    public function testCalculateBillWithDiscount()
    {
        $discountRule = $this->getMockBuilder(DiscountRule::class)->disableOriginalConstructor();
        $discountRule->percentage = 20.0;
        $discountRule->min_quantity = 3;
        $billingCalculator = new BillingCalculator(
            $discountRule
        );
        Assert::same($billingCalculator->calculateBill(1.5, 3), 3.6);
    }

    public function testCalculateBillWithOutDiscount()
    {
        $discountRule = $this->getMockBuilder(DiscountRule::class)->disableOriginalConstructor();
        $discountRule->percentage = 20.0;
        $discountRule->min_quantity = 3;
        $billingCalculator = new BillingCalculator(
            $discountRule
        );
        Assert::same($billingCalculator->calculateBill(1.5, 2), 3.0);
    }
}
