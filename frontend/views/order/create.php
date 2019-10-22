<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model api\modules\v1\models\Order */
/* @var $users array */
/* @var $products array */

$this->title = 'Create Order';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'products' => $products
    ]) ?>

</div>
