<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model api\common\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'class' => 'inline'
    ]); ?>

    <div class="row">
        <div class="col-sm-12 col-md-3">
            <?= $form->field($model, 'timeSpan')
                ->label(false)
                ->dropDownList(['alltime' => 'All Time', 'week'=>'Last Week', 'today' => 'Today'])
            ?>
        </div>
        <div class="col-sm-12 col-md-3">
            <?= $form->field($model, 'searchTerm')
                ->label(false)
                ->textInput(['maxlength' => 255, 'placeholder'=>'Search Term'])
            ?>
        </div>
        <div class="col-sm-12 col-md-3">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success pull-right']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
