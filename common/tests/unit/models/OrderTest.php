<?php

namespace common\tests\unit\models;

use common\fixtures\DiscountRuleFixture;
use common\fixtures\ProductFixture;
use common\fixtures\UserFixture;
use common\models\BillingCalculator;
use common\models\Order;

class OrderTest extends \Codeception\Test\Unit
{

    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    private static $VALID_USER_ID = 1;
    private static $PEPSI_COLA_ID = 2;
    private static $OTHER_PRODUCT_ID = 1;

    public function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'discount_rule' => [
                'class' => DiscountRuleFixture::class,
                'dataFile' => codecept_data_dir() . 'discount_rule.php'
            ],
            'product' => [
                'class' => ProductFixture::class,
                'dataFile' => codecept_data_dir() . 'product.php'
            ]
        ]);
    }

    public function testCreateOrderWithInvalidProduct()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => 9999,
                'user_id' => self::$VALID_USER_ID,
                'quantity' => 1
            ]
        ]);

        expect_not($order->validate());
        expect_that($order->getErrors('product_id'));
        expect($order->getFirstError('product_id'))->equals("Product is invalid.");
    }

    public function testCreateOrderWithInvalidUser()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => self::$PEPSI_COLA_ID,
                'user_id' => 9999,
                'quantity' => 1
            ]
        ]);

        expect_not($order->validate());
        expect_that($order->getErrors('user_id'));
        expect($order->getFirstError('user_id'))->equals("User is invalid.");
    }

    public function testCreateOrderWithZeroQuantity()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => self::$PEPSI_COLA_ID,
                'user_id' => self::$VALID_USER_ID,
                'quantity' => 0
            ]
        ]);

        expect_not($order->validate());
        expect_that($order->getErrors('quantity'));
        expect($order->getFirstError('quantity'))->equals("Quantity must be at least 1.");
    }

    public function testCreateOrderWithNegativeQuantity()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => self::$PEPSI_COLA_ID,
                'user_id' => self::$VALID_USER_ID,
                'quantity' => -1
            ]
        ]);

        expect_not($order->validate());
        expect_that($order->getErrors('quantity'));
        expect($order->getFirstError('quantity'))->equals("Quantity must be at least 1.");
    }

    public function testQuantityMustBeInteger()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => self::$PEPSI_COLA_ID,
                'user_id' => self::$VALID_USER_ID,
                'quantity' => "abc"
            ]
        ]);

        expect_not($order->validate());
        expect_that($order->getErrors('quantity'));
        expect($order->getFirstError('quantity'))->equals("Quantity must be an integer.");
    }

    public function testCreateOrderWithValidData()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => self::$PEPSI_COLA_ID,
                'user_id' => self::$VALID_USER_ID,
                'quantity' => 2
            ]
        ]);

        $order->validate();
        expect($order->getErrorSummary(true))->equals([]);
        expect($order->validate())->true();

        $order->initialize(new BillingCalculator());
        expect($order->save())->true();

        /** @var \common\models\Order $dbOrder */
        $dbOrder = $this->tester->grabRecord('common\models\Order', [
            'id' => $order->id
        ]);
        expect($dbOrder)->isInstanceOf(Order::class);
    }

    public function testValidDiscountIsAppliedOnPepsiColaOrder()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => self::$PEPSI_COLA_ID,
                'user_id' => self::$VALID_USER_ID,
                'quantity' => 3
            ]
        ]);

        $order->validate();
        expect($order->getErrorSummary(true))->equals([]);
        expect($order->validate())->true();

        $order->initialize(
            new BillingCalculator($this->getDiscountRule($order->product_id))
        );
        expect($order->save())->true();

        /** @var \common\models\Order $dbOrder */
        $dbOrder = $this->tester->grabRecord('common\models\Order', [
            'id' => $order->id
        ]);
        expect($dbOrder)->isInstanceOf(Order::class);
        expect($dbOrder->amount)->equals(3.36);
    }

    public function testNoDiscountIsAppliedOnOtherProductsOrder()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => self::$OTHER_PRODUCT_ID,
                'user_id' => self::$VALID_USER_ID,
                'quantity' => 3
            ]
        ]);

        $order->validate();
        expect($order->getErrorSummary(true))->equals([]);
        expect($order->validate())->true();

        $order->initialize(
            new BillingCalculator($this->getDiscountRule($order->product_id))
        );
        expect($order->save())->true();

        /** @var \common\models\Order $dbOrder */
        $dbOrder = $this->tester->grabRecord('common\models\Order', [
            'id' => $order->id
        ]);
        expect($dbOrder)->isInstanceOf(Order::class);
        expect($dbOrder->amount)->equals(4.5);
    }

    public function testNoDiscountIsAppliedIfPepsiColaOrderIsLessThanThree()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => self::$PEPSI_COLA_ID,
                'user_id' => self::$VALID_USER_ID,
                'quantity' => 2
            ]
        ]);

        $order->validate();
        expect($order->getErrorSummary(true))->equals([]);
        expect($order->validate())->true();

        $order->initialize(
            new BillingCalculator($this->getDiscountRule($order->product_id))
        );
        expect($order->save())->true();

        /** @var \common\models\Order $dbOrder */
        $dbOrder = $this->tester->grabRecord('common\models\Order', [
            'id' => $order->id
        ]);
        expect($dbOrder)->isInstanceOf(Order::class);
        expect($dbOrder->amount)->equals(2.8);
    }

    public function testDiscountIsAppliedIfPepsiColaOrderIsMoreThanThree()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => self::$PEPSI_COLA_ID,
                'user_id' => self::$VALID_USER_ID,
                'quantity' => 5
            ]
        ]);

        $order->validate();
        expect($order->getErrorSummary(true))->equals([]);
        expect($order->validate())->true();

        $order->initialize(
            new BillingCalculator($this->getDiscountRule($order->product_id))
        );
        expect($order->save())->true();

        /** @var \common\models\Order $dbOrder */
        $dbOrder = $this->tester->grabRecord('common\models\Order', [
            'id' => $order->id
        ]);
        expect($dbOrder)->isInstanceOf(Order::class);
        expect($dbOrder->amount)->equals(5.6);
    }

    public function testNoDiscountIsAppliedIfAnotherProductOrderIsMoreThanThree()
    {
        $order = new Order();
        $order->load([
            'Order' => [
                'product_id' => self::$OTHER_PRODUCT_ID,
                'user_id' => self::$VALID_USER_ID,
                'quantity' => 5
            ]
        ]);

        $order->validate();
        expect($order->getErrorSummary(true))->equals([]);
        expect($order->validate())->true();

        $order->initialize(
            new BillingCalculator($this->getDiscountRule($order->product_id))
        );
        expect($order->save())->true();

        /** @var \common\models\Order $dbOrder */
        $dbOrder = $this->tester->grabRecord('common\models\Order', [
            'id' => $order->id
        ]);
        expect($dbOrder)->isInstanceOf(Order::class);
        expect($dbOrder->amount)->equals(7.5);
    }

    /**
     * @param integer $product_id
     * @return \common\models\DiscountRule
     */
    private function getDiscountRule($product_id)
    {
        return $this->tester->grabRecord('common\models\DiscountRule', [
            'product_id' => $product_id
        ]);
    }
}
