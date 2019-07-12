<?php

use app\modules\user\Module;
use app\modules\user\components\UsersListWidget;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = 'REQUESTS';
$this->params['breadcrumbs'][] = $this->title;


?>
    <h1>Заявки в друзья:</h1>
<?= UsersListWidget::widget([
    'users' => $users,
    'button' =>  [
        'text' => 'Принять',
        'url' => 'default/add-friend',
        'class' => 'btn btn-success',
        'redirect' => '/user/profile/request'

    ],
    'photo_size' => 1,
]) ?>