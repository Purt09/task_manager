<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\task\Module;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Task */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>

    <? $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->widget(DatePicker::className(), [
        'name' => 'anniversary',
        'value' => '08/10/2004',
        'readonly' => true,

        'removeButton' => false,
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyymmdd'
        ]
    ]) ?>

<!--    --><?//= $form->field($model, 'created_at')->widget(DatePicker::className(), [
//        'name' => 'anniversary',
//        'value' => '08/10/2004',
//        'readonly' => true,
//
//        'removeButton' => false,
//        'pluginOptions' => [
//            'autoclose'=>true,
//            'format' => 'yyyymmdd'
//        ]
//    ]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton(Module::t('module', 'SAVE'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
