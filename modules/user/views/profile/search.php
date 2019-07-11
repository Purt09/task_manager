<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\user\Module;
use app\modules\user\components\UsersWidget;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = 'SEARCH';
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="user-search">

    <hr>
    <div class="user-form">

        <?php $form = ActiveForm::begin(['id' => 'profile-search']); ?>
        <table>
            <tr>
                <td>
                    <?= $form->field($model, 'query')->textInput(['class' => 'input'])->label('') ?>
                </td>
                <td>

                    <div class="form-group ml-3">
                        <?= Html::submitButton(Module::t('module', 'SEARCH_USER'), ['class' => 'btn btn-primary']) ?>
                    </div>
                </td>
            </tr>
        </table>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<h1>Результаты поиска:</h1>
<?= UsersWidget::widget([
    'users' => $users,
    'button' => true,
    'photo_size' => 1,
]) ?>

