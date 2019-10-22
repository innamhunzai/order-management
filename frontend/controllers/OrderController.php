<?php

namespace frontend\controllers;

use common\models\BillingCalculator;
use common\models\DiscountRule;
use common\models\Product;
use common\models\User;
use Yii;
use common\models\Order;
use common\models\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();
        //Validate user Inputs
        if ($model->load(Yii::$app->request->post())) {
            //Initialize and attach Billing Calculator with model
            $billingCalculator = new BillingCalculator(
                DiscountRule::findDiscountRule($model->product_id, $model->quantity)
            );
            $model->initialize($billingCalculator);
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'users' => ArrayHelper::map(User::find()->asArray()->all(), 'id', 'username'),
            'products' => ArrayHelper::map(Product::find()->asArray()->all(), 'id', 'name'),
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            //Initialize and attach Billing Calculator with model
            $billingCalculator = new BillingCalculator(
                DiscountRule::findDiscountRule($model->product_id, $model->quantity)
            );
            $model->initialize($billingCalculator);
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $users = ArrayHelper::map(User::find()->asArray()->all(), 'id', 'username');
        return $this->render('update', [
            'model' => $model,
            'users' => $users,
            'products' => ArrayHelper::map(Product::find()->asArray()->all(), 'id', 'name'),
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable in case delete failed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
