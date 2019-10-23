<?php

namespace common\tests\unit\models;

use common\fixtures\OrderFixture;
use common\fixtures\ProductFixture;
use common\fixtures\UserFixture;
use common\models\OrderSearch;
use yii\data\ActiveDataProvider;

class OrderSearchTest extends \Codeception\Test\Unit
{

    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;


    public function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'product' => [
                'class' => ProductFixture::class,
                'dataFile' => codecept_data_dir() . 'product.php'
            ],
            'order' => [
                'class' => OrderFixture::class,
                'dataFile' => codecept_data_dir() . 'order.php'
            ]
        ]);
    }

    public function testSearchWithInvalidTimeSpan()
    {
        $orderModel = new OrderSearch();
        $searchResult = $orderModel->search([
            'OrderSearch' => [
                'timeSpan' => 'invalid input',
                'searchTerm' => ''
            ]
        ]);
        expect($searchResult)->isInstanceOf(ActiveDataProvider::class);
        expect($searchResult->totalCount)->equals(0);
        expect_that($orderModel->getErrors('timeSpan'));
        expect($orderModel->getFirstError('timeSpan'))->equals("Time Span is invalid.");
    }

    public function testSearchWithValidAllTimeSpan()
    {
        $orderModel = new OrderSearch();
        $searchResult = $orderModel->search([
            'OrderSearch' => [
                'timeSpan' => 'alltime',
                'searchTerm' => ''
            ]
        ]);
        expect($searchResult)->isInstanceOf(ActiveDataProvider::class);
        expect($searchResult->totalCount)->equals(17);
        expect_not($orderModel->getErrors('timeSpan'));
    }

    public function testSearchOnProductName()
    {
        $orderModel = new OrderSearch();
        $searchResult = $orderModel->search([
            'OrderSearch' => [
                'timeSpan' => 'alltime',
                'searchTerm' => 'Pepsi Cola'
            ]
        ]);
        expect($searchResult)->isInstanceOf(ActiveDataProvider::class);
        expect($searchResult->totalCount)->equals(3);
        expect_not($orderModel->getErrors('timeSpan'));
    }

    public function testSearchOnUserName()
    {
        $orderModel = new OrderSearch();
        $searchResult = $orderModel->search([
            'OrderSearch' => [
                'timeSpan' => 'alltime',
                'searchTerm' => 'innamhunzai'
            ]
        ]);
        expect($searchResult)->isInstanceOf(ActiveDataProvider::class);
        expect($searchResult->totalCount)->equals(4);
        expect_not($orderModel->getErrors('timeSpan'));
    }

    public function testFilterTodayOrders()
    {
        //todo: implementation of test case
    }

    public function testFilterLastWeekOrders()
    {
        //todo: implementation of test case
    }

    public function testFilterTodayOrdersOfSpecificUser()
    {
        //todo: implementation of test case
    }

    public function testFilterTodayOrdersOfSpecificProduct()
    {
        //todo: implementation of test case
    }
}
